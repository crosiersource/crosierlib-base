<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;

/**
 * Class EntityIdUtils.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\EntitIdyUtils
 * @author Carlos Eduardo Pauluk
 */
class EntityIdUtils
{

    /**
     * Constrói um array de entidades sendo as chaves as ids.
     * @param array $entities
     * @return array
     */
    public static function buildArrayComIdsComoChaves(array $entities)
    {
        $r = [];
        foreach ($entities as $entity) {
            if ($entity instanceof EntityId) {
                $r[$entity->getId()] = $entity;
            } else {
                $r[$entity['id']] = $entity;

            }
        }
        return $r;
    }

}