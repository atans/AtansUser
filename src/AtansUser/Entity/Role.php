<?php
namespace AtansUser\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Rbac\Role\HierarchicalRoleInterface;
use ZfcRbac\Permission\PermissionInterface;

/**
 * Role
 *
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(name="atansuser_role", options={"collate"="utf8_general_ci"})
 * @package User\Entity
 */
class Role implements HierarchicalRoleInterface
{
    /**
     * @ORM\Id
     * @ORm\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=48, unique=true)
     * @var string
     */
    protected $name;

    /**
     * @var Role[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *  name="atansuser_role_children",
     *  joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="id")}
     * )
     */
    protected $children = null;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", indexBy="name", inversedBy="permissions", fetch="EAGER")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(
     *  name="atansuser_role_permissions",
     *  joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     * @var Permission[]
     */
    protected $permissions = null;

    public function __construct()
    {
        $this->children    = new ArrayCollection();
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
     * {@inheritDoc}
     */
    public function addChild(HierarchicalRoleInterface $child)
    {
        $this->children[] = $child;
    }

    /**
     * Add children
     *
     * @param Collection $children
     * @return $this
     */
    public function addChildren(Collection $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    /**
     * For doctrine hydrator
     *
     * @param Collection $children
     * @return Role
     */
    public function addChildrens(Collection $children)
    {
        $this->addChildren($children);
        return $this;
    }

    /**
     * Remove child
     *
     * @param HierarchicalRoleInterface $child
     * @return Role
     */
    public function removeChild(HierarchicalRoleInterface $child)
    {
        $this->children->removeElement($child);
        return $this;
    }

    /**
     *  Remove children
     *
     * @param  Collection $children
     * @return Role
     */
    public function removeChildren(Collection $children)
    {
        foreach ($children as $child) {
            $this->removeChild($child);
        }
        return $this;
    }

    /**
     * For doctrine hydrator
     *
     * @param Collection $children
     * @return Role
     */
    public function removeChildrens(Collection $children)
    {
        $this->removeChildren($children);
        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return !$this->children->isEmpty();
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
     * @param  PermissionInterface|string $permission
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
        //$criteria = Criteria::create()->where(Criteria::expr()->eq('name', (string) $permission));
        //$result   = $this->permissions->matching($criteria);
        //return count($result) > 0;
        return isset($this->permissions[(string) $permission]);
    }

    public function __toString()
    {
        return $this->name;
    }
}
