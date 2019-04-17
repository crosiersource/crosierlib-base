<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\StringUtils;

use NumberFormatter;
use Transliterator;

/**
 * Class StringUtils
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\StringUtils
 * @author Carlos Eduardo Pauluk
 */
class StringUtils
{

    const PATTERN_DATA = "@(?<data>\\d{2}/\\d{2}/\\d{4}|\\d{2}/\\d{2}/\\d{2}|\\d{2}/\\d{2}){1}@";

    const PATTERN_MONEY =
        "@" .
        "(?<SINAL_I>\\+|\\-)?(?<Q>\\D*\\$?\\D*)(?<money>(?<INTEIROS>\\d{1,3}|\\d{1,3}(?:\\.\\d{3})+){1},{1}(?<CENTAVOS>\\d{2}){1})(?:\\s)*(?<SINAL_F>\\+|\\-)?" .
        "@";

    public static function parseFloat($formattedFloat, $clear = false)
    {
        $formattedFloat = str_replace(' ', '', $formattedFloat);
        $negativo = null;
        if ($formattedFloat[strlen($formattedFloat) - 1] === 'D') {
            $negativo = true;
        }
        // Se pedir pra remover caracteres estranhos...
        if ($clear) {
            $formattedFloat = preg_replace("@[^0-9\\.\\,]@", "", $formattedFloat);
        }
        $fmt = new NumberFormatter('pt-BR', NumberFormatter::DECIMAL);
        $float = $fmt->parse($formattedFloat);
        $float = $negativo ? -(abs($float)) : $float;
        return $float;
    }

    public static function mascarar($valor, $mascara)
    {
        $subs = explode(".", $mascara); // verificar como fazer o split tendo separadores diferentes (como é o caso do CNPJ)

        if (($subs == null) || (count($subs) == 0)) {
            throw new \Exception("Máscara inválida (Valor: '" . $valor . "', Máscara: '" . $mascara . "')");
        }
        $tam = 0;
        $tamanhoPermitido = false;
        foreach ($subs as $sub) {
            $tam += strlen($sub);
            if (strlen($valor) == $tam) {
                $tamanhoPermitido = true;
                break;
            }
        }
        if (!$tamanhoPermitido) {
            throw new \Exception("Qtde de caracteres não permitida (Valor: '" . $valor . "', Máscara: '" . $mascara . "')");
        }
        if (strlen($subs[0]) == strlen($valor)) {
            return $valor;
        } else {
            $sb = "";
            foreach ($subs as $sub) {
                $tamA = strlen($sub);
                $sb .= substr($valor, 0, $tamA);
                $valor = substr($valor, $tamA);

                if (strlen($valor) < 1) {
                    break;
                } else {
                    $sb .= '.'; // FIXME: aqui tem que adicionar o caracter da máscara
                }
            }

            return $sb;
        }
    }

    /**
     * Troca todos os caracteres específicos, e troca qualquer coisa que não seja letra ou números por underscore.
     * @param $str
     * @return mixed
     */
    public static function strToFilenameStr($str)
    {
        $str = trim($str);
        $str = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD)->transliterate($str);
        $str = preg_replace('/[^a-zA-Z0-9]/', '_', $str);
        return $str;
    }

    /**
     * Retorna um GUID v4
     * @param $data
     * @return string
     */
    public static function guidv4($data = null)
    {
        if (!$data) {
            $data = openssl_random_pseudo_bytes(16);
        }
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


}

