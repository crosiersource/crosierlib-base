<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Security\Core\Security;

/**
 * Class CrosierAPIClient.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 * @author Carlos Eduardo Pauluk
 */
abstract class CrosierAPIClient
{

    protected Security $security;

    protected LoggerInterface $logger;

    protected string $baseURI;


    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
    }

    /**
     * @param $uri
     * @param null $params
     * @param bool $asQueryString
     * @return null|string
     * @throws GuzzleException
     */
    public function post($uri, $params = null, $asQueryString = false): ?string
    {
        return $this->doRequest($uri, 'post', $params, $asQueryString);
    }

    /**
     * Executa um request utilizando o Guzzle.
     *
     * @param $uri
     * @param $method
     * @param null $params
     * @param bool $asQueryString
     * @return string
     * @throws GuzzleException
     */
    public function doRequest($uri, $method, $params = null, $asQueryString = false): ?string
    {
        try {
            $authHeader = $this->getAuthHeader();
            if (!$this->getBaseURI()) {
                throw new RuntimeException('baseURI não definido');
            }
            $uri = $this->getBaseURI() . $uri;
            $cParams = [];
            if (isset($_SERVER['CROSIERCORE_SELFSIGNEDCERT'])) {
                $cParams['verify'] = $_SERVER['CROSIERCORE_SELFSIGNEDCERT'];
            }
            $client = new Client($cParams);
            $key = $asQueryString ? 'query' : 'json';
            $response = $client->request($method, $uri,
                [
                    'headers' => $authHeader,
                    $key => $params
                ]
            );
            return $response->getBody()->getContents();
        } catch (Exception $e) {
            $this->logger->error('URI: ' . $uri);
            $this->logger->error($e->getMessage());
            throw new RuntimeException('Erro - ' . $uri, 0, $e);
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getAuthHeader()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user->getApiToken()) {
            $this->logger->error('user sem apiToken');
            throw new RuntimeException('user sem apiToken');
        }
        $apiToken = $user && $user->getApiToken() ? $user->getApiToken() : ' ???';
        $authHeader['X-Authorization'] = 'Bearer ' . $apiToken;
        return $authHeader;
    }

    /**
     * @return string
     */
    public function getBaseURI(): string
    {
        return $this->baseURI;
    }

    /**
     * @param string $baseURI
     * @return CrosierAPIClient
     */
    public function setBaseURI(string $baseURI)
    {
        $this->baseURI = $baseURI;
        return $this;
    }

    /**
     * @param $uri
     * @param null $params
     * @param bool $asQueryString
     * @return null|string
     * @throws GuzzleException
     */
    public function get($uri, $params = null, $asQueryString = true): ?string
    {
        return $this->doRequest($uri, 'get', $params, $asQueryString);
    }

    /**
     * @param $r
     * @throws ViewException
     */
    public function handleErrorResponse($r): void
    {
        $this->logger->error(print_r($r, true));
        if (isset($r['status']) and $r['status'] === 400) {
            throw new ViewException($r['status'] . ' - ' . $r['title']);
        }
        // else
        throw new ViewException('API error');

    }

}
