<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class AppConfigBusiness
{

    private EntityManagerInterface $doctrine;

    private AppConfigEntityHandler $appConfigEntityHandler;

    public function __construct(
        EntityManagerInterface $doctrine,
        AppConfigEntityHandler $appConfigEntityHandler
    )
    {
        $this->doctrine = $doctrine;
        $this->appConfigEntityHandler = $appConfigEntityHandler;
    }

    public function getValor(string $chave)
    {
        $cache = new FilesystemAdapter('cfg_app_config', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $valor = $cache->get('getValor_' . $chave, function (ItemInterface $item) use ($chave) {
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', $chave]]);
            if ($appConfig) {
                if ($appConfig->isJson) {
                    return json_decode($appConfig->valor, true);
                }
                return $appConfig->valor;
            }
            return null;
        });
        return $valor;
    }

    public function setValor(string $chave, string $valor): void
    {
        $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
        $appConfig = $repoAppConfig->findAppConfigByChave($chave);
        if (!$appConfig) {
            $appConfig = new AppConfig();
            $appConfig->chave = $chave;
            $appConfig->appUUID = $_SERVER['CROSIERAPP_UUID'];
        }
        $appConfig->valor = $valor;
        $this->appConfigEntityHandler->save($appConfig);
    }


}