<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 13/8/2018
 * Time: 5:05 PM
 */

namespace App\Entity\Appraisal;

use App\Entity\Base\User;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Appraisal\AppraisalAbstract;
/**
 * Class AppraisalResponse
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\Table(name="appraisal_response")
 */
class AppraisalResponse {
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="App\Entity\Base\User", inversedBy="id")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $owner;

	/**
	 * @var AppraisalAbstract
	 * @ORM\ManyToOne(targetEntity="AppraisalAbstract", inversedBy="responses")
	 * @ORM\JoinColumn(name="appraisal_id", referencedColumnName="id")
	 */
	private $appraisal;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=256)
	 */
	private $responseType;

	/**
	 * @var array
	 * @ORM\Column(type="json", nullable=true)
	 */
	private $jsonData;

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
	 * @return string
	 */
	public function getResponseType(): string {
		return $this->responseType;
	}

	/**
	 * @param string $responseType
	 */
	public function setResponseType(string $responseType): void {
		$this->responseType = $responseType;
	}

	/**
	 * @return array
	 */
	public function getJsonData(): ?array {
		return json_decode($this->jsonData, true);
	}

	/**
	 * @param array $jsonData
	 * @return AppraisalResponse
	 */
	public function setJsonData(array $jsonData): AppraisalResponse {
		$this->jsonData = json_encode($jsonData);
		return $this;
	}

	/**
	 * @return AppraisalAbstract
	 */
	public function getAppraisal(): AppraisalAbstract {
		return $this->appraisal;
	}

	/**
	 * @param AppraisalAbstract $appraisal
	 * @return AppraisalResponse
	 */
	public function setAppraisal(AppraisalAbstract $appraisal): AppraisalResponse {
		$this->appraisal = $appraisal;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}


}