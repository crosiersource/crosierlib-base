<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\PushMessageRepository")
 * @ORM\Table(name="cfg_pushmessage")
 *
 * @author Carlos Eduardo Pauluk
 */
class PushMessage implements EntityId
{

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="mensagem", type="string", nullable=false, length=200)
     * @Groups("entity")
     * @NotUppercase()
     *
     * @var null|string
     */
    public ?string $mensagem;

    
    /**
     *
     * @ORM\Column(name="url", type="string", nullable=true, length=2000)
     * @Groups("entity")
     * @NotUppercase()
     *
     * @var null|string
     */
    public ?string $url;


    /**
     * @ORM\Column(name="user_destinatario_id", type="bigint", nullable=false)
     * @Groups("entity")
     *
     * @var null|integer
     */
    public ?int $userDestinatarioId;

    
    /**
     * Data em que a mensagem foi enviada.
     * 
     * @ORM\Column(name="dt_envio", type="datetime", nullable=false)
     * @Groups("entity")
     *
     * @var null|\DateTime
     */
    public ?\DateTime $dtEnvio;

    
    /**
     * Data em que a mensagem foi exibida na notificação.
     * 
     * @ORM\Column(name="dt_notif", type="datetime", nullable=true)
     * @Groups("entity")
     *
     * @var null|\DateTime
     */
    public ?\DateTime $dtNotif;

    
    /**
     * Data em que a mensagem foi aberta na tela de mensagens.
     * 
     * @ORM\Column(name="dt_abert", type="datetime", nullable=true)
     * @Groups("entity")
     *
     * @var null|\DateTime
     */
    public ?\DateTime $dtAbert;


    /**
     * Data de validade da mensagem (após essa data, ela não é mais notificada).
     *
     * @ORM\Column(name="dt_abert", type="datetime", nullable=true)
     * @Groups("entity")
     *
     * @var null|\DateTime
     */
    public ?\DateTime $dtValidade;

    /**
     *
     * @ORM\Column(name="params", type="string", nullable=true, length=5000)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $params;

    


}