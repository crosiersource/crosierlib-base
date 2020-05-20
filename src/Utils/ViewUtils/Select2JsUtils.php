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
                        foreach ($class->getMethods() as $method) {
                            if (strtoupper($method->getName()) === 'GET' . strtoupper($atributo)) {
                                $getter = $class->getMethod($method->getName());
                                $val = $getter->invoke($e);
                                break;
                            }
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
     * @param \Closure $fn
     * @param null $entitiesIdsSelecteds
     * @return array
     */
    public static function toSelect2DataFn(array $entities, \Closure $fn, $entitiesIdsSelecteds = null): array
    {
        try {
            if ($entitiesIdsSelecteds) {
                if (!is_array($entitiesIdsSelecteds)) {
                    $entitiesIdsSelecteds = [$entitiesIdsSelecteds];
                }
            }
            $select2Data = [];
            foreach ($entities as $entity) {
                $text = $fn($entity);
                if ($entity instanceof EntityId) {
                    $select2Data[] = [
                        'id' => $entity->getId(),
                        'text' => $text,
                        'selected' => $entitiesIdsSelecteds ? in_array($entity->getId(), $entitiesIdsSelecteds, false) : false
                    ];
                } else {
                    if (!isset($entity['id'])) {
                        throw new \RuntimeException('id não encontrado');
                    }
                    $select2Data[] = [
                        'id' => $entity['id'],
                        'text' => $text,
                        'selected' => $entitiesIdsSelecteds ? in_array($entity['id'], $entitiesIdsSelecteds, false) : false
                    ];
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
     * @param array $entities
     * @param string $vsprintfFormat
     * @param array $atributos
     * @param null $entitiesIdsSelecteds
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
                'selected' => $keysSelecteds ? in_array($entity['id'], $keysSelecteds, false) : false
            ];
        }

        return $rs;
    }

}