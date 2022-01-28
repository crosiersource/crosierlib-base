<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 03/04/19
 * Time: 10:38
 */

namespace CrosierSource\CrosierLibBaseBundle\Utils\StringUtils;

/**
 * Class ValidaCPFCNPJ
 * @package CrosierSource\CrosierLibBaseBundle\Utils\StringUtils
 * @author Carlos Eduardo Pauluk
 */
class ValidaCPFCNPJ
{

    /**
     * @param $documento
     * @return bool|null
     */
    public static function valida($documento): ?bool
    {
        $documento = preg_replace("/[\D]/", '', $documento);
        if (strlen($documento) === 11) {
            return self::validaCPF($documento);
        } else if (strlen($documento) === 14) {
            return self::validaCNPJ($documento);
        } else {
            return false;
        }
    }

    /**
     * @param null $cpf
     * @return bool|null
     */
    public static function validaCPF($cpf = null): ?bool
    {
        if (empty($cpf)) {
            return false;
        }

        $cpf = preg_replace("/[\D]/", '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if ($cpf === '00000000000' ||
            $cpf === '11111111111' ||
            $cpf === '22222222222' ||
            $cpf === '33333333333' ||
            $cpf === '44444444444' ||
            $cpf === '55555555555' ||
            $cpf === '66666666666' ||
            $cpf === '77777777777' ||
            $cpf === '88888888888' ||
            $cpf === '99999999999') {
            return false;
        }

        for ($p = 9; $p < 11; $p++) {

            for ($digito = 0, $c = 0; $c < $p; $c++) {
                $digito += $cpf[$c] * (($p + 1) - $c);
            }
            $digito = (((10 * $digito) % 11) % 10);
            if ((int)$cpf[$c] !== $digito) {
                return false;
            }
        }
        return true;
    }


    /**
     * @param null $cnpj
     * @return bool|null
     */
    public static function validaCNPJ($cnpj = null): ?bool
    {
        // Verifica se um número foi informado
        if (empty($cnpj)) {
            return false;
        }

        // Elimina possivel mascara
        $cnpj = preg_replace("/[\D]/", '', $cnpj);
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        // else
        if ($cnpj === '00000000000000' ||
            $cnpj === '11111111111111' ||
            $cnpj === '22222222222222' ||
            $cnpj === '33333333333333' ||
            $cnpj === '44444444444444' ||
            $cnpj === '55555555555555' ||
            $cnpj === '66666666666666' ||
            $cnpj === '77777777777777' ||
            $cnpj === '88888888888888' ||
            $cnpj === '99999999999999') {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        }
        // else

        $j = 5;
        $k = 6;
        $soma1 = 0;
        $soma2 = 0;

        for ($i = 0; $i < 13; $i++) {
            $j = $j === 1 ? 9 : $j;
            $k = $k === 1 ? 9 : $k;
            $soma2 += ($cnpj[$i] * $k);
            if ($i < 12) {
                $soma1 += ($cnpj[$i] * $j);
            }
            $k--;
            $j--;
        }

        $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
        $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

        return (((int)$cnpj[12] === (int)$digito1) and ((int)$cnpj[13] === (int)$digito2));
    }

    /**
     * Método para gerar CNPJ válido, com máscara ou não
     * @param int $mascara
     * @return string
     * @example cnpjRandom(0)
     *          para retornar CNPJ sem máscar
     */
    public static function cnpjRandom($mascara = "1")
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;
        $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 + $n1 * 5;
        $d1 = 11 - (self::mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 + $n2 * 5 + $n1 * 6;
        $d2 = 11 - (self::mod($d2, 11));
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($mascara == 1) {
            $retorno = '' . $n1 . $n2 . "." . $n3 . $n4 . $n5 . "." . $n6 . $n7 . $n8 . "/" . $n9 . $n10 . $n11 . $n12 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $n10 . $n11 . $n12 . $d1 . $d2;
        }
        return $retorno;
    }

    /**
     * Método para gerar CPF válido, com máscara ou não
     * @param int $mascara
     * @return string
     * @example cpfRandom(0)
     *          para retornar CPF sem máscar
     */
    public static function cpfRandom($mascara = "1")
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - (self::mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - (self::mod($d2, 11));
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($mascara == 1) {
            $retorno = '' . $n1 . $n2 . $n3 . "." . $n4 . $n5 . $n6 . "." . $n7 . $n8 . $n9 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
        }
        return $retorno;
    }

    /**
     * @param type $dividendo
     * @param type $divisor
     * @return type
     */
    private static function mod($dividendo, $divisor)
    {
        return round($dividendo - (floor($dividendo / $divisor) * $divisor));
    }

}
