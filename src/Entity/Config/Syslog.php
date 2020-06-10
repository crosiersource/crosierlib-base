<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="cfg_syslog")
 *
 * @author Carlos Eduardo Pauluk
 */
class Syslog
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @Groups("entityId")
     */
    public int $id;

    /**
     *
     * @ORM\Column(name="app", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $app = null;

    /**
     *
     * @ORM\Column(name="component", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $component = null;

    /**
     *
     * @ORM\Column(name="act", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $act = null;

    /**
     *
     * @ORM\Column(name="username", type="string")
     * @Groups("entity")
     * @NotUppercase()
     * @var string|null
     */
    public ?string $username = null;

    /**
     *
     * @ORM\Column(name="moment", type="datetime")
     * @Groups("entityId")
     * @var null|\DateTime
     */
    private ?\DateTime $moment = null;

    /**
     *
     * @ORM\Column(name="obs", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $obs = null;

    /**
     *
     * @ORM\Column(name="delete_after", type="datetime")
     * @Groups("entityId")
     * @var null|\DateTime
     */
    public ?\DateTime $deleteAfter = null;

    /**
     *
     * @ORM\Column(name="json_data", type="json")
     * @var null|array
     * @NotUppercase()
     * @Groups("entity")
     */
    public ?array $jsonData = null;


}