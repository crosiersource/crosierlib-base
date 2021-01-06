<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository;


use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Classe base para repositórios com pesquisa pelo padrão FilterData.
 *
 * @author Carlos Eduardo Pauluk
 */
abstract class FilterRepository extends EntityRepository
{

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
     * @param null $orderBy
     * @return array|mixed
     * @throws ViewException
     */
    public function findAll($orderBy = null)
    {
        return $this->findByFilters(null, $orderBy, $start = 0, $limit = null);
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
                if (strpos($col, '.') === FALSE) {
                    $col = 'e.' . $col;
                }
                if (strpos($col, 'jsonData') !== FALSE) {
                    if (strpos($col, '.dt_') !== FALSE) {
                        $col = 'CAST(JSON_UNQUOTE(JSON_EXTRACT(e.jsonData, \'$.' . substr($col, 11) . '\')) AS DATE)';
                    } else {
                        $col = 'JSON_EXTRACT(e.jsonData, \'$.' . substr($col, 11) . '\')';
                    }
                }
                $qb->addOrderBy($col, $dir);
            }
        } else if (is_string($orders)) {
            $qb->addOrderBy($orders, 'asc');
        }

//        $dql = $qb->getDql();
//        $sql = $qb->getQuery()->getSQL();
        $query = $qb->getQuery();
        $query->setFirstResult($start);
        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        return $query->getResult();
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
     * Ordens padrão do ORDER BY.
     *
     * @return array
     */
    public function getDefaultOrders()
    {
        return ['e.updated' => 'desc'];
    }

    /**
     *
     *
     * @param array $filtersSimpl
     * @return mixed
     * @throws ViewException
     */
    public function doCountByFiltersSimpl(array $filtersSimpl)
    {
        $filters = [];
        foreach ($filtersSimpl as $filterSimpl) {
            $filter = new FilterData($filterSimpl[0], $filterSimpl[1]);
            if (isset($filterSimpl[2])) {
                $filter->setVal($filterSimpl[2]);
            }
            $filters[] = $filter;
        }
        return $this->doCountByFilters($filters);
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
     * @param array $filtersSimpl
     * @param null $orders
     * @return mixed|null
     * @throws ViewException
     */
    public function findOneByFiltersSimpl(array $filtersSimpl, $orders = null)
    {
        $r = $this->findByFiltersSimpl($filtersSimpl, $orders, 0, 2);
        if ($r) {
            if (count($r) > 1) {
                throw new ViewException('Mais de um resultado encontrado.');
            }
            return $r[0];
        }
        return null;
    }

    /**
     *
     *
     * @param array $filtersSimpl
     * @param null $orders (no padrão do datatables.js)
     * @param int $start
     * @param int $limit
     * @return mixed
     * @throws ViewException
     */
    public function findByFiltersSimpl(array $filtersSimpl, $orders = null, $start = 0, $limit = 10)
    {
        $filters = [];
        foreach ($filtersSimpl as $filterSimpl) {
            if ($filterSimpl instanceof FilterData) {
                $filters[] = $filterSimpl;
            } else {
                $filter = new FilterData($filterSimpl[0], $filterSimpl[1]);
                if (isset($filterSimpl[2])) {
                    $filter->setVal($filterSimpl[2]);
                }
                $filters[] = $filter;
            }
        }
        return $this->findByFilters($filters, $orders, $start, $limit);
    }

    /**
     * @param string $field
     * @return mixed
     * @throws ViewException
     */
    public function findProx($field = 'id')
    {
        $prox = null;

        try {
            $tableName = $this->getEntityManager()->getClassMetadata($this->getEntityClass())->getTableName();
            $sql = 'SELECT (max(' . $field . ') + 1) as prox FROM ' . $tableName;
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('prox', 'prox');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $rs = $query->getResult();
            $prox = $rs[0]['prox'];
        } catch (\Exception $e) {
            throw new ViewException('Erro ao buscar o próximo "' . $field . '" em ' . $this->getEntityClass());
        }

        return $prox;
    }

}
