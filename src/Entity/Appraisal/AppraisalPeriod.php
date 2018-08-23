<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 24/7/2018
 * Time: 2:03 PM
 */

namespace App\Entity\Appraisal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * Class AppraisalPeriod
 * @package App\Entity
 * @ORM\Table("appraisal_period")
 * @ORM\Entity()
 */
class AppraisalPeriod {
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var \DateTimeImmutable
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $startDate;

	/**
	 * @var \DateTimeImmutable
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $endDate;

	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	private $isEnabled = true;

	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="AppraisalAbstract", mappedBy="period")
	 */
	private $appraisals;

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getStartDate(): \DateTimeImmutable {
		return $this->startDate;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getEndDate(): \DateTimeImmutable {
		return $this->endDate;
	}

	/**
	 * @return Collection
	 */
	public function getAppraisals(): Collection {
		return $this->appraisals;
	}

	/**
	 * @param \DateTimeImmutable $startDate
	 * @return AppraisalPeriod
	 */
	public function setStartDate(\DateTimeImmutable $startDate): AppraisalPeriod {
		$this->startDate = $startDate;
		return $this;
	}

	/**
	 * @param \DateTimeImmutable $endDate
	 * @return AppraisalPeriod
	 */
	public function setEndDate(\DateTimeImmutable $endDate): AppraisalPeriod {
		$this->endDate = $endDate;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return AppraisalPeriod
	 */
	public function setName(string $name): AppraisalPeriod {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->isEnabled;
	}

	/**
	 * @param bool $isEnabled
	 * @return AppraisalPeriod
	 */
	public function setIsEnabled(bool $isEnabled): AppraisalPeriod {
		$this->isEnabled = $isEnabled;
		return $this;
	}


}