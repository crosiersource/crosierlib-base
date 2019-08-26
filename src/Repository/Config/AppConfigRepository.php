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

    public function findConfigByCrosierEnv(App $app, string $chave)
    {
        try {
            $dql = "SELECT c FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig c WHERE c.appUUID = :appUUID AND c.chave = :chave";
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('appUUID', $app->getUUID());
            $crosierEnv = $_SERVER['CROSIER_ENV'];
            $qry->setParameter('chave', $chave . '_' . $crosierEnv);
            $appConfig = $qry->getOneOrNullResult();
            return $appConfig ? $appConfig->getValor() : 'NULL_CONFIG';
        } catch (NonUniqueResultException $e) {
            $this->getLogger()->error($e->getMessage());
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
            $dql = 'SELECT ac FROM App\Entity\Config\AppConfig ac JOIN App\Entity\Config\App app WITH ac.app = app WHERE app.nome = :appNome AND ac.chave = :chave';
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('appNome', $appNome);
            $qry->setParameter('chave', $chave);
            return $qry->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

}

