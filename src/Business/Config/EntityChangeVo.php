<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

/**
 * @author Carlos Eduardo Pauluk
 */
class EntityChangeVo
{

    public string $entityClass;
    public int $entityId;
    public string $ip;
    public int $changingUserId;
    public string $changingUserUsername;
    public string $changedAt;
    public string $changes;

    public function __construct(
        string $entityClass,
        int    $entityId,
        string $ip,
        int    $changingUserId,
        string $changingUserUsername,
        string $changedAt,
        string $changes
    )
    {
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
        $this->ip = $ip;
        $this->changingUserId = $changingUserId;
        $this->changingUserUsername = $changingUserUsername;
        $this->changedAt = $changedAt;
        $this->changes = $changes;
    }


}