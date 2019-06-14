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
     * @return array
     * @throws \Exception
     */
    public static function toSelect2DataFn(array $entities, \Closure $fn, ?array $entitiesIdsSelecteds = [])
    {
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
                    throw new \Exception('id não encontrado');
                }
                $select2Data[] = [
                    'id' => $entity['id'],
                    'text' => $text,
                    'selected' => $entitiesIdsSelecteds ? in_array($entity['id'], $entitiesIdsSelecteds, false) : false
                ];
            }
        }
        return $select2Data;
    }

    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo Select2 utilizando um
     * formato e os argumentos que serão passados a função vsprintf().
     *
     * @param array $entities
     * @param string $vsprintfFormat
     * @param string $atributos
     * @return array
     * @throws \Exception
     */
    public static function toSelect2Data(array $entities, string $vsprintfFormat, array $atributos)
    {
        return Select2JsUtils::toSelect2DataFn($entities, function ($e) use ($vsprintfFormat, $atributos) {
            $args = [];
            foreach ($atributos as $atributo) {
                if ($e instanceof EntityId) {
                    $p = (new \ReflectionProperty($e, $atributo));
                    $p->setAccessible(true);
                    $args[] = $p->getValue($e);
                } else {
                    $args[] = isset($e[$atributo]) ? $e[$atributo] : null;
                }
            }
            return vsprintf($vsprintfFormat, $args);
        });
    }


}