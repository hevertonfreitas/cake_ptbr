<?php
/**
 * Fixture de Produto
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Produto Fixture
 *
 */
class ProdutosFixture extends TestFixture
{

    /**
     * Nome do fixture
     *
     * @var string
     * @access public
     */
    public $name = 'Produto';

    /**
     * Campos da tabela
     *
     * @var array
     * @access public
     */
    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
        'nome' => ['type' => 'string', 'null' => false, 'default' => null],
        'valor' => ['type' => 'float', 'null' => false, 'default' => null],
        '_constraints' => [ 'primary' => ['type' => 'primary', 'columns' => ['id']] ],
    ];

    /**
     * Registros
     *
     * @var array
     * @access public
     */
    public $records = [
        [
            'id' => 1,
            'nome' => 'Produto 1',
            'valor' => 1.99,
        ],
        [
            'id' => 2,
            'nome' => 'Produto 2',
            'valor' => 1000.20,
        ],
        [
            'id' => 3,
            'nome' => 'Produto 3',
            'valor' => 1999000.00,
        ],
    ];
}
