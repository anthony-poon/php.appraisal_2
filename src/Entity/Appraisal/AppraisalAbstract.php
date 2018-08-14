<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 11/7/2018
 * Time: 6:11 PM
 */

namespace App\Entity\Appraisal;

use App\Entity\Base\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="appraisal")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="appraisal_type", type="string")
 */

abstract class AppraisalAbstract {
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="App\Entity\Base\User", inversedBy="appraisals")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $owner;

	/**
	 * @var AppraisalPeriod
	 * @ORM\ManyToOne(targetEntity="AppraisalPeriod", inversedBy="appraisals")
	 */
    private $period;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="AppraisalResponse", mappedBy="appraisal")
	 */
    private $responses;

	/**
	 * @var array
	 * @ORM\Column(type="json", nullable=true)
	 */
	private $jsonData;

	/**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AppraisalAbstract
     */
    public function setId(int $id): AppraisalAbstract {
        $this->id = $id;
        return $this;
    }

	/**
	 * @return AppraisalPeriod
	 */
	public function getPeriod(): AppraisalPeriod {
		return $this->period;
	}

	/**
	 * @param AppraisalPeriod $period
	 * @return AppraisalAbstract
	 */
	public function setPeriod(AppraisalPeriod $period): AppraisalAbstract {
		$this->period = $period;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getOwner(): User {
		return $this->owner;
	}

	/**
	 * @param User $owner
	 */
	public function setOwner(User $owner): void {
		$this->owner = $owner;
	}

	/**
	 * @return Collection
	 */
	public function getResponses(): ?Collection {
		return $this->responses;
	}

	/**
	 * @return array
	 */
	public function getJsonData(): ?array {
		return json_decode($this->jsonData, true);
	}

	/**
	 * @param array $jsonData
	 * @return AppraisalAbstract
	 */
	public function setJsonData(array $jsonData): ?AppraisalAbstract {
		$this->jsonData = json_encode($jsonData);
		return $this;
	}

	/**
	 * @return int
	 */
	abstract function getScore(): int;

	/**
	 * @return string
	 */
	abstract function getTemplate(): string;
}