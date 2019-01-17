<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils;

class FilterData
{

    public $field;

    public $compar;

    public $val;

    public $fieldType;

    public function __construct($field, $compar, $val, $fieldType = null)
    {
        $this->field = $field;
        $this->compar = $compar;
        $this->val = $val;
        $this->fieldType = $fieldType;
    }
}