<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\StoredViewInfo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade StoredViewInfo.
 *
 * @author Carlos Eduardo Pauluk
 */
class StoredViewInfoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return StoredViewInfo::class;
    }
}
