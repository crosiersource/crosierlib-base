<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository;


use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

/**
 * Classe base para repositórios com pesquisa pelo padrão FilterData.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
abstract class FilterRepository extends EntityRepository
{

    /** @var LoggerInterface */
    private $logger;

    public function __construct(EntityManagerInterface $registry)
    {
        $entityClass = $this::getEntityClass();
        $classMetadata = $registry->getClassMetadata($entityClass);
        parent::__construct($registry, $classMetadata);
    }

    /**
     * @return string
     */
    abstract public function getEntityClass(): string;

    /**
     * @return mixed
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Monta o "FROM" da query.
     *
     * @param QueryBuilder $qb
     */
    public function handleFrombyFilters(QueryBuilder $qb)
    {
        $qb->from($this->getEntityClass(), 'e');
    }

    /**
     * @param null $orderBy
     * @return array|mixed
     * @throws ViewException
     */
    public function findAll($orderBy = null)
    {
        return $this->findByFilters(null, $orderBy, $start = 0, $limit = null);
    }

    /**
     * Ordens padrão do ORDER BY.
     *
     * @return array
     */
    public function getDefaultOrders()
    {
        return ['e.updated' => 'desc'];
    }

    /**
     * Contagem de registros utilizando os filtros.
     *
     * @param $filters
     * @return mixed
     * @throws ViewException
     */
    public function doCountByFilters(?array $filters = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(e.id)');
        $this->handleFrombyFilters($qb);
        WhereBuilder::build($qb, $filters);
//        $dql = $qb->getDql();
//        $sql = $qb->getQuery()->getSQL();
        $count = $qb->getQuery()->getScalarResult();
        return $count[0][1];
    }

    /**
     *
     *
     * @param $filters
     * @param null $orders (no padrão do datatables.js)
     * @param int $start
     * @param int $limit
     * @return mixed
     * @throws ViewException
     */
    public function findByFilters($filters, $orders = null, $start = 0, $limit = 10)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e');
        $this->handleFrombyFilters($qb);
        WhereBuilder::build($qb, $filters);
        if (!$orders) {
            $orders = $this->getDefaultOrders();
        }
        if (is_array($orders)) {
            foreach ($orders as $col => $dir) {
                $qb->addOrderBy($col, $dir);
            }
        } else if (is_string($orders)) {
            $qb->addOrderBy($orders, 'asc');
        }

//        $dql = $qb->getDql();
//        $sql = $qb->getQuery()->getSQL();
        $query = $qb->getQuery();
        $query->setFirstResult($start);
        if ($limit) {
            $query->setMaxResults($limit);
        }
        return $query->execute();
    }

}
