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
		$ownJson = $ownerResponse->getJsonData();
		$appJson = $appraiserResponse->getJsonData();
		if (isset($ownJson["part_a"])) {
			$json["part_a"] = $ownJson["part_a"];
		}
		if (isset($appJson["part_a"])) {
			$json["part_a"] = array_merge_recursive($json["part_a"], $appJson["part_a"]);
		}
		if (isset($ownJson["part_b1"])) {
			$json["part_b1"] = $ownJson["part_b1"];
		}
		if (isset($appJson["part_b1"])) {
			$json["part_b1"] = array_merge_recursive($json["part_b1"], $appJson["part_b1"]);
		}
		if (isset($ownJson["part_b2"])) {
			$json["part_b2"] = $ownJson["part_b2"];
		}
		if (isset($appJson["part_b2"])) {
			$json["part_b2"] = array_merge_recursive($json["part_b2"], $appJson["part_b2"]);
		}
		$json["id"] = $this->getId();
		return $json;
	}
}