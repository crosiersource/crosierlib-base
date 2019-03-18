<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class CrosierCoreAssetExtension extends AbstractExtension
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('crosierAsset', array($this, 'getCrosierAsset')),
        );
    }

    /**
     * @param $asset
     * @return string
     * @throws ViewException
     */
    function getCrosierAsset($asset)
    {
        try {
            $asset = trim($asset);
            $this->logger->debug(str_repeat('.',100));
            $this->logger->debug('getCrosierAsset(' . $asset . ')');
            $base_uri = trim(getenv('CROSIERCORE_URL'));
            $this->logger->info($base_uri);
            $client = new Client([
                'base_uri' => $base_uri,
                'timeout'  => 10.0,
            ]);
            $uri = $base_uri . '/getCrosierAssetUrl?asset=' . urlencode($asset);
            $this->logger->debug('request uri="' . $uri . '"');
            $response = $client->request('GET', $uri);
            $this->logger->debug('OK! getContents()');
            $jsonResponse = $response->getBody()->getContents();
            $this->logger->debug('OK!');
            $decoded = json_decode($jsonResponse, true);
            $this->logger->debug('url="' . $decoded['url'] . '"');
            return $base_uri . $decoded['url'];
            return null;
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return 'NOTFOUND/' . $asset;
            // throw new ViewException('CrosierCoreAssetExtension:getCrosierAsset error');
        }

    }
}