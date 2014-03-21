<?php
namespace AtansUser\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Rbac\AbstractRole;

/**
 * Role
 *
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(name="role", options={"collate"="utf8_general_ci"})
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
     * @ORM\ManyToMany(targetEntity="Permission", indexBy="name", inversedBy="permissions", fetch="LAZY")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(
     *  name="role_permission",
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
     * @param  \ZfcRbac\Permission\PermissionInterface|string $permission
     * @return void
     */
    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $name       = $permission;
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
            $this->addPermission($permission);
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
            $this->removePermission($permission);
        }
        return $this;
    }

    public function hasPermission($permission)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', (string) $permission));
        $result   = $this->permissions->matching($criteria);

        return count($result) > 0;
    }
}
