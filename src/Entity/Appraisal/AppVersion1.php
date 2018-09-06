<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 14/8/2018
 * Time: 3:36 PM
 */

namespace App\Entity\Appraisal;

use App\Controller\ControllerContext;
use App\Entity\Appraisal\AppraisalAbstract;
use App\Entity\Base\User;
use App\FormType\Form\Appraisal\Version1\FormMainType;
use Doctrine\ORM\Mapping as ORM;
/**
 * Class AppVersion1
 * @package App\Entity\Appraisal
 * @ORM\Entity()
 * @ORM\Table(name="app_version_1")
 */
class AppVersion1 extends AppraisalAbstract {
	function getScore(): int {
		$json = $this->getJsonData();
		return (int) $json["part_a_b_total"] ?? 0;
	}

	function getTemplate(): string {
		return FormMainType::class;
	}

	function read(ControllerContext $context = null): array {
		$json = $this->getJsonData();
		$owner = $this->getOwner();
		/* @var \App\Entity\Appraisal\AppraisalResponse $ownerResponse */
		$ownerResponse = $this->getResponses()->filter(function (AppraisalResponse $rsp) use ($owner) {
			return $rsp->getOwner() === $owner;
		})->first();
		/* @var \App\Entity\Appraisal\AppraisalResponse $appraiserResponse */
		$appraiserResponse = $this->getResponses()->filter(function (AppraisalResponse $rsp) {
			return $rsp->getResponseType() === "appraiser";
		})->first();

		if ($ownerResponse) {
			$ownJson = $ownerResponse->getJsonData();
			$json = array_replace_recursive($json, $ownJson);
		}

		if ($appraiserResponse) {
			$appJson = $appraiserResponse->getJsonData();
			$json = array_replace_recursive($json, $appJson);
		}

		$json["id"] = $this->getId();
		return $json;
	}

	function create(ControllerContext $context = null) {
		$owner = $this->getOwner();
		$period = $this->getPeriod();
		$appraisers = $owner->getAppraisers();
		$counters = $owner->getCountersigners();
		$appStr = [];
		foreach ($appraisers as $a) {
			$appStr[] = $a->getFullName();
		}
		$appStr = implode(", ", $appStr);
		$coStr = [];
		foreach ($counters as $c) {
			$coStr[] = $c->getFullName();
		}
		$coStr = implode(", ", $coStr);
		$this->setJsonData([
			"form_username" => $owner->getUsername(),
			"survey_period" => $period->getName(),
			"staff_name" => $owner->getFullName(),
			"staff_department" => $owner->getDepartment(),
			"staff_office" => $owner->getOffice(),
			"staff_position" => $owner->getPosition(),
			"appraiser_name" => $appStr,
			"countersigner_name" => $coStr,
			"survey_type" => "Annual Appraisal"
		]);
	}

	function update(ControllerContext $context = null) {
		$user = $context->getUser();
		$role = $context->getParam()["role"] ?? "owner";
		$rsp = $this->getResponses()->filter(function(AppraisalResponse $rsp) use ($user, $role){
			return ($rsp->getOwner()->getId() === $user->getId()) && ($rsp->getResponseType() === $role);
		})->first();
		/* @var $rsp \App\Entity\Appraisal\AppraisalResponse */
		if (!$rsp) {
			$rsp = new AppraisalResponse();
			$rsp->setOwner($user);
			$rsp->setAppraisal($this);
			$rsp->setResponseType($role);
			$this->getResponses()->add($rsp);
		}
		$rspJson = $rsp->getJsonData() ?? [];
		$rsp->setJsonData(array_replace_recursive($rspJson, $context->getData()));
	}

	function delete(ControllerContext $context = null) {
		// TODO: Implement delete() method.
		$rsp = $this->getResponses()->filter(function(AppraisalResponse $rsp) use ($user, $role){
			return ($rsp->getOwner()->getId() === $user->getId()) && ($rsp->getResponseType() === $role);
		})->first();
		/* @var $rsp \App\Entity\Appraisal\AppraisalResponse */
		if (!$rsp) {
			throw new \RuntimeException("Unable to locate responses.");
		}
		$rspJson = $rsp->getJsonData() ?? [];
		$ptr = &$rspJson;
		// Field name of form is prefix[depth_1][depth_2][depth_3]
		// Explode the field name using regex
		if (preg_match_all("/\[([\w\d_\-]+)\]/", $fieldName, $delimited)) {
			// Capture group is stored in [1]
			$captureGrp = $delimited[1];
			for ($i = 0; $i < count($captureGrp); $i++) {
				// If is last element
				if ($i == count($captureGrp) - 1) {
					unset($ptr[$captureGrp[$i]]);
				} else {
					// Walk 1 level deeper
					$ptr = &$ptr[$captureGrp[$i]];
				}
			}
		}
		$rsp->setJsonData(array_replace_recursive($rspJson, $rspJson));
	}

	/**
	 * @return User
	 */
	public function getAppraiser(): User {
		return $this->appraiser;
	}

	/**
	 * @param User $appraiser
	 */
	public function setAppraiser(User $appraiser): void {
		$this->appraiser = $appraiser;
	}

	/**
	 * @return User
	 */
	public function getCounter1(): User {
		return $this->counter1;
	}

	/**
	 * @param User $counter1
	 */
	public function setCounter1(User $counter1): void {
		$this->counter1 = $counter1;
	}

	/**
	 * @return User
	 */
	public function getCounter2(): User {
		return $this->counter2;
	}

	/**
	 * @param User $counter2
	 */
	public function setCounter2(User $counter2): void {
		$this->counter2 = $counter2;
	}
}