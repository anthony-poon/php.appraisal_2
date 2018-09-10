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
use App\FormType\Form\Appraisal\Version1\FormMainType;
use App\FormType\SurveyForm\Type1;
use App\Service\BaseTemplateHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
				$app->create();
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
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		$app = $appRepo->find($id);
		$user = $this->getUser();
		$context = new ControllerContext();
		$context->setUser($user);
		$context->setParam($request->query->all());
		var_dump($app->read());
		$form = $this->createForm(FormMainType::class, $app->read(), [
			"attr" => [
				"novalidate" => true
			],
			"action" => $this->generateUrl("api_appraisal_view", [
				"id" => $id
			]),
			"controller_context" => $context,
			"disabled" => !$app->isLocked()
		]);

		return $this->render("render/appraisal/view_appraisal.html.twig", [
			"form" => $form->createView(),
		]);
	}

	/**
	 * @Route("/member/api/appraisal/{id}", name="api_appraisal_view", requirements={"id"="\d+"})
	 * @Method({"GET"})
	 */
	public function ajaxGet(int $id) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
		return new JsonResponse($app->read());
	}

	/**
	 * @Route("/member/api/appraisal/{id}", requirements={"id"="\d+"})
	 * @Method({"POST"})
	 */
	public function ajaxPost(int $id, Request $request) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
		$user = $this->getUser();
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
		$form = $this->createForm($app->getTemplate(), $app->read());
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$context = new ControllerContext();
			$context->setUser($user)->setParam($request->query->all());
			$context->setData($form->getData());
			$app->update($context);
			return new JsonResponse($form->getData());
		} else {
			foreach ($form->getErrors(true) as $e) {
				var_dump($e->getOrigin()->createView());
				var_dump($e->getCause());
			}
		}
		//foreach ($rqJson as $fieldName => $value) {
		//	$app->update($user, $role, $fieldName, $value);
		//}
		//$em = $this->getDoctrine()->getManager();
		//$em->persist($app);
		//$em->flush();
		return new JsonResponse([
			"status" => "success"
		]);
	}

	/**
	 * @Route("/member/api/appraisal/{id}", requirements={"id"="\d+"})
	 * @Method({"DELETE"})
	 */
	public function ajaxDelete(int $id, Request $request) {
		$role = $request->request->get("role") ?? "owner";
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
		$user = $this->getUser();
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
		$rqJson = json_decode($request->getContent(), true);
		foreach ($rqJson as $fieldName) {
			$app->delete($user, $role, $fieldName);
		}
	}
}