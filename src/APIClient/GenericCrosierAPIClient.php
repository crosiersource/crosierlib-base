<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


/**
 * Cliente para consumir os serviços REST de qualquer API.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class GenericCrosierAPIClient extends CrosierEntityIdAPIClient
{
    /** @var string */
    private static $baseUri;

    /**
     * @return string
     */
    public static function getBaseUri(): string
    {
        return self::$baseUri;
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): GenericCrosierAPIClient
    {
        self::$baseUri = $baseUri;
        return self;
    }


}