<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CrosierAPIClient.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 * @author Carlos Eduardo Pauluk
 */
abstract class CrosierAPIClient
{

    /**
     * @var Security
     */
    protected $security;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $baseURI;


    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
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
     * @return mixed
     * @throws \Exception
     */
    public function getAuthHeader()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $apiToken = $user && $user->getApiToken() ? $user->getApiToken() : ' ???';
        $authHeader['X-Authorization'] = 'Bearer ' . $apiToken;
        return $authHeader;
    }

    /**
     * Executa um request utilizando o Guzzle.
     *
     * @param $uri
     * @param $method
     * @param null $params
     * @param bool $asQueryString
     * @return string
     */
    public function doRequest($uri, $method, $params = null, $asQueryString = false): ?string
    {
        try {
            if (!$this->getBaseURI()) {
                throw new \RuntimeException('baseURI não definido');
            }
            $uri = $this->getBaseURI() . $uri;
            $client = new Client();
            $key = $asQueryString ? 'query' : 'json';
            $response = $client->request($method, $uri,
                [
                    'headers' => array_merge($this->getAuthHeader(), ['XDEBUG_SESSION' => 'blabla']),
                    $key => $params
                ]
            );
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            $this->logger->error('URI: ' . $uri);
            $this->logger->error($e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->error('URI: ' . $uri);
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * @param $uri
     * @param null $params
     * @param bool $asQueryString
     * @return null|string
     */
    public function post($uri, $params = null, $asQueryString = false): ?string
    {
        return $this->doRequest($uri, 'post', $params, $asQueryString);
    }

    /**
     * @param $uri
     * @param null $params
     * @param bool $asQueryString
     * @return null|string
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