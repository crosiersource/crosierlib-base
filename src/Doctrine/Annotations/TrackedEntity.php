<?php

namespace CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * Marca uma entidade para ter suas alterações logadas no cfg_entities_changes
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Carlos Eduardo Pauluk
 */
class TrackedEntity extends Annotation
{

}