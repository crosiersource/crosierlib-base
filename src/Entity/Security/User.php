<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'User'.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"user","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"user"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/sec/user/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"path"="/sec/user/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/sec/user/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/sec/user", "security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"path"="/sec/user", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"username": "exact", "nome": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"id", "username", "nome", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Security\UserEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository")
 * @ORM\Table(name="sec_user")
 * @author Carlos Eduardo Pauluk
 */
class User implements EntityId, UserInterface, \Serializable
{

    use EntityIdTrait;


    /**
     * @NotUppercase()
     * @ORM\Column(name="username", type="string", length=90, unique=true)
     * @Groups("user")
     * @var null|string
     */
    public $username;

    /**
     * @NotUppercase()
     * @ORM\Column(name="password", type="string", length=90)
     * @var null|string
     */
    public $password;

    /**
     * @NotUppercase()
     * @ORM\Column(name="email", type="string", length=90, unique=true)
     * @Groups("user")
     * @var null|string
     */
    public $email;

    /**
     *
     * @ORM\Column(name="nome", type="string", length=90, unique=true)
     * @Groups("user")
     * @var null|string
     */
    public $nome;

    /**
     *
     * @ORM\Column(name="ativo", type="boolean")
     * @Groups("user")
     * @var null|bool
     */
    public $isActive = true;

    /**
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibBaseBundle\Entity\Security\Group")
     * @ORM\JoinColumn(name="group_id", nullable=true)
     * @Groups("user")
     * 
     * @var null|Group
     */
    public $group;

    /**
     * Renomeei o atributo para poder funcionar corretamente com o security do Symfony.
     *
     * @ORM\ManyToMany(targetEntity="Role",cascade={"all"})
     * @ORM\JoinTable(name="sec_user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     * 
     * @Groups("user")
     */
    public $userRoles;

    /**
     * @NotUppercase()
     * @ORM\Column(name="api_token", type="string", length=255, unique=true)
     * @var null|string
     */
    public $apiToken;

    /**
     * @ORM\Column(name="api_token_expires_at", type="datetime", length=255)
     */
    public $apiTokenExpiresAt;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    
    public function getRoles()
    {
        $roles = array();
        foreach ($this->userRoles as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function addRole(Role $role)
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles[] = $role;
        }
        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password
        ));
    }

    public function unserialize($serialized)
    {
        list ($this->id, $this->username, $this->password) = unserialize($serialized, [
            'allowed_classes' => false
        ]);
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    


}

