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

    const PATTERN_DATA = "@(?<data>\\d{2}/\\d{2}/\\d{4}|\\d{2}/\\d{2}/\\d{2}|\\d{2}/\\d{2}|\\d{4}\-\\d{2}\-\\d{2}){1}@";

    const PATTERN_MONEY =
        "@" .
        "(?<SINAL_I>\\+|\\-)?(?<money>(?<INTEIROS>\\d{1,3}|\\d{1,3}(?:\\.\\d{3})+){1},{1}(?<CENTAVOS>\\d{2}){1})(?:\\s)*(?<SINAL_F>\\+|\\-|C|D)?" .
        "@";

    /**
     * @param $formattedFloat
     * @param bool $clear
     * @return false|float|int|mixed
     */
    public static function parseFloat($formattedFloat, $clear = false)
    {
        $formattedFloat = str_replace(' ', '', $formattedFloat);
        $negativo = null;
        if ($formattedFloat[strlen($formattedFloat) - 1] === 'D') {
            $negativo = true;
        }
        // Se pedir pra remover caracteres estranhos...
        if ($clear) {
            $formattedFloat = preg_replace("@[^0-9\\-\\.\\,]@", "", $formattedFloat);
        }
        $fmt = new NumberFormatter('pt-BR', NumberFormatter::DECIMAL);
        $float = $fmt->parse($formattedFloat);
        $float = $negativo ? -(abs($float)) : $float;
        return $float;
    }

    /**
     * @param $valor
     * @param $mascara
     * @return string
     * @throws \Exception
     */
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


    public static function shortRandom(): string
    {
        $data = openssl_random_pseudo_bytes(4);
        return vsprintf('%s', bin2hex($data));
    }

    /**
     * @param null|string $str
     * @return null|string
     */
    public static function removerAcentos(?string $str): ?string
    {
        return
            preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1',
                htmlentities($str, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * @param null|string $value
     * @return null|string
     */
    public static function mascararCnpjCpf(?string $value = null, bool $permitirLetrasEInterrogacao = false): ?string
    {
        if ($permitirLetrasEInterrogacao) {
            $cnpj_cpf =
                preg_replace('/[^\w\?]/', '', $value);
        } else {
            $cnpj_cpf =
                preg_replace("/\D/", '', $value);
        }

        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(.{3})(.{3})(.{3})(.{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } elseif (strlen($cnpj_cpf) === 14) {
            return preg_replace("/(.{2})(.{3})(.{3})(.{4})(.{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
        }
        return $value;
    }

    /**
     * @param $number
     * @param int $pad_length
     * @param string|null $pad_string
     * @return string
     */
    public static function strpad($number, int $pad_length, ?string $pad_string = '0'): string
    {
        return str_pad($number, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    /**
     * @param string $str
     * @return bool
     */
    public static function isJson(string $str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public static function strposRegex(string $subject, string $pattern)
    {
        preg_match('/' . $pattern . '/', $subject, $matches, PREG_OFFSET_CAPTURE);
        return $matches[0][1] ?? null;
    }


    public static function formataTelefone(string $numero)
    {
        $numero = trim($numero);
        if (!$numero) {
            return '';
        }
        $numero = preg_replace("/[^0-9]/", "", $numero);
        $arrNumeros = str_split($numero);
        $numero = (strlen($numero) == 11) ?
            vsprintf('(%d%d) %d%d%d%d%d-%d%d%d%d', $arrNumeros) :
            vsprintf('(%d%d) %d%d%d%d-%d%d%d%d', $arrNumeros);
        return $numero;
    }


    public static function obfuscateEmail(string $email): string
    {
        try {
            $user = substr($email, 0, strpos($email, '@'));
            $ini = $email[0] . str_repeat('*', strlen($user) - 2) . $user[strlen($user) - 1];
            $domain = substr($email, strpos($email, '@') + 1);
            $domainOnly = substr($domain, 0, strrpos($domain, '.'));
            $d = $domainOnly[0] . str_repeat('*', strlen($domainOnly) - 2) . $domainOnly[strlen($domainOnly) - 1];
            $last = substr($email, strrpos($email, '.'));
            $obsf = $ini . '@' . $d . $last;
        } catch (\Exception $e) {
            $obsf = $email;
        }
        return $obsf;
    }


    public static function removeNonAlfanumerics(?string $str): string
    {
        return preg_replace('/[\W]/', '', $str ?? '');
    }


    /**
     * Descriptografa um valor vindo criptografado pelo crypto-js.
     */
    public static function cryptoJsDecrypt(string $passphrase, string $jsonString)
    {
        $jsondata = json_decode($jsonString, true);
        $salt = hex2bin($jsondata["theS"]);
        $ct = base64_decode($jsondata["theCt"]);
        $iv = hex2bin($jsondata["theIv"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    /**
     * Criptografa um valor para ser utilizado com crypto-js.
     *
     * @param mixed $passphrase
     * @param mixed $value
     * @return string
     */
    public static function cryptoJsEncrypt(string $passphrase, string $value): string
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("theCt" => base64_encode($encrypted_data), "theIv" => bin2hex($iv), "theS" => bin2hex($salt));
        return json_encode($data);
    }


    /**
     * @param string|null $bool
     * @return bool|null
     */
    public static function parseBoolStr(?string $bool): ?bool
    {
        return filter_var($bool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }


    public static function ucFirstNomes($name): string
    {
        $smallWords = ['da', 'de', 'do', 'das', 'dos'];

        $nameParts = explode(' ', $name);
        $formattedName = [];

        foreach ($nameParts as $part) {
            $formattedPart = strtolower($part);

            if (!in_array($formattedPart, $smallWords)) {
                $formattedPart = ucfirst($formattedPart);
            }

            $formattedName[] = $formattedPart;
        }

        return implode(' ', $formattedName);
    }

}

