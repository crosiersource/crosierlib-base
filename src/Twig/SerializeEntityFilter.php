<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class TransliterateFilter
 * @package App\Twig
 */
class SerializeEntityFilter extends AbstractExtension
{



    public function getFilters()
    {
        return [
            new TwigFilter('serializeEntity', [$this, 'serializeEntity']),
        ];
    }

    public function serializeEntity(EntityId $entity)
    {
        $json = EntityIdUtils::serialize($entity);
        return json_encode($json);
    }
}