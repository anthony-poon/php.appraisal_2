<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 12/7/2018
 * Time: 10:59 AM
 */

namespace App\Controller;


use App\FormType\SurveyForm\Type1;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Base\User;

class AppraisalController extends Controller {
    /**
     * @Route("/member/appraisal-repo/appraisal", name="appraisal_list")
     */
    public function list(Request $request) {
    	/* @var User $user */
        $user = $this->getUser();
        $appArr = $user->getAppraisals();
        $list = [];
        foreach ($appArr as $app) {
        	/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
        	$list[$app->getPeriod()->getName()] = $app;
		}

        return $this->render("render/appraisal/list_appraisal.html.twig", [
            "list" => $list
        ]);
    }
}