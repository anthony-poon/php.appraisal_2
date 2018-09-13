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
    public function list() {
    	/* @var User $user */
        $user = $this->getUser();
        $list = [];
		$periodRepo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
        $periods = array_reverse($periodRepo->findAll());
		// Get all appraised / countersigned appraisal in the opened period
		foreach ($periods as $p) {
		    /* @var AppraisalPeriod $p */
		    $owned = $p->getAppraisals()->filter(function(AppraisalAbstract $app) use ($user){
		        return $app->getOwner() === $user;
            })->first();
		    if ($owned) {
                $owned = $this->generateUrl("appraisal_view", [
                    "id" => $owned->getId(),
                    "edit" => $p->isOpen()
                ]);
            } else if ($p->isOpen()) {
                $owned = $this->generateUrl("appraisal_create", [
                    "id" => $p->getId(),
                ]);
            }
            $appraisedApp = $p->getAppraisals()->filter(function(AppraisalAbstract $app) use ($user){
                return $app->getOwner()->getAppraisers()->contains($user);
            })->toArray();
            $counteredApp = $p->getAppraisals()->filter(function(AppraisalAbstract $app) use ($user){
                return $app->getOwner()->getCountersigners()->contains($user);
            })->toArray();
            $appList = [];
            foreach ($appraisedApp as $app) {
                /* @var AppraisalAbstract $app */
                $appList[$app->getOwner()->getFullName()] = $this->generateUrl("appraisal_view", [
                    "id" => $app->getId(),
                    "role" => "appraiser",
                    "edit" => $p->isOpen()
                ]);
            }
            if ($p->isOpen()) {
                $existing = array_keys($appList);
                $all = $user->getAppraisees();
                $missing = $all->filter(function(User $u) use ($existing){
                    return !in_array($u->getFullName(), $existing);
                });
                foreach ($missing as $u) {
                    /* @var User $u */
                    if ($u->getIsActive()) {
                        $appList[$u->getFullName()] = null;
                    }
                }
            }
            $counterList = [];
            foreach ($counteredApp as $app) {
                /* @var AppraisalAbstract $app */
                $counterList[$app->getOwner()->getFullName()] = $this->generateUrl("appraisal_view", ["id" => $app->getId(), "role" => "appraiser"]);
            }
            if ($p->isOpen()) {
                $existing = array_keys($counterList);
                $all = $user->getCountersignees();
                $missing = $all->filter(function(User $u) use ($existing){
                    return !in_array($u->getFullName(), $existing);
                });
                foreach ($missing as $u) {
                    /* @var User $u */
                    if ($u->getIsActive()) {
                        $counterList[$u->getFullName()] = null;
                    }
                }
            }
            $list[$p->getName()] = [
                "id" => $p->getId(),
                "owned" => $owned,
                "appraised" => $appList,
                "countered" => $counterList
            ];

        }
        return $this->render("render/appraisal/list_appraisal.html.twig", [
            "list" => $list,
        ]);
    }

    /**
     * @Route("/member/appraisal/create/{id}", name="appraisal_create", requirements={"id"="\d+"})
     */
    public function newAppraisal(int $id) {
        $repo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
        $app = $repo->findOneBy([
            "owner" => $this->getUser()->getId(),
            "period" => $id
        ]);
        $this->denyAccessUnlessGranted("owner", $app);
        if (empty($app)) {
            $periodRepo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
            $p = $periodRepo->find($id);
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

	/**
	 * @Route("/member/appraisal/{id}", name="appraisal_view", requirements={"id"="\d+"})
	 */
    public function view(int $id, BaseTemplateHelper $helper, Request $request) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		$app = $appRepo->find($id);
		$role = $request->query->get("role") ?? "owner";
        $this->denyAccessUnlessGranted($role, $app);
		$isEdit = $request->query->get("edit") == true;
        $context = new ControllerContext();
        $context->setUser($this->getUser());
        $context->setParam($request->query->all());
		$form = $this->createForm(FormMainType::class, $app->read($context), [
			"attr" => [
				"novalidate" => true,
                "data-ajax" => $this->generateUrl("api_appraisal_view", [
                    "id" => $id,
                    "role" => $role
                ])
			],
			"role" => $role,
			"disabled" => !$isEdit || $app->isLocked()
		]);

		return $this->render("render/appraisal/view_appraisal.html.twig", [
			"form" => $form->createView(),
		]);
	}

	/**
	 * @Route("/member/api/appraisal/{id}", name="api_appraisal_view", requirements={"id"="\d+"})
	 * @Method({"GET"})
	 */
	public function ajaxGet(int $id, Request $request) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
        $role = $request->get("role") ?? "owner";
        $this->denyAccessUnlessGranted($role, $app);
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
		$role = $request->get("role") ?? "owner";
		$this->denyAccessUnlessGranted($role, $app);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
        $context = new ControllerContext();
        $context->setUser($user)->setParam($request->query->all());
		$form = $this->createForm($app->getTemplate(), $app->read($context), [
            "role" => $role
        ]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
            $context->setData($form->getData());
			$result = $app->update($context);
            $em = $this->getDoctrine()->getManager();
            $em->persist($app);
            $em->flush();
            return new JsonResponse([
                "status" => "success",
                "data" => $result
            ]);
		} else {
		    $errors = [];
            foreach ($form->getErrors(true) as $e){
                $errors[] = [
                    "source" => $e->getOrigin()->createView()->vars["full_name"],
                    "msg" => $e->getMessage()
                ];
            }
            return new JsonResponse([
                "error" => $errors
            ], 400);
		}
	}

	/**
	 * @Route("/member/api/appraisal/{id}", requirements={"id"="\d+"})
	 * @Method({"DELETE"})
	 */
	public function ajaxDelete(int $id, Request $request) {
		$appRepo = $this->getDoctrine()->getRepository(AppraisalAbstract::class);
		$app = $appRepo->find($id);
		$user = $this->getUser();
        $role = $request->get("role") ?? "owner";
        $this->denyAccessUnlessGranted($role, $app);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		if (empty($app)) {
			$this->createNotFoundException("Unable to locate appraisal");
		}
		$data = json_decode($request->getContent(), true);
		$context = new ControllerContext();
		$context->setUser($user);
		$context->setParam($request->query->all());
		$context->setData($data);
		$app->delete($context);
		$em = $this->getDoctrine()->getManager();
		$em->persist($app);
		$em->flush();
		return new JsonResponse([
		    "status" => "success"
        ]);
	}
}