<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\PushMessage;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade PushMessage.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PushMessageRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return PushMessage::class;
    }


}

