<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;

/**
 * Class Select2JsUtils.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils
 * @author Carlos Eduardo Pauluk
 */
class Select2JsUtils
{

    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo Select2 utilizando um
     * formato e os argumentos que serão passados a função vsprintf().
     *
     * @param array $entities
     * @param string $vsprintfFormat
     * @param array $atributos
     * @param null $entitiesIdsSelecteds
     * @return array
     */
    public static function toSelect2Data(array $entities, string $vsprintfFormat, array $atributos, $entitiesIdsSelecteds = null): array
    {
        return self::toSelect2DataFn(
            $entities,
            function ($e) use ($vsprintfFormat, $atributos) {
                $args = [];
                foreach ($atributos as $atributo) {
                    if ($e instanceof EntityId) {
                        $val = null;
                        $class = new \ReflectionClass($e);
                        $getter = null;
                        foreach ($class->getMethods() as $method) {
                            if (strtoupper($method->getName()) === 'GET' . strtoupper($atributo)) {
                                $getter = $class->getMethod($method->getName());
                                $val = $getter->invoke($e);
                                break;
                            }
                        }
                        if (!$getter) {
                            $val = $e->$atributo;
                        }
                        if (!$val) {
                            throw new \Exception('Impossível obter o valor de ' . $atributo);
                        }
                        $args[] = $val ?? null;
                    } else {
                        $args[] = $e[$atributo] ?? null;
                    }
                }
                return vsprintf($vsprintfFormat, $args);
            },
            $entitiesIdsSelecteds
        );
    }

    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo Select2 utilizando uma
     * função passada como argumento.
     * Ex.:
     *
     * $select2Data = Select2JsUtils::toSelect2DataFn($array, function ($e) {
     *   return $e->getGroupname() . ' (' . $e->getInserted()->format('d/m/Y') . ')';
     *  });
     *
     * @param array $entities
     * @param \Closure|string $keyText
     * @param null $entitiesIdsSelecteds
     * @param $keyId
     * @return array
     */
    public static function toSelect2DataFn(array $entities, $keyText, $entitiesIdsSelecteds = null, $keyId = 'id'): array
    {
        try {
            if ($entitiesIdsSelecteds) {
                if (!is_array($entitiesIdsSelecteds)) {
                    $entitiesIdsSelecteds = [$entitiesIdsSelecteds];
                }
            }
            $select2Data = [];
            foreach ($entities as $entity) {
                if (is_callable($keyText)) {
                    $text = $keyText($entity);
                } else {
                    $text = $entity[$keyText];
                }

                if ($entity instanceof EntityId) {

                    if (is_callable($keyId)) {
                        $id = $keyId($entity);
                    } else {
                        $id = $entity->getId();
                    }

                    $select2Data[] = [
                        'id' => $entity->getId(),
                        'text' => $text,
                        'selected' => $entitiesIdsSelecteds ? in_array($entity->getId(), $entitiesIdsSelecteds, false) : false
                    ];
                } else {

                    if (is_callable($keyId)) {
                        $id = $keyId($entity);
                    } else {
                        $id = $entity[$keyId];
                    }

                    if (!$id) {
                        throw new \RuntimeException('id não encontrado');
                    }
                    $select2Data[] = array_merge([
                        'id' => $id,
                        'text' => $text,
                        'selected' => $entitiesIdsSelecteds ? in_array($id, $entitiesIdsSelecteds, false) : false
                    ], $entity);
                }
            }
            return $select2Data;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo Select2 utilizando um
     * formato e os argumentos que serão passados a função vsprintf().
     *
     * @param array $keyValues
     * @param null $keysSelecteds
     * @param string|null $placeholder
     * @return array
     */
    public static function arrayToSelect2Data(array $keyValues, $keysSelecteds = null, ?string $placeholder = '...'): array
    {
        if ($keysSelecteds) {
            if (!is_array($keysSelecteds)) {
                $keysSelecteds = [$keysSelecteds];
            }
        }

        $rs = [];
        if ($placeholder !== null) {
            $rs[] = [
                'id' => '',
                'text' => $placeholder
            ];
        }
        foreach ($keyValues as $key => $value) {
            $rs[] = [
                'id' => $key,
                'text' => $value,
                'selected' => $keysSelecteds ? in_array($key, $keysSelecteds, false) : false
            ];
        }

        return $rs;
    }


    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo Select2 utilizando um
     * formato e os argumentos que serão passados a função vsprintf().
     *
     * @param array $arr
     * @param null $keysSelecteds
     * @param string|null $placeholder
     * @return array
     */
    public static function arrayToSelect2DataKeyEqualValue(array $arr, $keysSelecteds = null, ?string $placeholder = '...'): array
    {
        if ($keysSelecteds) {
            if (!is_array($keysSelecteds)) {
                $keysSelecteds = [$keysSelecteds];
            }
        }

        $rs = [];
        if ($placeholder !== null) {
            $rs[] = [
                'id' => '',
                'text' => $placeholder,
                'selected' => false
            ];
        }
        foreach ($arr as $value) {
            $rs[] = [
                'id' => $value,
                'text' => $value,
                'selected' => $keysSelecteds ? in_array($value, $keysSelecteds, false) : false
            ];
        }

        return $rs;
    }

}
