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


}