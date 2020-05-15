<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CrosierCoreAssetExtension.
 * Funciona em conjunto com o CrosierCoreAssetController.
 * Ver mais em https://symfony.com/doc/current/frontend/encore/split-chunks.html.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class CrosierCoreAssetExtension extends AbstractExtension
{

    private LoggerInterface $logger;

    public TagRenderer $tagRenderer;

    private Packages $assetsManager;


    public function __construct(LoggerInterface $logger, TagRenderer $tagRenderer, Packages $assetsManager)
    {
        $this->tagRenderer = $tagRenderer;
        $this->logger = $logger;
        $this->assetsManager = $assetsManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('crosierAsset', [$this, 'getCrosierAsset']),
            new TwigFunction('renderCrosierWebpackScriptTags', [$this, 'renderCrosierWebpackScriptTags'], ['is_safe' => ['html']]),
            new TwigFunction('renderCrosierWebpackLinkTags', [$this, 'renderCrosierWebpackLinkTags'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $asset
     * @return string
     */
    public function getAsset(string $asset)
    {
        return $this->assetsManager->getUrl($asset);
    }

    /**
     * @param $asset
     * @return string
     */
    public function getCrosierAsset($asset): ?string
    {
        $asset = trim($asset);
        if ($_SERVER['CROSIERAPP_UUID'] === '175bd6d3-6c29-438a-9520-47fcee653cc5') {
            return $this->getAsset($asset);
        }
        // else

        try {
            $base_uri = trim($_SERVER['CROSIERCORE_URL']);
            if (!$base_uri) {
                throw new \Exception('CROSIERCORE_URL não definido');
            }

            $cParams = [
                'base_uri' => $base_uri,
                'timeout' => 10.0,
            ];
            if (isset($_SERVER['CROSIERCORE_SELFSIGNEDCERT'])) {
                $cParams['verify'] = $_SERVER['CROSIERCORE_SELFSIGNEDCERT'];
            }
            $client = new Client($cParams);
            $uri = $base_uri . '/getCrosierAssetUrl?asset=' . urlencode($asset);
            $response = $client->request('GET', $uri);
            $jsonResponse = $response->getBody()->getContents();
            $decoded = json_decode($jsonResponse, true);
            return $base_uri . $decoded['url'];
        } catch (\Throwable $e) {
            $this->logger->error('Erro no getCrosierAsset(\$asset = $asset)');
            $this->logger->error($e->getMessage());
            return 'NOTFOUND/' . $asset;
        }
    }


    /**
     * Copiado/adaptado de Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension.
     *
     * É chamado também pelo CrosierCoreAssetController.
     *
     * @param string $entryName
     * @return string
     */
    public function getRenderCrosierWebpackScriptTags(string $entryName): string
    {
        $tags = $this->tagRenderer->renderWebpackScriptTags($entryName);
        $tags = str_replace('<script src="', '<script src="' . $_SERVER['CROSIERCORE_URL'], $tags);
        return $tags;
    }

    /**
     * @param string $entryName
     * @return string
     */
    public function renderCrosierWebpackScriptTags(string $entryName): ?string
    {
        $entryName = trim($entryName);
        if ($_SERVER['CROSIERAPP_UUID'] === '175bd6d3-6c29-438a-9520-47fcee653cc5') {
            return $this->getRenderCrosierWebpackScriptTags($entryName);
        }
        // else
        try {

            $base_uri = trim($_SERVER['CROSIERCORE_URL']);
            if (!$base_uri) {
                throw new \Exception('CROSIERCORE_URL não definido');
            }

            $cParams = [
                'base_uri' => $base_uri,
                'timeout' => 10.0,
            ];
            if (isset($_SERVER['CROSIERCORE_SELFSIGNEDCERT'])) {
                $cParams['verify'] = $_SERVER['CROSIERCORE_SELFSIGNEDCERT'];
            }
            $client = new Client($cParams);
            $uri = $base_uri . '/getRenderCrosierWebpackScriptTags?entryName=' . urlencode($entryName);

            $response = $client->request('GET', $uri);
            $r = (string) $response->getBody()->getContents();
            return $r;
        } catch (\Throwable $e) {
            $this->logger->error('Erro no renderCrosierWebpackScriptTags() - entryName: ' . $entryName);
            $this->logger->error($e->getMessage());
            return 'NOTFOUND/' . $entryName;
        }
    }


    /**
     * Copiado/adaptado de Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension
     *
     * É chamado também pelo CrosierCoreAssetController.
     * @param string $entryName
     * @return string
     */
    public function getRenderCrosierWebpackLinkTags(string $entryName): string
    {
        $tags = $this->tagRenderer->renderWebpackLinkTags($entryName);
        $tags = str_replace('<link rel="stylesheet" href="', '<link rel="stylesheet" href="' . $_SERVER['CROSIERCORE_URL'], $tags);
        return $tags;
    }


    /**
     * @param string $entryName
     * @return string
     */
    public function renderCrosierWebpackLinkTags(string $entryName): ?string
    {
        $entryName = trim($entryName);
        if ($_SERVER['CROSIERAPP_UUID'] === '175bd6d3-6c29-438a-9520-47fcee653cc5') {
            return $this->getRenderCrosierWebpackLinkTags($entryName);
        }
        // else
        try {
            $entryName = trim($entryName);
            $base_uri = trim($_SERVER['CROSIERCORE_URL']);
            if (!$base_uri) {
                throw new \Exception('CROSIERCORE_URL não definido');
            }

            $cParams = [
                'base_uri' => $base_uri,
                'timeout' => 10.0,
            ];
            if (isset($_SERVER['CROSIERCORE_SELFSIGNEDCERT'])) {
                $cParams['verify'] = $_SERVER['CROSIERCORE_SELFSIGNEDCERT'];
            }
            $client = new Client($cParams);
            $uri = $base_uri . '/getRenderCrosierWebpackLinkTags?entryName=' . urlencode($entryName);
            $response = $client->request('GET', $uri);
            $r = (string) $response->getBody()->getContents();
            return $r;
        } catch (\Throwable $e) {
            $this->logger->error('Erro no getRenderCrosierWebpackLinkTags() - entryName: ' . $entryName);
            $this->logger->error($e->getMessage());
            return 'NOTFOUND/' . $entryName;
        }
    }


}