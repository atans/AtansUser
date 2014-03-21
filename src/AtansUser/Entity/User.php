<?php
namespace AtansUser\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ZfcRbac\Identity\IdentityInterface;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(
 *  name="atansuser_user",
 *  options={"collate"="utf8_general_ci"},
 *  indexes={
 *    @ORM\Index(name="username", columns={"username"}),
 *    @ORM\Index(name="email", columns={"email"})
 *  }
 * )
 */

class User implements IdentityInterface
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_DELETED  = 'deleted';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=128)
     * @var string
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(
     *  name="atansuser_user_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @var Role[]
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", length=20)
     * @var string
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

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
     * Set id
     *
     * @param  int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param  string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param  string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param  string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get roles for RBAC
     *
     * @return array
     */
//    public function getRoles()
//    {
//        $roles = array();
//        if (count($this->userRoles) > 0) {
//            foreach ($this->userRoles as $userRole) {
//                $roles[] = $userRole->getName();
//            }
//        }
//        return $roles;
//    }

    /**
     * Add role
     *
     * @param  Role $role
     * @return User
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
     * @return User
     */
    public function addRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $this->roles->add($role);
        }
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
     * Remove role
     *
     * @param  Role $role
     * @return User
     */
    public function removeRole(Role $role)
    {
        $this->roles->remove($role);
        return $this;
    }

    /**
     * Remove roles
     *
     * @param  Collection $roles
     * @return $this
     */
    public function removeRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $this->roles->removeElement($role);
        }
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param  string $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param  DateTime $created
     * @return User
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }
}
