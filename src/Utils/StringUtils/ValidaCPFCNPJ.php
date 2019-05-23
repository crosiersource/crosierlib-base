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
        }
        if (strlen($documento) === 14) {
            return self::validaCNPJ($documento);
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
                $digito += $cpf{$c} * (($p + 1) - $c);
            }
            $digito = (((10 * $digito) % 11) % 10);
            if ((int)$cpf{$c} !== $digito) {
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
        $soma1 = '';
        $soma2 = '';

        for ($i = 0; $i < 13; $i++) {

            $j = $j === 1 ? 9 : $j;
            $k = $k === 1 ? 9 : $k;

            $soma2 += ($cnpj{$i} * $k);

            if ($i < 12) {
                $soma1 += ($cnpj{$i} * $j);
            }

            $k--;
            $j--;

        }

        $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
        $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

        return (($cnpj{12} === $digito1) and ($cnpj{13} === $digito2));


    }

}