<?php
namespace AtansUser\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(name="rbac_role", options={"collate"="utf8_general_ci"})
 * @package User\Entity
 */
class Role
{
    /**
     * @ORM\Id
     * @ORm\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @var string
     */
    protected $name = null;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     * @var Role
     */
    protected $parentRole = null;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", inversedBy="roles")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(
     *  name="rbac_role_permission",
     *  joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     * @var Permission[]
     */
    protected $permissions = null;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param  int $id
     * @return Role
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get parentRole
     *
     * @return Role
     */
    public function getParentRole()
    {
        return $this->parentRole;
    }

    /**
     * Set parentRole
     *
     * @param  Role $parentRole
     * @return Role
     */
    public function setParentRole(Role $parentRole)
    {
        $this->parentRole = $parentRole;
        return $this;
    }

    /**
     * Get permissions
     *
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add permission
     *
     * @param Permission $permission
     * @return $this
     */
    public function addPermission(Permission $permission)
    {
        $this->permissions->add($permission);
        return $this;
    }

    /**
     * Add permissions
     *
     * @param  Collection $permissions
     * @return Role
     */
    public function addPermissions(Collection $permissions)
    {
        foreach ($permissions as $permission) {
            $this->permissions->add($permission);
        }
        return $this;
    }

    /**
     * Remove permission
     *
     * @param  Permission $permission
     * @return Role
     */
    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
        return $this;
    }

    /**
     * Remove permissions
     *
     * @param  Collection $permissions
     * @return Role
     */
    public function removePermissions(Collection $permissions)
    {
        foreach ($permissions as $permission) {
            $this->permissions->removeElement($permission);
        }
        return $this;
    }
}
