<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
            $this->logger->info(str_repeat('.',100));
            $this->logger->info('getCrosierAsset(' . $asset . ')');
            $base_uri = getenv('CROSIERCORE_URL');
            $this->logger->info($base_uri);
            $client = new Client(['base_uri' => $base_uri]);
            $uri = $base_uri . '/getCrosierAssetUrl?asset=' . urlencode($asset);
            $response = $client->request('GET', $uri);

            $jsonResponse = $response->getBody()->getContents();
            $decoded = json_decode($jsonResponse, true);
            return $base_uri . $decoded['url'];
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return 'NOTFOUND/' . $asset;
            // throw new ViewException('CrosierCoreAssetExtension:getCrosierAsset error');
        }

    }
}