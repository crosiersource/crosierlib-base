<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use NumberFormatter;

/**
 * Class WhereBuilder
 *
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
            $orX = self::buildAndCondition($qb, $filter);

            $andX->add($orX);

        }
        if (!$filtrando) {
            return null;
        }
        $qb->where($andX);

        foreach ($filters as $filter) {
            if (!self::checkHasVal($filter)) {
                continue;
            }
            self::placeValues($qb, $filter);
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
        // Usa sempre o nome do primeiro campo como nome para o parâmetro, pois setará sempre o mesmo na lógica do "OR"
        $fieldP = str_replace('.', '_', $filter->field[0]);

        if ($filter->filterType === 'BETWEEN_PORCENT') {
            $field = 'CAST(' . $field . ' AS DECIMAL(15,4))';
        }

        if ($filter->val['i'] === null || $filter->val['i'] === '') {
            return $qb->expr()->lte($field, ':' . $fieldP . '_f');
        }
        if ($filter->val['f'] === null || $filter->val['f'] === '') {
            return $qb->expr()->gte($field, ':' . $fieldP . '_i');
        }
        return $qb->expr()->between($field, ':' . $fieldP . '_i', ':' . $fieldP . '_f');

    }

    /**
     * @param FilterData $filter
     * @throws ViewException
     * @throws \Exception
     */
    public static function parseVal(FilterData $filter): void
    {
        if ($filter->fieldType === 'decimal') {
            if (!is_array($filter->val)) {
                $filter->val = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val);
            } else {
                if ($filter->val['i'] !== null && !is_numeric($filter->val['i'])) {
                    $filter->val['i'] = DecimalUtils::parseStr($filter->val['i']);
                }
                if ($filter->val['f'] !== null && !is_numeric($filter->val['f'])) {
                    $filter->val['f'] = DecimalUtils::parseStr($filter->val['f']);
                }
            }
            return;
        }
        if (in_array($filter->filterType, ['BETWEEN_DATE', 'BETWEEN_DATE_CONCAT'], true)) {
            if ($filter->val['i'] ?? false) {
                if (!($filter->val['i'] instanceof \DateTime)) {
                    $ini = DateTimeUtils::parseDateStr($filter->val['i']);
                    $filter->val['i'] = $ini;
                }
                $filter->val['i']->setTime(0, 0);
            }

            if ($filter->val['f'] ?? false) {
                if (!($filter->val['f'] instanceof \DateTime)) {
                    $fim = DateTimeUtils::parseDateStr($filter->val['f']);
                    $filter->val['f'] = $fim;
                }
                $filter->val['f']->setTime(23, 59, 59, 999999);
            }

            return;
        }
        if ($filter->filterType === 'BETWEEN_IDADE') {

            $f = $filter->val['f']; // auxiliar, pois poderá ser alterado
            if ($filter->val['i']) {
                $max = new \DateTime('now');
                $max->sub(new \DateInterval('P' . (int)$filter->val['i'] . 'Y'));
                $filter->val['f'] = $max->format('Y-m-d');
            } else {
                $filter->val['f'] = null;
            }
            if ($f) {
                $min = new \DateTime('now');
                $min->sub(new \DateInterval('P' . (((int)$f) + 1) . 'Y'));
                $min->add(new \DateInterval('P1D'));
                $min->setTime(0, 0);
                $filter->val['i'] = $min->format('Y-m-d');
            } else {
                $filter->val['i'] = null;
            }
            return;
        }
        if ($filter->filterType === 'BETWEEN_MESANO') {
            $dt = \DateTime::createFromFormat('dmY', '01' . $filter->val['mes'] . $filter->val['ano']);
            $filter->val = null;
            $ini = DateTimeUtils::getPrimeiroDiaMes($dt);
            $filter->val['i'] = $ini->format('Y-m-d');
            $fim = DateTimeUtils::getUltimoDiaMes($dt);
            $fim->setTime(23, 59, 59, 999999);
            $filter->val['f'] = $fim->format('Y-m-d');
            return;
        }

        if ($filter->filterType === 'IS_EMPTY' || $filter->filterType === 'IS_NOT_EMPTY') {
            $filter->val = '';
            return;
        }

        if ($filter->fieldType === 'date') {
            if ($filter->val instanceof \DateTime) {
                $filter->val = $filter->val->format('Y-m-d');
            } else {
                $filter->val = DateTimeUtils::parseDateStr($filter->val)->format('Y-m-d');
            }
        }
    }

    /**
     * @param FilterData $filter
     * @return bool
     */
    private static function checkHasVal(FilterData $filter): bool
    {
        if ($filter->filterType === 'IS_NULL' || $filter->filterType === 'IS_NOT_NULL' || $filter->filterType === 'IS_EMPTY' || $filter->filterType === 'IS_NOT_EMPTY') {
            return true;
        }
        if (is_array($filter->val)) {
            foreach ($filter->val as $val) {
                if ($val !== null && $val !== '') {
                    return true;
                }
            }
        } else if ($filter->val !== null && $filter->val !== '') {
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

    /**
     * @param QueryBuilder $qb
     * @param FilterData $filter
     * @return Orx
     * @throws ViewException
     */
    public static function buildAndCondition(QueryBuilder $qb, FilterData $filter): Orx
    {
        // Adiciona o prefixo padrão 'e.' para os nomes de campos que não tiverem
        foreach ($filter->field as $key => $fieldName) {
            if (strpos($fieldName, '.') === FALSE) {
                $fieldName = 'e.' . $fieldName;
            }
            $filter->field[$key] = $fieldName;
        }

        $orX = $qb->expr()->orX();

        $fieldP = ':' . str_replace('.', '_', $filter->field[0]) . ($filter->isOrFilterData ? '_OFD' : '');
        foreach ($filter->field as $field) {

            if ($filter->jsonDataField) {
                $field = 'JSON_UNQUOTE(JSON_EXTRACT(e.jsonData, \'$.' . substr($field, 2) . '\'))';

                if ($filter->fieldType == 'date') {
                    $field = 'STR_TO_DATE(' . $field . ', \'%Y-%m-%d\')';
                } elseif ($filter->fieldType == 'datetime') {
                    $field = 'STR_TO_DATE(' . $field . ', \'%Y-%m-%d\ %H:%i:%s\')';
                }
            }


            switch ($filter->filterType) {
                case 'EQ':
                case 'IS_EMPTY':
                    if ($filter->fieldType === 'date') {
                        $orX->add($qb->expr()
                            ->eq('date(' . $field . ')', $fieldP));
                        break;
                    }
                    $orX->add($qb->expr()
                        ->eq($field, $fieldP));
                    break;
                case 'EQ_DIAMES':
                    $orX->add($qb->expr()
                        ->eq('DATE_FORMAT(' . $field . ', \'%d/%m\')', $fieldP));
                    break;
                case 'EQ_BOOL':
                    $orX->add($qb->expr()
                        ->eq($field, $fieldP));
                    break;
                case 'NEQ':
                case 'IS_NOT_EMPTY':
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
                        ->in($qb->expr()->lower($field), $fieldP));
                    break;
                case 'NOT_IN':
                    $orX->add($qb->expr()
                        ->notIn($qb->expr()->lower($field), $fieldP));
                    break;
                case 'LIKE':
                case 'LIKE_START':
                case 'LIKE_END':
                case 'LIKE_ONLY':
                    $orX->add($qb->expr()
                        ->like($qb->expr()->lower($field), $fieldP));
                    break;
                case 'NOT_LIKE':
                    $orX->add($qb->expr()
                        ->notLike($qb->expr()->lower($field), $fieldP));
                    break;
                case 'BETWEEN':
                case 'BETWEEN_DATE':
                case 'BETWEEN_IDADE':
                case 'BETWEEN_MESANO':
                case 'BETWEEN_PORCENT':
                case 'BETWEEN_DATE_CONCAT':
                    $orX->add(self::handleBetween($field, $filter, $qb));
                    break;

                default:
                    throw new ViewException('Tipo de filtro desconhecido.');
            }

            if ($filter->getOrFilterData()) {
                $orX->add(self::buildAndCondition($qb, $filter->getOrFilterData()));
            }
        }
        return $orX;
    }

    /**
     * @param QueryBuilder $qb
     * @param FilterData $filter
     */
    public static function placeValues(QueryBuilder $qb, FilterData $filter): void
    {
        $fieldP = str_replace('.', '_', $filter->field[0]) . ($filter->isOrFilterData ? '_OFD' : '');

        switch ($filter->filterType) {
            case 'BETWEEN':
            case 'BETWEEN_DATE':
            case 'BETWEEN_DATE_CONCAT':
            case 'BETWEEN_IDADE':
            case 'BETWEEN_MESANO':
                if ($filter->val['i'] !== null && $filter->val['i'] !== '') {
                    $qb->setParameter($fieldP . '_i', $filter->val['i']);
                }
                if ($filter->val['f'] !== null && $filter->val['f'] !== '') {
                    $qb->setParameter($fieldP . '_f', $filter->val['f']);
                }
                break;
            case 'BETWEEN_PORCENT':
                if (isset($filter->val['i'])) {
                    if (!is_float($filter->val['i'])) {
                        $fmt = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
                        $filter->val['i'] = $fmt->parse($filter->val['i']);
                    }
                    $val_i = round((float)bcdiv($filter->val['i'], 100, 6), 6);
                    $val_i = floor($val_i) !== $val_i ? $val_i : (int)$val_i;
                    $qb->setParameter($fieldP . '_i', $val_i);
                }
                if (isset($filter->val['f'])) {
                    if (!is_float($filter->val['f'])) {
                        $fmt = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
                        $filter->val['f'] = $fmt->parse($filter->val['f']);
                    }
                    $val_f = round((float)bcdiv($filter->val['f'], 100, 6), 6);
                    $val_f = floor($val_f) !== $val_f ? $val_f : (int)$val_f;
                    $qb->setParameter($fieldP . '_f', $val_f);
                }
                break;
            case 'LIKE':
                $qb->setParameter($fieldP, '%' . mb_strtolower($filter->val) . '%');
                break;
            case 'LIKE_START':
                $qb->setParameter($fieldP, mb_strtolower($filter->val) . '%');
                break;
            case 'LIKE_END':
                $qb->setParameter($fieldP, '%' . mb_strtolower($filter->val));
                break;
            case 'EQ_BOOL':
                $qb->setParameter($fieldP, $filter->val === 'true');
                break;
            case 'IS_NULL':
            case 'IS_NOT_NULL':
                break;
            case 'IN':
            case 'NOT_IN':
                if (is_array($filter->val)) {
                    $filter->val = explode(',', mb_strtolower(implode(',', $filter->val)));
                } else {
                    $filter->val = mb_strtolower($filter->val));
                }
                $qb->setParameter($fieldP, $filter->val);
                break;
            case 'EQ_DIAMES':
            case 'LIKE_ONLY':
            case 'NOT_LIKE':
            default:
                $qb->setParameter($fieldP, $filter->val);
                break;
        }

        if ($filter->getOrFilterData()) {
            self::placeValues($qb, $filter->getOrFilterData());
        }
    }
}
