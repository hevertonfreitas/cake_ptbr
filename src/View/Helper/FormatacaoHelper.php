<?php
/**
 * Helper para formatação de dados no padrão brasileiro
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\View\Helper;

use Cake\I18n\Time;
use Cake\View\Helper;

/**
 * Formatação Helper
 *
 * @property Helper\TimeHelper $Time
 * @property Helper\NumberHelper $Number
 * @link http://wiki.github.com/jrbasso/cake_ptbr/helper-formatao
 */
class FormatacaoHelper extends Helper
{

    /**
     * Helpers auxiliares
     *
     * @var array
     * @access public
     */
    public $helpers = ['Time', 'Number'];

    /**
     * Formata a data
     *
     * @param int $data Data em timestamp ou null para atual
     * @param array $opcoes É possível definir o valor de 'invalid' e 'userOffset' que serão usados pelo helper Time
     * @return string Data no formato dd/mm/aaaa
     * @access public
     */
    public function data($data = null, $opcoes = [])
    {
        $padrao = [
            'invalid' => '31/12/1969',
            'userOffset' => null,
        ];
        $config = array_merge($padrao, $opcoes);

        $data = $this->_ajustaDataHora($data) ? $this->_ajustaDataHora($data) : $data;

        return $this->Time->format($data, 'dd/MM/YYYY', $config['invalid'], $config['userOffset']);
    }

    /**
     * Mostrar a data completa
     *
     * @param int|\Cake\I18n\Time $dataHora Data e hora em timestamp ou null para atual
     * @return string Descrição da data no estilo "Sexta-feira", 01 de Janeiro de 2010, 00:00:00"
     * @access public
     */
    public function dataCompleta($dataHora = null)
    {
        $_diasDaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        $_meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        $dataHora = $this->_ajustaDataHora($dataHora);
        $dataHora = is_object($dataHora) ? $dataHora : Time::createFromTimestamp($dataHora);

        return sprintf('%s, %02d de %s de %04d, %s', $_diasDaSemana[$dataHora->dayOfWeek], $dataHora->day, $_meses[$dataHora->month - 1], $dataHora->year, $dataHora->format('H:i:s'));
    }

    /**
     * Mostrar uma data em tempo
     *
     * @param int $dataHora Data e hora em timestamp, dd/mm/YYYY ou null para atual
     * @param string $limite null, caso não haja expiração ou então, forneça um tempo usando o formato inglês para strtotime: Ex: 1 year
     * @return string Descrição da data em tempo ex.: a 1 minuto, a 1 semana
     * @access public
     */
    public function tempo($dataHora = null, $limite = '30 days')
    {
        if (!$dataHora) {
            $dataHora = time();
        }

        if (strpos($dataHora, '/') !== false) {
            $_dataHora = str_replace('/', '-', $dataHora);
            $_dataHora = date('ymdHi', strtotime($_dataHora));
        } elseif (is_string($dataHora)) {
            $_dataHora = date('ymdHi', strtotime($dataHora));
        } else {
            $_dataHora = date('ymdHi', $dataHora);
        }

        if ($limite !== null) {
            if ($_dataHora > date('ymdHi', strtotime('+ ' . $limite))) {
                return $this->dataHora($dataHora);
            }
        }

        $_dataHora = date('ymdHi') - $_dataHora;
        if ($_dataHora > 88697640 && $_dataHora < 100000000) {
            $_dataHora -= 88697640;
        }

        switch ($_dataHora) {
            case 0:
                return 'menos de 1 minuto';
            case ($_dataHora < 99):
                if ($_dataHora === 1) {
                    return '1 minuto';
                } elseif ($_dataHora > 59) {
                    return ($_dataHora - 40) . ' minutos';
                }

                return $_dataHora . ' minutos';
            case ($_dataHora > 99 && $_dataHora < 2359):
                $flr = floor($_dataHora * 0.01);

                return $flr == 1 ? '1 hora' : $flr . ' horas';

            case ($_dataHora > 2359 && $_dataHora < 310000):
                $flr = floor($_dataHora * 0.0001);

                return $flr == 1 ? '1 dia' : $flr . ' dias';

            case ($_dataHora > 310001 && $_dataHora < 12320000):
                $flr = floor($_dataHora * 0.000001);

                return $flr == 1 ? '1 mes' : $flr . ' meses';

            case ($_dataHora > 100000000):
            default:
                $flr = floor($_dataHora * 0.00000001);

                return $flr == 1 ? '1 ano' : $flr . ' anos';
        }
    }

    /**
     * Formata a data e hora
     *
     * @param int $dataHora Data e hora em timestamp ou null para atual
     * @param bool $segundos Mostrar os segundos
     * @param array $opcoes É possível definir o valor de 'invalid' e 'userOffset' que serão usados pelo helper Time
     * @return string Data no formato dd/mm/aaaa hh:mm:ss
     * @access public
     */
    public function dataHora($dataHora = null, $segundos = true, $opcoes = [])
    {
        $padrao = [
            'invalid' => '31/12/1969',
            'userOffset' => null,
        ];
        $config = array_merge($padrao, $opcoes);

        $dataHora = $this->_ajustaDataHora($dataHora);
        if ($segundos) {
            return $this->Time->format($dataHora, 'dd/MM/YYYY HH:mm:ss', $config['invalid'], $config['userOffset']);
        }

        return $this->Time->format($dataHora, 'dd/MM/YYYY HH:mm', $config['invalid'], $config['userOffset']);
    }

