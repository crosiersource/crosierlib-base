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

    public static function validaCPF($cpf = null): ?bool
    {
        if (empty($cpf)) {
            return false;
        }

        $cpf = preg_replace('/[^0-9]/', "", $cpf);
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
            $digito = (int)(((10 * $digito) % 11) % 10);
            if ((int)$cpf{$c} !== $digito) {
                return false;
            }
        }

        return true;

    }

}