<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\TrackedEntity;
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
 *     denormalizationContext={"groups"={"user","userPassword","entityId"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/sec/user/{id}", "security"="is_granted('ROLE_ADMIN') or object.getId() == user.getId()"},
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
 * @ApiFilter(SearchFilter::class, properties={
 *     "username": "partial",
 *     "nome": "partial",
 *     "email": "partial"
 * })
 *
 * @ApiFilter(BooleanFilter::class, properties={
 *     "isActive"
 * })
 *
 * @ApiFilter(OrderFilter::class, properties={
 *     "id",
 *     "username",
 *     "nome",
 *     "updated",
 *     "isActive"
 * }, arguments={"orderParameterName"="order"})
 *
 *
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Security\UserEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository")
 * @ORM\Table(name="sec_user")
 *
 * @TrackedEntity
 *
 * @author Carlos Eduardo Pauluk
 * @method string getUserIdentifier()
 */
class User implements EntityId, UserInterface, \Serializable
{

    use EntityIdTrait;


    /**
     * @NotUppercase()
     * @ORM\Column(name="username", type="string", length=90, nullable=false)
     * @Groups("user")
     * @var null|string
     */
    public ?string $username = null;

    /**
     * @NotUppercase()
     * @ORM\Column(name="password", type="string", length=90, nullable=false)
     * @Groups("userPassword")
     * @var null|string
     */
    public ?string $password = null;

    /**
     * @NotUppercase()
     * @ORM\Column(name="email", type="string", length=90, nullable=false)
     * @Groups("user")
     * @var null|string
     */
    public ?string $email = null;

    /**
     * @ORM\Column(name="fone", type="string", length=90)
     * @Groups("user")
     * @var null|string
     */
    public ?string $fone = null;

    /**
     * @ORM\Column(name="nome", type="string", length=90, nullable=false)
     * @Groups("user")
     * @var null|string
     */
    public ?string $nome = null;

    /**
     * @ORM\Column(name="descricao", type="string", length=255, nullable=true)
     * @Groups("user")
     * @var null|string
     */
    public ?string $descricao = null;

    /**
     *
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     * @Groups("user")
     * @var null|bool
     */
    public bool $isActive = true;

    /**
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibBaseBundle\Entity\Security\Group")
     * @ORM\JoinColumn(name="group_id", nullable=true)
     * @Groups("user")
     *
     * @var null|Group
     */
    public ?Group $group = null;

    /**
     * Renomeei o atributo para poder funcionar corretamente com o security do Symfony.
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="sec_user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     *
     * @Groups("user")
     */
    private $userRoles = null;

    /**
     * @NotUppercase()
     * @ORM\Column(name="api_token", type="string", length=255, nullable=false)
     * @Groups("userPassword")
     * @var null|string
     */
    public ?string $apiToken = null;

    /**
     * @ORM\Column(name="api_token_expires_at", type="datetime", nullable=false)
     * @Groups("userPassword")
     */
    public ?\DateTime $apiTokenExpiresAt = null;

    /**
     * @NotUppercase()
     * @ORM\Column(name="token_recupsenha", type="string", length=36, nullable=true)
     * @Groups("userPassword")
     * @var null|string
     */
    public ?string $tokenRecupSenha = null;

    /**
     * @ORM\Column(name="dt_valid_token_recupsenha", type="datetime", nullable=false)
     * @Groups("userPassword")
     */
    public ?\DateTime $dtValidadeTokenRecupSenha = null;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    public function setUserRoles(ArrayCollection|array $userRoles): void
    {
        $this->userRoles = is_array($userRoles) ? new ArrayCollection($userRoles) : $userRoles;
    }

    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function getRolesAsArray()
    {
        $roles = [];
        foreach ($this->getUserRoles() as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function addRole(Role $role)
    {
        if (!$this->getUserRoles()->contains($role)) {
            $this->userRoles->add($role);
        }
        return $this;
    }

    public function removeRole(Role $role)
    {
        if ($this->getUserRoles()->contains($role)) {
            $this->userRoles->removeElement($role);
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


    /**
     * @Groups("user")
     * @return string|null
     */
    public function getDescricaoMontada(): ?string
    {
        $d = $this->username . ' - ' . $this->nome;
        if ($this->descricao) {
            $d .= ' (' . $this->descricao . ')';
        }
        return $d;
    }

    /**
     * @Groups("user")
     * @return string|null
     */
    public function getRolesSeparadasPorVirgulas(): ?string
    {
        $roles = $this->getRoles();
        return implode(', ', $roles);
    }

    public function getRoles()
    {
        return $this->getRolesAsArray();
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}

