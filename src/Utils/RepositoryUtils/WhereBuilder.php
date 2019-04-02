<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WhereBuilder
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils
 * @author Carlos Eduardo Pauluk
 */
class WhereBuilder
{


    /**
     *
     * @param QueryBuilder $qb
     * @param array $filters
     * @return \Doctrine\ORM\Query\Expr\Comparison[]|string[]|NULL[]
     * @throws ViewException
     */
    public static function build(QueryBuilder $qb, ?array $filters): ?array
    {
        if (!$filters) {
            return null;
        }
        $andX = $qb->expr()->andX();

        $filtrando = false;

        /** @var FilterData $filter */
        foreach ($filters as $filter) {

            self::parseVal($filter);

            if (!self::checkHasVal($filter)) {
                continue;
            }


            $filtrando = true;

            // Adiciona o prefixo padrão 'e.' para os nomes de campos que não tiverem
            foreach ($filter->field as $key => $fieldName) {
                if (strpos($fieldName, '.') === FALSE) {
                    $fieldName = 'e.' . $fieldName;
                }
                $filter->field[$key] = $fieldName;
            }

            $orX = $qb->expr()->orX();

            $fieldP = ':' . str_replace('.', '_', $filter->field[0]);
            foreach ($filter->field as $field) {

                switch ($filter->filterType) {
                    case 'EQ':
                        $orX->add($qb->expr()
                            ->eq($field, $fieldP));
                        break;
                    case 'EQ_BOOL':
                        $orX->add($qb->expr()
                            ->eq($field, $fieldP));
                        break;
                    case 'NEQ':
                        $orX->add($qb->expr()
                            ->neq($field, $fieldP));
                        break;
                    case 'LT':
                        $orX->add($qb->expr()
                            ->lt($field, $fieldP));
                        break;
                    case 'LTE':
                        $orX->add($qb->expr()
                            ->lte($field, $fieldP));
                        break;
                    case 'GT':
                        $orX->add($qb->expr()
                            ->gt($field, $fieldP));
                        break;
                    case 'GTE':
                        $orX->add($qb->expr()
                            ->gte($field, $fieldP));
                        break;
                    case 'IS_NULL':
                        $orX->add($qb->expr()
                            ->isNull($field));
                        break;
                    case 'IS_NOT_NULL':
                        $orX->add($qb->expr()
                            ->isNotNull($field));
                        break;
                    case 'IN':
                        $orX->add($qb->expr()
                            ->in($field, $fieldP));
                        break;
                    case 'NOT_IN':
                        // $exprs[] = $qb->expr()->isNotNull($field, $val);
                        break;
                    case 'LIKE':
                    case 'LIKE_ONLY':
                        $orX->add($qb->expr()
                            ->like($qb->expr()->lower($field), $fieldP));
                        break;
                    case 'NOT_LIKE':
                        $orX->add($qb->expr()
                            ->notLike($field, $fieldP));
                        break;
                    case 'BETWEEN':
                    case 'BETWEEN_DATE':
                        $orX->add(self::handleBetween($field, $filter, $qb));
                        break;
                    default:
                        throw new ViewException('Tipo de filtro desconhecido.');
                }
            }
            $andX->add($orX);
        }
        if (!$filtrando) {
            return null;
        }
        $qb->where($andX);

        // $qb->getDql()
        foreach ($filters as $filter) {

            if (!self::checkHasVal($filter)) {
                continue;
            }

            $fieldP = str_replace('.', '_', $filter->field[0]);
//            foreach ($field_array as $field) {

            switch ($filter->filterType) {
                case 'BETWEEN':
                case 'BETWEEN_DATE':
                    if ($filter->val['i']) {
                        $qb->setParameter($fieldP . '_i', $filter->val['i']);
                    }
                    if ($filter->val['f']) {
                        $qb->setParameter($fieldP . '_f', $filter->val['f']);
                    }
                    break;
                case 'LIKE':
                    $qb->setParameter($fieldP, '%' . strtolower($filter->val) . '%');
                    break;
                case 'LIKE_ONLY':
                    $qb->setParameter($fieldP, $filter->val);
                    break;
                case 'EQ_BOOL':
                    $qb->setParameter($fieldP, $filter->val === 'true');
                    break;
                case 'IS_NULL':
                case 'IS_NOT_NULL':
                    break;
                default:
                    $qb->setParameter($fieldP, $filter->val);
                    break;
            }
//            }
        }
        return null;

    }

    /**
     * @param string $field
     * @param FilterData $filter
     * @param QueryBuilder $qb
     * @return null
     */
    private static function handleBetween(string $field, FilterData $filter, QueryBuilder $qb)
    {
        // Usa sempre o nme do primeiro campo como nome para o parâmetro, pois setará sempre o mesmo na lógica do "OR"
        $fieldP = str_replace('.', '_', $filter->field[0]);

        if (!$filter->val['i']) {
            return $qb->expr()->lte($field, ':' . $fieldP . '_f');
        }
        if (!$filter->val['f']) {
            return $qb->expr()->gte($field, ':' . $fieldP . '_i');
        }
        return $qb->expr()->between($field, ':' . $fieldP . '_i', ':' . $fieldP . '_f');

    }

    /**
     * @param FilterData $filter
     * @throws ViewException
     */
    private static function parseVal(FilterData $filter): void
    {
        if ($filter->fieldType === 'decimal') {
            if (!is_array($filter->val)) {
                $filter->val = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val);
            } else {
                if ($filter->val['i']) {
                    $filter->val['i'] = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val['i']);
                }
                if ($filter->val['f']) {
                    $filter->val['f'] = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val['f']);
                }
            }
        }
        if ($filter->filterType === 'BETWEEN_DATE') {
            if ($filter->val['i'] && !($filter->val['i'] instanceof \DateTime)) {
                $ini = DateTimeUtils::parseDateStr($filter->val['i']);
                $ini->setTime(0, 0);
                $filter->val['i'] = $ini;
            }
            if ($filter->val['f'] && !($filter->val['f'] instanceof \DateTime)) {
                $fim = DateTimeUtils::parseDateStr($filter->val['f']);
                $fim->setTime(23, 59, 59, 999999);
                $filter->val['f'] = $fim;
            }
        }
    }

    /**
     * @param FilterData $filter
     * @return bool
     */
    private static function checkHasVal(FilterData $filter): bool
    {
        if ($filter->filterType === 'IS_NULL' || $filter->filterType === 'IS_NOT_NULL') {
            return true;
        }
        if (is_array($filter->val)) {
            foreach ($filter->val as $val) {
                if ($val) {
                    return true;
                }
            }
        } else if ($filter->val) {
            return true;
        }
        return false;
    }

    /**
     * @param $ordersStrs
     * @return array
     */
    public static function buildOrderBy($ordersStrs): array
    {
        $ordersBy = array();
        $ordersStrs = is_array($ordersStrs) ? $ordersStrs : [$ordersStrs];
        foreach ($ordersStrs as $orderStr) {
            // Se tiver espaço, então deve ser 'campo DESC' ou 'campo ASC'
            if (strpos($orderStr, ' ') !== FALSE) {
                $o = explode(' ', $orderStr);
                $field = $o[0];
                $dir = $o[1];
                if (strpos($field, '.') === FALSE) {
                    $field = 'e.' . $field;
                }
                $ordersBy = [$field => $dir];
            } else {
                $field = $orderStr;
                if (strpos($field, '.') === FALSE) {
                    $field = 'e.' . $field;
                }
                $ordersBy = [$field => 'asc'];
            }
        }
        return $ordersBy;
    }
}