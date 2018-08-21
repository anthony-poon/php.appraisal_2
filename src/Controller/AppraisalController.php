<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 12/7/2018
 * Time: 10:59 AM
 */

namespace App\Controller;


use App\Entity\Appraisal\AppraisalAbstract;
use App\FormType\SurveyForm\Type1;
use App\Service\BaseTemplateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Base\User;

class AppraisalController extends Controller {
    /**
     * @Route("/member/	appraisal", name="appraisal_list")
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

	/**
	 * @Route("/member/appraisal/{id}", name="appraisal_view", requirements={"id"="\d+"})
	 */
    public function view(int $id, BaseTemplateHelper $helper) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		$app = $appRepo->find($id);
		$helper->addJsParam([
			"apiPath" => $this->generateUrl("api_appraisal_view", [
				"id" => $id
			])
		]);
		if ($app->isLocked()) {
			return $this->render($app->getTemplate());
		}
	}

	/**
	 * @Route("/member/api/appraisal/{id}", name="api_appraisal_view", requirements={"id"="\d+"})
	 */
	public function ajaxViewAppraisal(int $id) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
		return new JsonResponse($app->getRenderData());
	}
}