<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 14/8/2018
 * Time: 3:36 PM
 */

namespace App\Entity\Appraisal;

use App\Entity\Appraisal\AppraisalAbstract;
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
		return "component/appraisal_template/version_1.html.twig";
	}

	function getRenderData(): array {
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
		$json["part_a"] = [];
		$json["part_b1"] = [];
		$json["part_b2"] = [];
		$json["part_d"] = [];

		if ($ownerResponse) {
			$ownJson = $ownerResponse->getJsonData();
			if (isset($ownJson["part_a"])) {
				$json["part_a"] = $ownJson["part_a"];
			}
			if (isset($ownJson["part_b1"])) {
				$json["part_b1"] = $ownJson["part_b1"];
			}
			if (isset($ownJson["part_b2"])) {
				$json["part_b2"] = $ownJson["part_b2"];
			}
			if (isset($ownJson["part_d"])) {
				$json["part_d"] = $ownJson["part_d"];
			}
		}

		if ($appraiserResponse) {
			$appJson = $appraiserResponse->getJsonData();
			if (isset($appJson["part_a"])) {
				$json["part_a"] = array_merge_recursive($json["part_a"], $appJson["part_a"]);
			}
			if (isset($appJson["part_b1"])) {
				$json["part_b1"] = array_merge_recursive($json["part_b1"], $appJson["part_b1"]);
			}
			if (isset($appJson["part_b2"])) {
				$json["part_b2"] = array_merge_recursive($json["part_b2"], $appJson["part_b2"]);
			}
			if (isset($appJson["part_d"])) {
				$json["part_d"] = array_merge_recursive($json["part_d"], $appJson["part_d"]);
			}
		}

		$json["id"] = $this->getId();
		return $json;
	}

	function initiate() {
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


}