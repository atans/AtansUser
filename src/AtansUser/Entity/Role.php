<?php
namespace AtansUser\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Rbac\AbstractRole;

/**
 * Role
 *
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(name="rbac_role", options={"collate"="utf8_general_ci"})
 * @package User\Entity
 */
class Role extends AbstractRole
{
    /**
     * @ORM\Id
     * @ORm\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @var Role
     */
    protected $parent = null;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", indexBy="name", inversedBy="permissions")
     * @var PermissionInterface[]|\Doctrine\Common\Collections\Collection
     */
    protected $permissions;

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
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get parent
     *
     * @return Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param  string|Role $parent
     * @return Role
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
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
     * Add a permission
     *
     * @param \ZfcRbac\Permission\PermissionInterface|string $permission
     * @return void
     */
    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $name = $permission;
            $permission = new Permission();
            $permission->setName($name);
        }

        $permission->addRole($this);
        $this->permissions[$permission->getName()] = $permission;
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
            //$this->permissions->add($permission);
            $this->addPermission($permission);
        }
        return $this;
    }

    /**
     * Remove permission
     *
     * @param  \ZfcRbac\Permission\PermissionInterface|string $permission
     * @return Role
     */
    public function removePermission($permission)
    {
        if (is_string($permission)) {
            $this->permissions->remove($permission);
        } else {
            $this->permissions->remove($permission->getName());
        }
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
            //$this->permissions->removeElement($permission);
            $this->removePermission($permission);
        }
        return $this;
    }
}
