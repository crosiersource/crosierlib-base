<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CrosierEntityIdAPIClient.
 * Classe padrão para interagir com classes BaseAPIEntityIdController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 * @author Carlos Eduardo Pauluk
 */
abstract class CrosierEntityIdAPIClient extends CrosierAPIClient
{

    abstract public static function getBaseUri(): string;

    public static function getFullBaseUri(): string {
        return $_SERVER['CROSIERCORE_URL'] . CrosierEntityIdAPIClient::getBaseUri();
    }

    /**
     * @param int $id
     * @return string
     */
    public function getById(int $id): string
    {
        return $this->post($this::getBaseUri() . '/findById/' . $id);
    }

    /**
     * @param array $filters
     * @return string
     * @throws GuzzleException
     * @throws ViewException
     */
    public function findByFilters(array $filters): string
    {
        return $this->post($this::getBaseUri() . '/findByFilters/', $filters);
    }

}