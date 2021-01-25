<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use GuzzleHttp\Client as GuzzleClient;
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

    private ?GuzzleClient $guzzleClient = null;

    private ?string $baseURI = null;

    /**
     * CrosierCoreAssetExtension constructor.
     * @param LoggerInterface $logger
     * @param TagRenderer $tagRenderer
     * @param Packages $assetsManager
     * @throws \Exception
     */
    public function __construct(LoggerInterface $logger,
                                TagRenderer $tagRenderer,
                                Packages $assetsManager)
    {
        $this->tagRenderer = $tagRenderer;
        $this->logger = $logger;
        $this->assetsManager = $assetsManager;
        $this->baseURI = trim($_SERVER['CROSIERCORE_URL']);
        if (!$this->baseURI) {
            throw new \Exception('CROSIERCORE_URL não definido');
        }
    }

    public function getFunctions(): array
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
    public function getAsset(string $asset): string
    {
        return $this->assetsManager->getUrl($asset);
    }

    /**
     * @return GuzzleClient
     * @throws \Exception
     */
    private function getGuzzleClient(): GuzzleClient
    {
        if (!$this->guzzleClient) {

            $cParams = [
                'base_uri' => $this->baseURI,
                'timeout' => 10.0,
            ];

            if ($_SERVER['CROSIER_ENV'] === 'devlocal') {
                $cParams['verify'] = false;
            } else if (isset($_SERVER['CROSIERCORE_SELFSIGNEDCERT'])) {
                $cParams['verify'] = $_SERVER['CROSIERCORE_SELFSIGNEDCERT'];
            }
            if ($_SERVER['GUZZLE_PROXY'] ?? false) {
                $cParams['proxy'] = [
                    'http' => $_SERVER['GUZZLE_PROXY'], // Use this proxy with "http"
                    'https' => $_SERVER['GUZZLE_PROXY'], // Use this proxy with "https",
                ];
            }

            $this->guzzleClient = new GuzzleClient($cParams);
        }
        return $this->guzzleClient;
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
            $uri = $this->baseURI . '/getCrosierAssetUrl?asset=' . urlencode($asset);
            $response = $this->getGuzzleClient()->request('GET', $uri);
            $jsonResponse = $response->getBody()->getContents();
            $decoded = json_decode($jsonResponse, true);
            return $this->baseURI . $decoded['url'];
        } catch (\Throwable $e) {
            $this->logger->error('Erro no getCrosierAsset(\$asset = $asset)');
            $this->logger->error($e->getMessage());
            return 'NOTFOUND111/' . $asset;
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
            $uri = $this->baseURI . '/getRenderCrosierWebpackScriptTags?entryName=' . urlencode($entryName);

            $response = $this->getGuzzleClient()->request('GET', $uri);
            return (string)$response->getBody()->getContents();
        } catch (\Throwable $e) {
            $this->logger->error('Erro no renderCrosierWebpackScriptTags() - entryName: ' . $entryName);
            $this->logger->error($e->getMessage());
            return 'NOTFOUND222/' . $entryName;
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
            $uri = $this->baseURI . '/getRenderCrosierWebpackLinkTags?entryName=' . urlencode($entryName);
            $response = $this->getGuzzleClient()->request('GET', $uri);
            return (string)$response->getBody()->getContents();
        } catch (\Throwable $e) {
            $this->logger->error('Erro no getRenderCrosierWebpackLinkTags() - entryName: ' . $entryName);
            $this->logger->error($e->getMessage());
            return 'NOTFOUND333/' . $entryName;
        }
    }


}
