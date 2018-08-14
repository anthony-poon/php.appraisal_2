<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 18/5/2018
 * Time: 5:09 PM
 */

namespace App\Entity\Base;

use App\Entity\Department;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="app_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username", message="Username is taken already")
 * @UniqueEntity("email", message="Email is registered already")
 */
class User extends DirectoryObject implements UserInterface, \Serializable {
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *      pattern="/^[\w_\.]+$/",
     *      message="Username contained invalid character"
     * )
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(
     *     max=4096,
     *     min=5,
     *     maxMessage="Password too long",
     *     minMessage="Password too short (5 characters or more)"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=128, unique=true, nullable=True)
     * @Assert\Email()
     */
    private $email = Null;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = True;

	/**
	 * Reference to the security group which this object is an immediate member
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="SecurityGroup", mappedBy="children")
	 */
	private $securityGroups;

	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="appraisers", cascade={"persist"})
	 */
	private $appraisees;


	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="appraisees")
	 * @ORM\JoinTable(name="user_appraiser_mapping",
	 *	 joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="appraiser_id", referencedColumnName="id")
	 *	 })
	 */
	private $appraisers;

	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="countersigners", cascade={"persist"})
	 */
	private $countersignees;

	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="countersignees")
	 * @ORM\JoinTable(name="user_countersigner_mapping",
	 *	 joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="countersigner_id", referencedColumnName="id")
	 *	 })
	 */
	private $countersigners;

	/**
	 * @ORM\Column(name="is_senior", type="boolean")
	 */
	private $isSenior = False;

	/**
	 * @var Department
	 * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="children")
	 * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
	 */
	private $department;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="App\Entity\Appraisal\AppraisalAbstract", mappedBy="owner")
	 */
	private $appraisals;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="App\Entity\Appraisal\AppraisalResponse", mappedBy="owner")
	 */
	private $reponses;

	public function __construct() {
		parent::__construct();
		$this->appraisees = new ArrayCollection();
		$this->appraisers = new ArrayCollection();
		$this->countersignees = new ArrayCollection();
		$this->countersigners = new ArrayCollection();
	}

	public function serialize() {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    public function unserialize($serialized) {
        list($this->id, $this->username, $this->password) = unserialize($serialized, [
            "allowed_classes" => false
        ]);
    }

    /**
     * @return array
     */
    public function getRoles(): array {
    	// Cannot use this->getSecurityGroup because it only get immediate parents
    	$parents = new ArrayCollection($this->getParentsRecursive());
    	$parents = $parents->filter(function(DirectoryGroup $g) {
    		return $g instanceof SecurityGroup;
		});
    	$rtn = [
    		"ROLE_USER"
		];
    	foreach ($parents as $g) {
    		/* @var SecurityGroup $g */
			$rtn[] = $g->getSiteToken();
		}
        return $rtn;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        return Null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
    	$this->plainPassword = "";
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getIsActive(): ?bool {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword(string $plainPassword): self {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): ?string {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return User
     */
    public function setFullName(string $fullName): User {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getSecurityGroups(): ?Collection {
		return $this->securityGroups;
	}

	public function setSecurityGroups($groups): User {
    	if ($groups instanceof Collection) {
			$this->securityGroups = $groups;
		} else {
			$this->securityGroup = new ArrayCollection($groups);
		}
    	return $this;
	}

	public function getFriendlyName(): string {
		return $this->fullName;
	}

	public function getFriendlyClassName(): string {
		return "User";
	}

	/**
	 * @return Collection
	 */
	public function getAppraisees(): Collection {
		return $this->appraisees;
	}

	/**
	 * @return Collection
	 */
	public function getAppraisers(): Collection {
		return $this->appraisers;
	}

	/**
	 * @return Collection
	 */
	public function getCountersignees(): Collection {
		return $this->countersignees;
	}

	/**
	 * @return Collection
	 */
	public function getCountersigners(): Collection {
		return $this->countersigners;
	}

	/**
	 * @return bool
	 */
	public function isSenior():bool {
		return $this->isSenior;
	}

	/**
	 * @param bool $isSenior
	 */
	public function setIsSenior(bool $isSenior): void {
		$this->isSenior = $isSenior;
	}

	/**
	 * @return Department
	 */
	public function getDepartment(): Department {
		return $this->department;
	}

	/**
	 * @param Department $department
	 */
	public function setDepartment(Department $department): void {
		$this->department = $department;
	}

	/**
	 * @return Collection
	 */
	public function getAppraisals(): Collection {
		return $this->appraisals;
	}

	/**
	 * @return Collection
	 */
	public function getReponses(): Collection {
		return $this->reponses;
	}


}