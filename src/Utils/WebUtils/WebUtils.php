<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\WebUtils;

/**
 * Class FTPUtils
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\FTPUtils
 * @author Carlos Eduardo Pauluk
 */
class WebUtils
{

    /**
     * Apaga todos os arquivos e subdiretórios de um diretório no ftp.
     *
     * @param string $url
     * @return bool
     */
    public static function urlNot404(string $url): bool
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        return $httpCode != 404;
    }

}