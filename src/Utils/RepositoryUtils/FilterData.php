<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils;

/**
 * Class FilterData
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils
 * @author Carlos Eduardo Pauluk
 */
class FilterData
{

    public $field;

    public $filterType;

    public $val;

    public $fieldType;

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
        'BETWEEN' => 2,
        'BETWEEN_DATE' => 2,
        'BETWEEN_MESANO' => 2,
        'BETWEEN_IDADE' => 2
    );

    /**
     * FilterData constructor.
     * @param null $field
     * @param null $filterType
     * @param null $viewFieldName
     * @param array|null $params
     * @param null $fieldType
     */
    public function __construct($field = null, $filterType = 'EQ', $viewFieldName = null, ?array $params = null, $fieldType = null)
    {
        // sempre será tratado como array
        $this->setField($field);
        $this->setFilterType($filterType);
        if (isset($params['filter'][$viewFieldName])) {
            $this->val = $params['filter'][$viewFieldName];
        }
        $this->fieldType = $fieldType;
    }

    /**
     * Cria um FilterData a partir de um array.
     *
     * @param array $filter
     * @return FilterData
     */
    public static function fromArray(array $filter): FilterData
    {
        $filterData = new FilterData($filter['field'], $filter['compar']);
        $filterData->fieldType = $filter['fieldType'] ?? null;
        $filterData->val = $filter['val'];
        return $filterData;
    }

    /**
     * @param mixed $field
     * @return FilterData
     */
    public function setField($field): FilterData
    {
        $field = is_array($field) ? $field : [$field];
        foreach ($field as $k => $f) {
            $field[$k] = strpos($f, '.') === FALSE ? 'e.' . $f : $f;
        }
        $this->field = $field;
        return $this;
    }

    /**
     * @param $filterType
     * @return FilterData
     * @throws ViewException
     */
    public function setFilterType($filterType): FilterData
    {
        if (!array_key_exists($filterType, $this->filterTypes)) {
            throw new \RuntimeException('FilterType não encontrado: ' . $filterType);
        }
        $this->filterType = $filterType;
        return $this;
    }

    /**
     * @param mixed $val
     * @return FilterData
     */
    public function setVal($val): FilterData
    {
        $this->val = $val;
        return $this;
    }

    /**
     * @param string $fieldType
     * @return FilterData
     */
    public function setFieldType(string $fieldType): FilterData
    {
        $this->fieldType = $fieldType;
        return $this;
    }


}