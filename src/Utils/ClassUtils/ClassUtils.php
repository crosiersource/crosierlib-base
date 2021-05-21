<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\ClassUtils;


/**
 * @author Carlos E Pauluk
 */
class ClassUtils
{

    public static function getClassNameWithoutNamespace($classname)
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }

}
