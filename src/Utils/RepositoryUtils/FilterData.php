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
    public function __construct($field, $compar, $viewFieldName = null, $fieldType = null, ?array $params = null)
    {
        $this->field = $field;
        $this->compar = $compar;
        if (isset($params['filter'][$viewFieldName])) {
            $this->val = $params['filter'][$viewFieldName];
        }
        $this->fieldType = $fieldType;
    }
}