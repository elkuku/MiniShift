<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 18/05/17
 * Time: 13:45
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="This user name is already in use")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id;
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $role;

    /**
     * @Assert\Length(max=4096)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $sshKey;

    /**
     * GPG Fingerprint
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gpgFpr;

    /**
     * @var Project[]
     *
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="users")
     * @ORM\JoinTable(
     *  name="user_project",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     *  }
     * )
     */
    protected $projects;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string|null $role
     *
     * @return User
     */
    public function setRole(string $role = null): User
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return [$this->getRole()];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username ?: '';
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword ?: '';
    }

    /**
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }
    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * Constructs the object
     * @link  http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * @param mixed $email
     *
     * @return User
     */
    public function setEmail($email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getSshKey()
    {
        return $this->sshKey;
    }

    /**
     * @param mixed $sshKey
     *
     * @return $this
     */
    public function setSshKey($sshKey): User
    {
        $this->sshKey = $sshKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGpgFpr()
    {
        return $this->gpgFpr;
    }

    /**
     * @param mixed $gpgFpr
     *
     * @return $this
     */
    public function setGpgFpr($gpgFpr): User
    {
        $this->gpgFpr = $gpgFpr;

        return $this;
    }

    /**
     * @param Project $project
     */
    public function addProject(Project $project)
    {
        if ($this->projects->contains($project)) {
            return;
        }

        $this->projects->add($project);
        $project->addUser($this);
    }

    /**
     * @param Project $project
     */
    public function removeProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            return;
        }

        $this->projects->removeElement($project);
        $project->removeUser($this);
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function hasProject(Project $project): bool
    {
        return $this->projects->contains($project);
    }
}
