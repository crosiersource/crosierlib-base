<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;

/**
 * Class ChoiceTypeUtils.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils
 * @author Carlos Eduardo Pauluk
 */
class ChoiceTypeUtils
{

    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo ChoiceType utilizando uma
     * função passada como argumento.
     * Ex.:
     *
     * $select2Data = Select2JsUtils::toSelect2Data($array, function ($e) {
     *   return $e->getGroupname() . ' (' . $e->getInserted()->format('d/m/Y') . ')';
     *  });
     *
     * @param array $entities
     * @param \Closure $fn
     * @return array
     * @throws \Exception
     */
    public static function toChoiceTypeChoicesFn(array $entities, \Closure $fn)
    {
        $select2Data = [];
        foreach ($entities as $entity) {
            $text = $fn($entity);
            if ($entity instanceof EntityId) {
                $select2Data[$text] = $entity->getId();
            } else {
                if (!isset($entity['id'])) {
                    throw new \Exception('id não encontrado');
                }
                $select2Data[$text] = $entity['id'];
            }
        }
        return $select2Data;
    }

    /**
     * Transforma um array de entidades em um array de elementos com o formato exigido pelo ChoiceType utilizando um
     * formato e os argumentos que serão passados a função vsprintf().
     *
     * @param array $entities
     * @param string $vsprintfFormat
     * @param string $atributos
     * @return array
     * @throws \Exception
     */
    public static function toChoiceTypeChoices(array $entities, string $vsprintfFormat, array $atributos)
    {
        return ChoiceTypeUtils::toChoiceTypeChoicesFn($entities, function ($e) use ($vsprintfFormat, $atributos) {
            $args = [];
            foreach ($atributos as $atributo) {
                if ($e instanceof EntityId) {
                    $p = (new \ReflectionProperty(get_class($e), $atributo));
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