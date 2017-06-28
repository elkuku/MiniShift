<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 25/05/17
 * Time: 11:19
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Project
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @UniqueEntity(fields="name", message="There is already a project with this name.")
 */
class Project
{
    public $dir = '';
    public $gitDir = '';
    public $hasWorkDir = '';
    public $hasWebDir = '';
    public $cloneHost = '';
    public $cloneIp = '';

    /**
     * @ORM\Id;
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $name;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="projects")
     */
    protected $users;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
        $user->addProject($this);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->removeElement($user);
        $user->removeProject($this);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    /**
     * @param mixed $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getUsersList(): string
    {
        $list = [];

        foreach ($this->users as $user) {
            $list[] = $user->getUsername();
        }

        return implode(', ', $list);
    }
}
