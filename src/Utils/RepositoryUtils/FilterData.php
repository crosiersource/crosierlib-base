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

    public $compar;

    public $val;

    public $fieldType;

    /**
     * FilterData constructor.
     * @param $fields
     * @param $compar
     * @param null $viewFieldName
     * @param null $fieldType
     * @param array|null $params
     */
    public function __construct($field, $compar, $viewFieldName = null, ?array $params = null, $fieldType = null)
    {
        // sempre será tratado como array
        $this->field = is_array($field) ? $field : [$field];
        $this->compar = $compar;
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
        $filterData->fieldType = isset($filter['fieldType']) ? $filter['fieldType'] : null;
        $filterData->val = $filter['val'];
        return $filterData;
    }
}