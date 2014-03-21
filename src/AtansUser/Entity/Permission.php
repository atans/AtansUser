<?php
namespace AtansUser\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ZfcRbac\Permission\PermissionInterface;

/**
 * Permission
 *
 * @ORM\Entity(repositoryClass="PermissionRepository")
 * @ORM\Table(
 *  name="permission",
 *  options={"collate"="utf8_general_ci"}
 * )
 */
class Permission implements PermissionInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="permissions")
     * @ORM\OrderBy({"name" = "ASC"})
     * @var Role[]|ArrayCollection
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * @return Permission
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Permission
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get roles
     *
     * @return Role[]|ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add role
     *
     * @param  Role $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);
        return $this;
    }


    /**
     * Add roles
     *
     * @param  Collection $roles
     * @return $this
     */
    public function addRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $role->addPermission($this);
            $this->roles->add($role);
        }
        return $this;
    }

    /**
     * Remove role
     *
     * @param  Role $role
     * @return $this
     */
    public function removeRole(Role $role)
    {
        $this->roles->remove($role);
        return $this;
    }

    /**
     * Remove roles
     *
     * @param Collection $roles
     * @return $this
     */
    public function removeRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $role->removePermission($this);
            $this->roles->remove($role);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->name;
    }
}
