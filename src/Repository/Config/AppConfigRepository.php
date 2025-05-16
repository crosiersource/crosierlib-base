<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\App;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade AppConfig.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class AppConfigRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return AppConfig::class;
    }

    public function handleFrombyFilters(QueryBuilder $qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin(App::class, 'app', 'WITH', 'app.UUID = e.appUUID');
    }


    public function findConfigByAppNameAndCrosierEnv(string $appName, string $chave)
    {
        $appRepo = $this->doctrine->getRepository(App::class);
        $app = $appRepo->findOneBy(['nome' => $appName]);
        return $this->findConfigByCrosierEnv($app, $chave);
    }

    /**
     * @param App $app
     * @param string $chave
     * @return string|null
     */
    public function findConfigByCrosierEnv(App $app, string $chave)
    {
        try {
            $dql = "SELECT c FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig c WHERE c.appUUID = :appUUID AND c.chave = :chave";
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('appUUID', $app->UUID);
            $crosierEnv = $_SERVER['CROSIER_ENV'];
            $qry->setParameter('chave', $chave . '_' . $crosierEnv);
            /** @var AppConfig $appConfig */
            $appConfig = $qry->getOneOrNullResult();
            if (!$appConfig) {
                return 'NULL_CONFIG';
            }
            return $appConfig->valor;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Pesquisa uma configuração de um App por sua chave.
     *
     * @param string $chave
     * @param string $appNome
     * @return AppConfig|null
     */
    public function findConfigByChaveAndAppNome(string $chave, string $appNome): ?AppConfig
    {
        try {
            $dql = 'SELECT ac FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig ac JOIN CrosierSource\CrosierLibBaseBundle\Entity\Config\App app WITH ac.appUUID = app.UUID WHERE app.nome = :appNome AND ac.chave = :chave';
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('appNome', $appNome);
            $qry->setParameter('chave', $chave);
            return $qry->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Pesquisa uma configuração de um App por sua chave.
     *
     * @param string $chave
     * @param string $appUUID
     * @return AppConfig|null
     */
    public function findConfigByChaveAndAppUUID(string $chave, string $appUUID): ?AppConfig
    {
        try {
            $dql = 'SELECT ac FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig ac WHERE ac.chave = :chave AND ac.appUUID = :appUUID';
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('chave', $chave);
            $qry->setParameter('appUUID', $appUUID);
            return $qry->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Retorna o valor pela chave.
     * @param string $chave
     * @return AppConfig|null
     */
    public function findByChave(string $chave): ?string
    {
        try {
            $dql = 'SELECT ac FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig ac WHERE ac.chave = :chave AND ac.appUUID = :appUUID';
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('chave', $chave);
            $qry->setParameter('appUUID', $_SERVER['CROSIERAPP_UUID']);
            return $qry->getSingleResult()->valor;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Retorna um AppConfig pela chave
     * @param string $chave
     * @return AppConfig|null
     */
    public function findAppConfigByChave(string $chave): ?AppConfig
    {
        try {
            $dql = 'SELECT ac FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig ac WHERE ac.chave = :chave AND ac.appUUID = :appUUID';
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('chave', $chave);
            $qry->setParameter('appUUID', $_SERVER['CROSIERAPP_UUID']);
            return $qry->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Pesquisa uma configuração de um App por sua chave.
     *
     * @param string $chave
     * @param string $appUUID
     * @return string|null
     */
    public function findValorByChaveAndAppUUID(string $chave, string $appUUID): ?string
    {
        try {
            $sql = 'SELECT valor FROM cfg_app_config ac WHERE ac.chave = :chave AND ac.app_uuid = :appUUID';
            return $this->getEntityManager()->getConnection()->fetchAssociative($sql, ['chave' => $chave, 'appUUID' => $appUUID])['valor'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Pesquisa uma configuração de um App por sua chave.
     *
     * @param string $chave
     * @param string $appUUID
     * @return string|null
     */
    public function findValorByChaveAndAppNome(string $chave, string $appNome): ?string
    {
        try {
            $sql = 'SELECT valor FROM cfg_app_config ac, cfg_app a WHERE a.uuid = ac.app_uuid AND ac.chave = :chave AND a.nome = :appNome';
            return $this->getEntityManager()->getConnection()->fetchAssociative($sql, ['chave' => $chave, 'appNome' => $appNome])['valor'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

}

