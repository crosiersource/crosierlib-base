<?php

namespace CrosierSource\CrosierLibBaseBundle\RepositoryUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\ORM\QueryBuilder;

class WhereBuilder
{

    public $filterTypes = array(
        'EQ' => 1,
        'NEQ' => 1,
        'LT' => 1,
        'LTE' => 1,
        'GT' => 1,
        'GTE' => 1,
        'IS_NULL' => 0,
        'IS_NOT_NULL' => 0,
        'IN' => 999,
        'NOT_IN' => 999,
        'LIKE' => 1,
        'LIKE_ONLY' => 1,
        'NOT_LIKE' => 1,
        'BETWEEN' => 2
    );

    /**
     *
     * @param QueryBuilder $qb
     * @param array $filters
     * @return \Doctrine\ORM\Query\Expr\Comparison[]|string[]|NULL[]
     * @throws ViewException
     */
    public static function build(QueryBuilder &$qb, $filters)
    {
        if (!$filters) {
            return null;
        }
        $andX = $qb->expr()->andX();

        $filtrando = false;

        foreach ($filters as $filter) {

            WhereBuilder::parseVal($filter);

            if (!WhereBuilder::checkHasVal($filter)) {
                continue;
            }
            $filtrando = true;

            $field_array = is_array($filter->field) ? $filter->field : array(
                $filter->field
            );

            $orX = $qb->expr()->orX();

            $fieldP = null;
            foreach ($field_array as $field) {

                // Verifica se foi passado somente o nome do campo, sem o prefixo do alias da tabela
                if (strpos($field, '.') === FALSE) {
                    $field = 'e.' . $field;
                }
                // nome do parâmetro que ficará na query (tenho que trocar o '.' por '_')
                // depois de setado, usa sempre o mesmo (para os casos onde é feito um OR entre vários campos)
                $fieldP = $fieldP === null ? ':' . str_replace('.', '_', $field) : $fieldP;

                switch ($filter->compar) {
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
                            ->like($field, $fieldP));
                        break;
                    case 'NOT_LIKE':
                        $orX->add($qb->expr()
                            ->notLike($field, $fieldP));
                        break;
                    case 'BETWEEN':
                    case 'BETWEEN_DATE':
                        $orX->add(WhereBuilder::handleBetween($filter, $qb));
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

        foreach ($filters as $filter) {

            if (!WhereBuilder::checkHasVal($filter)) {
                continue;
            }

            $field_array = is_array($filter->field) ? $filter->field : array(
                $filter->field
            );

            $fieldP = null;
            foreach ($field_array as $field) {

                // Verifica se foi passado somente o nome do campo, sem o prefixo do alias da tabela
                if (strpos($field, '.') === FALSE) {
                    $field = 'e.' . $field;
                }

                $fieldP = $fieldP === null ? str_replace('.', '_', $field) : $fieldP;


                switch ($filter->compar) {
                    case 'BETWEEN':
                    case 'BETWEEN_DATE':
                        if ($filter->val['i'])
                            $qb->setParameter($fieldP . '_i', $filter->val['i']);
                        if ($filter->val['f'])
                            $qb->setParameter($fieldP . '_f', $filter->val['f']);
                        break;
                    case 'LIKE':
                        $qb->setParameter($fieldP, '%' . $filter->val . '%');
                        break;
                    case 'LIKE_ONLY':
                        $qb->setParameter($fieldP, $filter->val);
                        break;
                    case 'EQ_BOOL':
                        $qb->setParameter($fieldP, $filter->val === 'true' ? true : false);
                        break;
                    default:
                        $qb->setParameter($fieldP, $filter->val);
                        break;
                }
            }
        }
        return null;

    }

    /**
     * @param $filter
     * @param $qb
     * @return null
     */
    private static function handleBetween($filter, $qb)
    {
        if (!$filter->val['i'] && !$filter->val['f']) {
            return null;
        }

        $field = $filter->field;
        if (strpos($field, '.') === FALSE) {
            $field = 'e.' . $field;
        }

        $fieldP = str_replace('.', '_', $field);


        if (!$filter->val['i']) {
            return $qb->expr()->lte($field, ':' . $fieldP . '_f');
        } else if (!$filter->val['f']) {
            return $qb->expr()->gte($field, ':' . $fieldP . '_i');
        } else {
            return $qb->expr()->between($field, ':' . $fieldP . '_i', ':' . $fieldP . '_f');
        }
    }

    /**
     * @param FilterData $filter
     */
    private static function parseVal(FilterData $filter)
    {
        if ($filter->fieldType == 'decimal') {
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
        if ($filter->compar == 'BETWEEN_DATE') {
            if ($filter->val['i']) {
                $ini = \DateTime::createFromFormat('Y-m-d', $filter->val['i']);
                $ini->setTime(0, 0, 0, 0);
                $filter->val['i'] = $ini;
            }
            if ($filter->val['f']) {
                $fim = \DateTime::createFromFormat('Y-m-d', $filter->val['f']);
                $fim->setTime(23, 59, 59, 999999);
                $filter->val['f'] = $fim;
            }
        }
    }

    /**
     * @param FilterData $filter
     * @return bool
     */
    private static function checkHasVal(FilterData $filter)
    {
        if (is_array($filter->val)) {
            foreach ($filter->val as $val) {
                if ($val) {
                    return true;
                }
            }
        } else {
            if ($filter->val) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $ordersStrs
     * @return array
     */
    public static function buildOrderBy($ordersStrs)
    {
        $ordersBy = array();
        if (!is_array($ordersStrs)) {
            $ordersStrs = array($ordersStrs);
        }
        foreach ($ordersStrs as $orderStr) {
            if (strpos($orderStr, " ") !== FALSE) {
                $o = explode(" ", $orderStr);
                $field = $o[0];
                if (strpos($field, '.') === FALSE) {
                    $field = 'e.' . $field;
                }
                $ordersBy[] = ['column' => $field, 'dir' => $o[1]];
            } else {
                if (strpos($orderStr, '.') === FALSE) {
                    $orderStr = 'e.' . $orderStr;
                }
                $ordersBy[] = ['column' => $orderStr, 'dir' => 'asc'];
            }
        }
        return $ordersBy;
    }
}