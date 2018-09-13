<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 11/7/2018
 * Time: 6:11 PM
 */

namespace App\Entity\Appraisal;

use App\Controller\ControllerContext;
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
	 * @ORM\JoinColumn(name="period_id", referencedColumnName="id")
	 */
    private $period;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="AppraisalResponse",
	 *     mappedBy="appraisal",
	 *     indexBy="owner_id",
	 *     cascade={"persist", "remove"}
	 *	 )
	 */
    private $responses;

	/**
	 * @var string
	 * @ORM\Column(type="json", nullable=true)
	 */
	private $jsonData;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $isLocked = false;

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
	 * @return bool
	 */
	public function isLocked(): bool {
		return $this->isLocked;
	}

	/**
	 * @param bool $isLocked
	 */
	public function setIsLocked(bool $isLocked): void {
		$this->isLocked = $isLocked;
	}

	/**
	 * @return float
	 */
	abstract function getScore():? float;

	/**
	 * @return string
	 */
	abstract function getTemplate(): string;

	abstract function read(ControllerContext $context = null);

	abstract function create(ControllerContext $context = null);

	abstract function update(ControllerContext $context = null);

	abstract function delete(ControllerContext $context = null);
}