    /**
     * Valor formatado com símbolo de %
     *
     * @param float $numero Número
     * @param int $casasDecimais Número de casas decimais
     * @return string Número formatado com %
     * @access public
     */
    public function porcentagem($numero, $casasDecimais = 2)
    {
        return $this->precisao($numero, $casasDecimais) . '%';
    }

    /**
     * Número float com ponto ao invés de vírgula
     *
     * @param float $numero Número
     * @param int $casasDecimais Número de casas decimais
     * @return string Número formatado
     * @access public
     */
    public function precisao($numero, $casasDecimais = 3)
    {
        return number_format($numero, $casasDecimais, ',', '.');
    }

    /**
     * Formata um valor para reais
     *
     * @param float $valor Valor
     * @param array $opcoes Mesmas opções de Number::currency()
     * @return string Valor formatado em reais
     * @access public
     */
    public function moeda($valor, $opcoes = [])
    {
        $padrao = [
            'after' => '',
            'zero' => 'R$ 0,00',
            'places' => 2,
            'negative' => '-',
            'locale' => 'pt_BR',
            'pattern' => '#.###,00',
            'escape' => true,
        ];
        $config = array_merge($padrao, $opcoes);

        return $this->Number->currency($valor, 'BRL', $config);
    }

    /**
     * Valor por extenso em reais
     *
     * @param float $numero Valore numérico
     * @return string Valor em reais por extenso
     * @access public
     * @link http://forum.imasters.uol.com.br/index.php?showtopic=125375
     */
    public function moedaPorExtenso($numero)
    {
        $singular = ['centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão'];
        $plural = ['centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões'];

        $c = ['', 'cem', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];
        $d = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
        $d10 = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezesete', 'dezoito', 'dezenove'];
        $u = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];

        $z = 0;
        $rt = '';

        $valor = number_format($numero, 2, '.', '.');
        $inteiro = explode('.', $valor);
        $tamInteiro = count($inteiro);

        // Normalizandos os valores para ficarem com 3 digitos
        $inteiro[0] = sprintf('%03d', $inteiro[0]);
        $inteiro[$tamInteiro - 1] = sprintf('%03d', $inteiro[$tamInteiro - 1]);

        $fim = $tamInteiro - 1;
        if ($inteiro[$tamInteiro - 1] <= 0) {
            $fim--;
        }
        foreach ($inteiro as $i => $valor) {
            $rc = $c[$valor[0]];
            if ($valor > 100 && $valor < 200) {
                $rc = 'cento';
            }
            $rd = '';
            if ($valor[1] > 1) {
                $rd = $d[$valor[1]];
            }
            $ru = '';
            if ($valor > 0) {
                if ($valor[1] == 1) {
                    $ru = $d10[$valor[2]];
                } else {
                    $ru = $u[$valor[2]];
                }
            }

            $r = $rc;
            if ($rc && ($rd || $ru)) {
                $r .= ' e ';
            }
            $r .= $rd;
            if ($rd && $ru) {
                $r .= ' e ';
            }
            $r .= $ru;
            $t = $tamInteiro - 1 - $i;
            if (!empty($r)) {
                $r .= ' ';
                if ($valor > 1) {
                    $r .= $plural[$t];
                } else {
                    $r .= $singular[$t];
                }
            }
            if ($valor == '000') {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }
            if ($t == 1 && $z > 0 && $inteiro[0] > 0) {
                if ($z > 1) {
                    $r .= ' de ';
                }
                $r .= $plural[$t];
            }
            if (!empty($r)) {
                if ($i > 0 && $i < $fim && $inteiro[0] > 0 && $z < 1) {
                    if ($i < $fim) {
                        $rt .= ', ';
                    } else {
                        $rt .= ' e ';
                    }
                } elseif ($t == 0 && $inteiro[0] > 0) {
                    $rt .= ' e ';
                } else {
                    $rt .= ' ';
                }
                $rt .= $r;
            }
        }

        if (empty($rt)) {
            return 'zero';
        }

        return trim(str_replace('  ', ' ', $rt));
    }

    /**
     * Se a data for nula, usa data atual
     *
     * @param mixed $data A data a ser ajustada
     * @return int|\Cake\I18n\Time Se null, retorna a data/hora atual
     * @access protected
     */
    protected function _ajustaDataHora($data)
    {
        if (is_null($data)) {
            return Time::now();
        }
        if (is_integer($data) || ctype_digit($data)) {
            return Time::createFromTimestamp($data);
        }

        $tsLong = strtotime((string)$data);

        return $tsLong ? $tsLong : $data;
    }
}
