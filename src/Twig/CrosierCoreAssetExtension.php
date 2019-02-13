<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class CrosierCoreAssetExtension extends AbstractExtension
{


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
            $base_uri = getenv('CROSIERCORE_URL');
            $client = new Client(['base_uri' => $base_uri]);
            $uri = $base_uri . '/getCrosierAssetUrl?asset=' . urlencode($asset);
            $response = $client->request('GET', $uri);

            $jsonResponse = $response->getBody()->getContents();
            $decoded = json_decode($jsonResponse, true);
            return $base_uri . $decoded['url'];
        } catch (GuzzleException $e) {
            throw new ViewException('CrosierCoreAssetExtension:getCrosierAsset error');
        }

    }
}