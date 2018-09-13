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
	function getScore():? float {
		$partA = $this->getPartAPostCounter();
		$partB = $this->getPartBPostCounter();
		if ($partA && $partB) {
		    return ($partA + $partB) / 2;
        } else {
		    return null;
        }
	}

	function getTemplate(): string {
		return FormMainType::class;
	}

	function read(ControllerContext $context = null): array {
		$json = $this->getJsonData();
		$json["part_a_overall_score"] = $this->getPartAPreCounter();
		$json["part_a_total"] = $this->getPartAPostCounter();
		$json["part_b1_overall_score"] = $this->getPartB1();
		$json["part_b2_overall_score"] = $this->getPartB2();
		$json["part_b_total"] = $this->getPartBPostCounter();
        $json["part_a_b_total"] = $this->getScore();
		$owner = $this->getOwner();
        /* @var \App\Entity\Appraisal\AppraisalResponse $ownerRsp */
        $ownerRsp = $this->getResponses()->filter(function (AppraisalResponse $rsp) use ($owner) {
            return $rsp->getOwner() === $owner;
        })->first();
        $appraiserId = null;
        $ctnId = null;
		if ($context) {
            $param = $context->getParam();
            $appraiserId = $param["appraiser"] ?? null;
            $ctnId = $param["ctn"] ?? null;
        }
		/* @var \App\Entity\Appraisal\AppraisalResponse $appraiserRsp */
		$appraiserRsp = $this->getResponses()->filter(function (AppraisalResponse $rsp) use ($appraiserId) {
		    if (!$appraiserId) {
                return $rsp->getResponseType() === "appraiser";
            } else {
		        return $rsp->getResponseType() === "appraiser" && $rsp->getOwner()->getId() === $appraiserId;
            }
		})->first();
        /* @var \App\Entity\Appraisal\AppraisalResponse $ctnRsp */
		$ctnRsp = $this->getResponses()->filter(function (AppraisalResponse $rsp) use ($ctnId) {
            if (!$ctnId) {
                return $rsp->getResponseType() === "counter";
            } else {
                return $rsp->getResponseType() === "counter" && $rsp->getOwner()->getId() === $ctnId;
            }
        })->first();
		if ($ownerRsp) {
			$ownJson = $ownerRsp->getJsonData();
			$json = array_replace_recursive($json, $ownJson);
		}

		if ($appraiserRsp) {
			$appJson = $appraiserRsp->getJsonData();
			$json = array_replace_recursive($json, $appJson);
		}
		$json["countersigner_name"] = null;
        if ($ctnRsp) {
            $json["countersigner_name"] = $ctnRsp->getOwner()->getFullName();
            $ctnJson = $ctnRsp->getJsonData();
            $json = array_replace_recursive($json, $ctnJson);
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
		$this->setJsonData([
			"form_username" => $owner->getUsername(),
			"survey_period" => $period->getName() ?? "",
			"staff_name" => $owner->getFullName() ?? "",
			"staff_department" => $owner->getDepartment() ?? "",
			"staff_office" => $owner->getOffice() ?? "",
			"staff_position" => $owner->getPosition() ?? "",
			"survey_commencement_date" => $owner->getCommenceDate()->format("Y-m-d"),
			"appraiser_name" => $appStr ?? "",
			"countersigner_name_all" => implode(", ", $coStr) ?? "",
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
		$formData = $context->getData();
		switch ($role) {
            case "owner":
                $rspJson = [
                    "part_a" => [],
                    "part_b1" => [],
                    "part_b2" => []
                ];
                foreach ($formData["part_a"] as $row) {
                    $rspJson["part_a"][] = [
                        "respon_name" => $row["respon_name"] ?? null,
                        "respon_result" => $row["respon_result"] ?? null,
                    ];
                }
                foreach ($formData["part_b1"] as $row) {
                    $rspJson["part_b1"][] = [
                        "self_example" => $row["self_example"] ?? null,
                        "self_score" => $row["self_score"] ?? null,
                    ];
                }
                if (!empty($formData["part_b2"])) {
                    foreach ($formData["part_b2"] as $row) {
                        $rspJson["part_b2"][] = [
                            "self_example" => $row["self_example"] ?? null,
                            "self_score" => $row["self_score"] ?? null,
                        ];
                    }
                }
                $rsp->setJsonData($rspJson);
                break;
            case "appraiser":
                $rspJson = [
                    "core_competency_1" => $formData["core_competency_1"] ?? null,
                    "core_competency_2" => $formData["core_competency_2"] ?? null,
                    "core_competency_3" => $formData["core_competency_3"] ?? null,
                    "function_training_0_to_1_year" => $formData["function_training_0_to_1_year"] ?? null,
                    "function_training_1_to_2_year" => $formData["function_training_1_to_2_year"] ?? null,
                    "function_training_2_to_3_year" => $formData["function_training_2_to_3_year"] ?? null,
                    "generic_training_0_to_1_year" => $formData["generic_training_0_to_1_year"] ?? null,
                    "generic_training_1_to_2_year" => $formData["generic_training_1_to_2_year"] ?? null,
                    "generic_training_2_to_3_year" => $formData["generic_training_2_to_3_year"] ?? null,
                    "on_job_0_to_1_year" => $formData["on_job_0_to_1_year"] ?? null,
                    "on_job_1_to_2_year" => $formData["on_job_1_to_2_year"] ?? null,
                    "on_job_2_to_3_year" => $formData["on_job_2_to_3_year"] ?? null,
                    "prof_competency_1" => $formData["prof_competency_1"] ?? null,
                    "prof_competency_2" => $formData["prof_competency_2"] ?? null,
                    "prof_competency_3" => $formData["prof_competency_3"] ?? null,
                    "part_b1_overall_comment" => $formData["part_b1_overall_comment"] ?? null,
                    "part_b2_overall_comment" => $formData["part_b2_overall_comment"] ?? null,
                    "survey_overall_comment" => $formData["survey_overall_comment"] ?? null,
                    "part_a" => [],
                    "part_b1" => [],
                    "part_b2" => [],
                    "part_d" => []
                ];
                if (!empty($formData["part_a"])) {
                    foreach ($formData["part_a"] as $row) {
                        $rspJson["part_a"][] = [
                            "respon_comment" => $row["respon_comment"] ?? null,
                            "respon_weight" => $row["respon_weight"] ?? null,
                            "respon_score" => $row["respon_score"] ?? null,
                        ];
                    }
                }
                foreach ($formData["part_b1"] as $row) {
                    $rspJson["part_b1"][] = [
                        "appraiser_example" => $row["appraiser_example"] ?? null,
                        "appraiser_score" => $row["appraiser_score"] ?? null,
                    ];
                }
                if (!empty($formData["part_b2"])) {
                    foreach ($formData["part_b2"] as $row) {
                        $rspJson["part_b2"][] = [
                            "self_example" => $row["self_example"] ?? null,
                            "self_score" => $row["self_score"] ?? null,
                        ];
                    }
                }
                if (!empty($formData["part_d"])) {
                    foreach ($formData["part_d"] as $row) {
                        $rspJson["part_d"][] = [
                            "key_respon" => $row["key_respon"] ?? null,
                            "goal_name" => $row["goal_name"] ?? null,
                            "measurement_name" => $row["measurement_name"] ?? null,
                            "goal_weight" => $row["goal_weight"] ?? null,
                            "complete_date" => $row["complete_date"] ?? null,
                        ];
                    }
                }
                $rsp->setJsonData($rspJson);
                break;
            case "counter":
                $rspJson = [
                    "countersigner_part_a_score" => $formData["countersigner_part_a_score"],
                    "countersigner_part_b_score" => $formData["countersigner_part_b_score"],
                ];
                $rsp->setJsonData($rspJson);
                break;
        }
        $json = [];
        $json["part_a_overall_score"] = $this->getPartAPreCounter();
        $json["part_a_total"] = $this->getPartAPostCounter();
        $json["part_b1_overall_score"] = $this->getPartB1();
        $json["part_b2_overall_score"] = $this->getPartB2();
        $json["part_b_total"] = $this->getPartBPostCounter();
        return $json;
	}

	function delete(ControllerContext $context = null) {
		// TODO: Implement delete() method.
        $user = $context->getUser();
        $role = $context->getParam()["role"] ?? "owner";
        $rsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) use ($user, $role){
			return ($rsp->getResponseType() === "appraiser") || ($rsp->getResponseType() === "owner");
		})->toArray();
		/* @var $rsp \App\Entity\Appraisal\AppraisalResponse */
		if (empty($rsps)) {
			throw new \RuntimeException("Unable to locate responses.");
		}
		switch ($role) {
            case "owner":
            case "appraiser":
                foreach ($rsps as $rsp){
                    $rspJson = $rsp->getJsonData();
                    $name = $context->getData()["name"];
                    $index = $context->getData()["index"];
                    if (empty($name) || empty($index)) {
                        throw new \RuntimeException("Invalid parameter.");
                    }
                    preg_match ("/^[\w_]+\[([\w_]+)\]/", $name, $match);
                    unset($rspJson[$match[1]][$index]);
                    $rsp->setJsonData($rspJson);
                }
                break;
            default:
                throw new \RuntimeException("Invalid parameter.");
                break;
        }
	}

	public function getPartAPreCounter():? float {
	    // TODO: Admin override
        $appRsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) {
            return $rsp->getResponseType() === "appraiser";
        })->toArray();
        $total = 0;
        $count = 0;
        foreach ($appRsps as $rsp) {
            /* @var \App\Entity\Appraisal\AppraisalResponse $rsp */
            $data = $rsp->getJsonData();
            $sum = 0;
            $avg = 0;
            foreach ($data["part_a"] as $row) {
                $weight = (float) $row["respon_weight"];
                $score = (float) $row["respon_score"];
                $avg += $score * ($weight / 100);
                $sum += $weight;
            }
            if ($sum == 100) {
                $total += $avg;
                $count += 1;
            }
        }
        if ($count > 0) {
            return $total / $count;
        } else {
            return null;
        }
    }

    public function getPartAPostCounter():? float {
        // TODO: Admin override
        $preCounter = $this->getPartAPreCounter();
        if ($preCounter === null) {
            return null;
        }
        $rsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) {
            return $rsp->getResponseType() === "counter";
        })->toArray();
        $count = 0;
        $sum = 0;
        foreach ($rsps  as $rsp) {
            /* @var AppraisalResponse $rsp */
            $score = (int) ($rsp->getJsonData()["countersigner_part_a_score"] ?? null);
            $sum  += $score;
            if ($score > 0) {
                $count += 1;
            }
        }
        if ($count > 0) {
            return $preCounter * 0.5 + ($sum / $count);
        } else {
            return $preCounter;
        }
    }

    public function getPartB1():? float {
        // TODO: Admin override
        $appRsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) {
            return $rsp->getResponseType() === "appraiser";
        })->toArray();
        $total = 0;
        $count = 0;
        foreach ($appRsps as $rsp) {
            /* @var \App\Entity\Appraisal\AppraisalResponse $rsp */
            $data = $rsp->getJsonData();
            $avg = 0;
            $isValid = true;
            if (!empty($data["part_b1"])) {
                foreach ($data["part_b1"] as $row) {
                    if ($isValid && $row["appraiser_score"]) {
                        $score = (float)$row["appraiser_score"];
                        $avg += $score / count($data["part_b1"]);
                    } else {
                        $isValid = false;
                    }
                }
                if ($isValid) {
                    $total += $avg;
                    $count += 1;
                }
            }
        }
        if ($count > 0) {
            return $total / $count;
        } else {
            return null;
        }
    }

    public function getPartB2():? float {
        $appRsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) {
            return $rsp->getResponseType() === "appraiser";
        })->toArray();
        $total = 0;
        $count = 0;
        foreach ($appRsps as $rsp) {
            /* @var \App\Entity\Appraisal\AppraisalResponse $rsp */
            $data = $rsp->getJsonData();
            $avg = 0;
            if (!empty($data["part_b2"])) {
                $isValid = true;
                foreach ($data["part_b2"] as $row) {
                    if ($isValid && $row["appraiser_score"]) {
                        $score = (float) $row["appraiser_score"];
                        $avg += $score / count($data["part_b2"]);
                    } else {
                        $isValid = false;
                    }
                }
                if ($isValid) {
                    $total += $avg;
                    $count += 1;
                }
            }
        }
        if ($count > 0) {
            return $total / $count;
        } else {
            return null;
        }
    }

    public function getPartBPreCounter():? float {
	    $data = $this->getJsonData();
	    if ($data["is_senior"] ?? false)	{
	        return $this->getPartB1() * 0.6 + $this->getPartB2() * 0.4;
        } else {
            return $this->getPartB1();
        }
    }

    public function getPartBPostCounter():? float {
        $preCounter = $this->getPartBPreCounter();
        if ($preCounter === false) {
            return false;
        }
        $rsps = $this->getResponses()->filter(function(AppraisalResponse $rsp) {
            return $rsp->getResponseType() === "counter";
        })->toArray();
        $count = 0;
        $sum = 0;
        foreach ($rsps  as $rsp) {
            /* @var AppraisalResponse $rsp */
            $score = (int) ($rsp->getJsonData()["countersigner_part_b_score"] ?? null);
            $sum  += $score;
            if ($score > 0) {
                $count += 1;
            }
        }
        if ($count > 0) {
            return $preCounter * 0.5 + ($sum / $count) * 0.5;
        } else {
            return $preCounter;
        }
    }

}