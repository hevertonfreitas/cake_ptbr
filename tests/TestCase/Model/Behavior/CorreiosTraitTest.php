<?php

namespace CakePtbr\Test\Traits;

use CakePtbr\Model\Behavior\CorreiosTrait;
use CakePtbr\Test\TestCase\Model\Behavior\CorreiosTraitImpl;
use Cake\TestSuite\TestCase;

/**
 * Class CorreiosTraitTest
 *
 * @property CorreiosTraitImpl $Correios
 * @package CakePtbr\Test\Traits
 */
class CorreiosTraitTest extends TestCase
{
    /**
     * @var CorreiosTraitImpl $Correios
     */
    private $Correios;

    public function setUp()
    {
        parent::setUp();
        $this->Correios = new CorreiosTraitImpl();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Correios);
    }

    public function testValorFrete()
    {
        $dados = [
            'servico' => CorreiosTrait::$CORREIOS_SEDEX,
            'cepOrigem' => '88037100',
            'cepDestino' => '86020121',
            'peso' => 1.00,
            'maoPropria' => true,
            'valorDeclarado' => 30,
            'avisoRecebimento' => false,
            'formato' => CorreiosTrait::$ENCOMENDA_CAIXA,
            'comprimento' => 20.00,
            'altura' => 20.00,
            'largura' => 30.00,
        ];

        $tamanhoInvalido = ['largura' => 10];
        $cepInvalido = ['cepOrigem' => '1000-00'];
        $pesoInvalido = ['peso' => 40];
        $pesoNegativo = ['peso' => -12];

        $this->assertEquals(CorreiosTrait::$ERRO_CORREIOS_PARAMETROS_INVALIDOS, $this->Correios->valorFrete(array_merge($dados, $tamanhoInvalido)));
        $this->assertEquals(CorreiosTrait::$ERRO_CORREIOS_PARAMETROS_INVALIDOS, $this->Correios->valorFrete(array_merge($dados, $cepInvalido)));
        $this->assertEquals(CorreiosTrait::$ERRO_CORREIOS_EXCESSO_PESO, $this->Correios->valorFrete(array_merge($dados, $pesoInvalido)));
        $this->assertEquals(CorreiosTrait::$ERRO_CORREIOS_PARAMETROS_INVALIDOS, $this->Correios->valorFrete(array_merge($dados, $pesoNegativo)));

        $correios = $this->Correios->valorFrete($dados);

        $this->assertEquals([
            'valorMaoPropria' => 7.5,
            'valorTarifaValorDeclarado' => 0.19,
            'valorFrete' => 58.40,
            'valorTotal' => 66.09,
            'entregaDomiciliar' => true,
            'entregaSabado' => true,
        ], $correios);
    }

    public function testEndereco()
    {
        $retorno = $this->Correios->endereco('45810000');
        $this->assertEquals('Porto Seguro', $retorno['cidade']);
        $this->assertEquals('BA', $retorno['estado']);

        $retorno = $this->Correios->endereco('01311-922');
        $this->assertContains('Avenida Paulista', $retorno['logradouro']);
        $this->assertEquals('Bela Vista', $retorno['bairro']);
        $this->assertEquals('São Paulo', $retorno['cidade']);
        $this->assertEquals('SP', $retorno['estado']);

        $retorno = $this->Correios->endereco('00000-121');
        $this->assertEquals(CorreiosTrait::$ERRO_POSTMON_CEP_INVALIDO, $retorno);
    }
}
