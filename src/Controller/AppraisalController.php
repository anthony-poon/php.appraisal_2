<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 12/7/2018
 * Time: 10:59 AM
 */

namespace App\Controller;


use App\Entity\Appraisal\AppraisalAbstract;
use App\Entity\Appraisal\AppraisalPeriod;
use App\FormType\SurveyForm\Type1;
use App\Service\BaseTemplateHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Base\User;

class AppraisalController extends Controller {
    /**
     * @Route("/member/appraisal", name="appraisal_list")
     */
    public function list(Request $request) {
    	$periodId = $request->query->get("period");
    	if ($periodId) {
    		$repo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
    		$app = $repo->findOneBy([
    			"owner" => $this->getUser()->getId(),
				"period" => $periodId
			]);
    		if (empty($app)) {
				$periodRepo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
				$p = $periodRepo->find($periodId);
				/* @var AppraisalPeriod $p */
				if (empty($p)) {
					throw $this->createNotFoundException("Unable to locate entity.");
				}
				$classPath = $p->getClassPath();
				$app = new $classPath;
				/* @var AppraisalAbstract $app */
				$app->setPeriod($p);
				$app->setOwner($this->getUser());
				$app->initiate();
				$em = $this->getDoctrine()->getManager();
				$em->persist($app);
				$em->flush();
			}
			return $this->redirectToRoute("appraisal_view", [
				"id" => $app->getId(),
				"edit" => true
			]);

		}
    	/* @var User $user */
        $user = $this->getUser();
        $appArr = $user->getAppraisals();
        $list = [];
        $pList = [];
        foreach ($appArr as $app) {
        	/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
        	$list[$app->getPeriod()->getName()] = $app;
		}
		$repo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
        foreach ($repo->findAll() as $p) {
        	/* @var AppraisalPeriod $p */
        	if ($p->isOpen()) {
        		$pList[] = $p;
			}
		}
        return $this->render("render/appraisal/list_appraisal.html.twig", [
            "list" => $list,
			"pList" => $pList
        ]);
    }

	/**
	 * @Route("/member/appraisal/{id}", name="appraisal_view", requirements={"id"="\d+"})
	 */
    public function view(int $id, BaseTemplateHelper $helper, Request $request) {
    	$isEditing = $request->query->get("edit") == true;
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		$app = $appRepo->find($id);
		$helper->addJsParam([
			"apiPath" => $this->generateUrl("api_appraisal_view", [
				"id" => $id
			]),
			"readOnly" => $app->isLocked() || !$isEditing,
			"testing" => "''"
		]);
		return $this->render($app->getTemplate());
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