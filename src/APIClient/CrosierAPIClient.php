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
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 */
class CrosierAPIClient
{

    /**
     * @var Security
     */
    private $security;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getAuthHeader()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $authHeader['X-Authorization'] = 'Bearer ' . $user->getApiToken();
        return $authHeader;
    }

    /**
     * Executa um request utilizando o Guzzle.
     *
     * @param $uri
     * @param $method
     * @param null $params
     * @return string
     * @throws ViewException
     * @throws GuzzleException
     */
    public function doRequest($uri, $method, $params = null)
    {
        try {
            $base_uri = getenv('CROSIERCORE_URL');
            if (!$base_uri) {
                throw new \Exception('CROSIERCORE_URL não definida');
            }
            $client = new Client(['base_uri' => $base_uri]);
            $uri = $base_uri . $uri;
            $response = $client->request($method, $uri,
                [
                    'headers' => array_merge($this->getAuthHeader(), ['XDEBUG_SESSION' => 'blabla']),
                    'json' => $params
                ]
            );
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            $this->logger->error('URI: ' . $uri);
            $this->logger->error($e->getMessage());
            // throw new ViewException('CrosierAPIClient:doRequest error', 0, $e);
            return null;
        }
    }

    /**
     * @param $uri
     * @param null $params
     * @return string
     * @throws ViewException
     * @throws GuzzleException
     */
    public function post($uri, $params = null)
    {
        return $this->doRequest($uri, 'post', $params);
    }

    /**
     * @param $uri
     * @param null $params
     * @return string
     * @throws ViewException
     */
    public function get($uri, $params = null)
    {
        return $this->doRequest($uri, 'get', $params);
    }

    /**
     * @param $r
     * @throws ViewException
     */
    public function handleErrorResponse($r)
    {
        $this->logger->error(print_r($r, true));
        if (isset($r['status']) and $r['status'] === 400) {
            throw new ViewException($r['status'] . ' - ' . $r['title']);
        } else {
            throw new ViewException('API error');
        }
    }

}