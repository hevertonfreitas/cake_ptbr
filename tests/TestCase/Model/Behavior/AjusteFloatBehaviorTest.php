<?php
/**
 * Teste do Behavior AjusteFloat
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\Model\Behavior;

use CakePtbr\Model;
use Cake\Database\Expression\Comparison;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

/**
 * AjusteFloat Test Case
 *
 */
class AjusteFloatBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     * @access public
     */
    public $fixtures = [
        'plugin.CakePtbr.Produtos',
    ];

    /**
     * Produto
     *
     * @var Table
     * @access public
     */
    public $Produtos = null;

    /**
     * startTest
     *
     * @retun void
     * @access public
     */
    public function setUp()
    {
        $tableLocator = new TableLocator();
        $this->Produtos = $tableLocator->get('CakePtbr.Produtos');
        $this->Produtos->addBehavior("CakePtbr.AjusteFloat");
    }

    /**
     * testBeforeFind
     *
     * @return void
     * @access public
     */
    public function testBeforeFind()
    {
        $condicoes = [
            'nome' => '1.000,00',
            'valor' => '1.500,03',
        ];

        $consulta = $this->Produtos->find('all')->where(
            $condicoes
        );
        $consulta->all();

        $condicoesTratadas = [];
        $todosCampos = [];

        /**
         * @var Query $consulta
         */
        $consulta->clause("where")->traverse(function ($comparison) use (&$condicoesTratadas, &$todosCampos) {
            /**
             * @var Comparison $comparison
             */
            if (isset($comparison)) {
                if ($this->Produtos->getSchema()->getColumnType($comparison->getField()) === "float") {
                    $condicoesTratadas[$comparison->getField()] = $comparison->getValue();
                }
                $todosCampos[$comparison->getField()] = $comparison->getValue();
            }
        });

        $this->assertEquals("1.000,00", $todosCampos["nome"]);
        $this->assertEquals("1500.03", $condicoesTratadas["valor"]);
    }

    /**
     * testSave
     *
     * @retun void
     * @access public
     */
    public function testSave()
    {
        $data = [
            'nome' => 'Produto 4',
            'valor' => '5.000,00',
        ];
        $entidade = $this->Produtos->newEntity($data);
        $resultado = $this->Produtos->save($entidade);

        $this->assertInstanceOf('Cake\ORM\Entity', $resultado);
        $this->assertEquals('5000.00', $entidade->get("valor"));
        $this->assertEquals('5000.00', $resultado->get("valor"));
    }
}
