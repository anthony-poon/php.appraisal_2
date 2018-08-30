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
use Symfony\Component\Validator\Constraints as Assert;
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
	 * @Assert\NotBlank()
	 */
	private $name;

	/**
	 * @var \DateTimeInterface
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\NotBlank()
	 */
	private $startDate;

	/**
	 * @var \DateTimeInterface
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\GreaterThan(propertyPath="startDate", message="End date must be greater than start date.")
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
	 * @var string
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank();
	 */
	private $classPath;

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getStartDate():? \DateTimeInterface {
		if ($this->startDate instanceof \DateTime) {
			return \DateTimeImmutable::createFromMutable($this->startDate);
		} else {
			return $this->startDate;
		}

	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getEndDate():? \DateTimeInterface {
		if ($this->endDate instanceof \DateTime) {
			return \DateTimeImmutable::createFromMutable($this->endDate);
		} else {
			return $this->endDate;
		}
	}

	/**
	 * @return Collection
	 */
	public function getAppraisals(): Collection {
		return $this->appraisals;
	}

	/**
	 * @param \DateTimeInterface $startDate
	 * @return AppraisalPeriod
	 */
	public function setStartDate(\DateTimeInterface $startDate = null): AppraisalPeriod {
		if ($startDate instanceof \DateTime) {
			$this->startDate = \DateTimeImmutable::createFromMutable($startDate);
		} else {
			$this->startDate = $startDate;
		}
		return $this;
	}

	/**
	 * @param \DateTimeInterface $endDate
	 * @return AppraisalPeriod
	 */
	public function setEndDate(\DateTimeInterface $endDate = null): AppraisalPeriod {
		if ($endDate instanceof \DateTime) {
			$this->endDate = \DateTimeImmutable::createFromMutable($endDate);
		} else {
			$this->endDate = $endDate;
		}
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

	/**
	 * @return string
	 */
	public function getClassPath():? string {
		return $this->classPath;
	}

	/**
	 * @param string $classPath
	 * @return AppraisalPeriod
	 */
	public function setClassPath(string $classPath): AppraisalPeriod {
		if (!new $classPath instanceof AppraisalAbstract) {
			throw new \Exception("$classPath not instance of AppraisalAbstract");
		}
		$this->classPath = $classPath;
		return $this;
	}

	public function isOpen() {
		$date = new \DateTime();
		if (empty($this->startDate) || empty($this->endDate)) {
			// Not valid if either missing. For backward compatibility
			return false;
		}
		// Start date inclusive, End date exclusive
		return $this->isEnabled && ($this->startDate <= $date && $date < $this->endDate);
	}
}