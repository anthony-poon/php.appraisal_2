<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 23/8/2018
 * Time: 5:08 PM
 */

namespace App\Controller;

use App\Entity\Appraisal\AppraisalPeriod;
use App\FormType\Form\AppraisalPeriodForm;
use App\Service\EntityTableHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppraisalPeriodController extends Controller {
	/**
	 * @Route("/admin/period", name="appraisal_period_list_period")
	 */
	public function listPeriod(EntityTableHelper $helper) {
		$repo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
		$periods = $repo->findAll();
		$helper->setAddPath("appraisal_period_create_period");
		$helper->setDelPath("appraisal_period_delete_period");
		$helper->setEditPath("appraisal_period_edit_period");
		$helper->setHeader([
			"#",
			"Name",
			"Start Date",
			"End Date"
		]);
		$helper->setTitle("Users");
		foreach ($periods as $p) {
			/* @var AppraisalPeriod $p */
			$helper->addRow($p->getId(), [
				$p->getId(),
				$p->getName(),
				$p->getStartDate(),
				$p->getEndDate(),
			]);
		}
		return $this->render("render/entity_table.html.twig", $helper->compile());
	}

	/**
	 * @Route("/admin/period/create", name="appraisal_period_create_period")
	 */
	public function createPeriod(Request $request) {
		$form = $this->createForm(AppraisalPeriodForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/* @var \App\Entity\Appraisal\AppraisalPeriod $p */
			$p = $form->getData();
			$em = $this->getDoctrine()->getManager();
			$em->persist($p);
			$em->flush();
			return $this->redirectToRoute("appraisal_period_list_period");
		}
		return $this->render("render/simple_form.html.twig", [
			"form" => $form->createView(),
			"title" => "Create Appraisal Period"
		]);
	}

	/**
	 * @Route("/admin/period/edit/{id}", name="appraisal_period_edit_period", requirements={"id": "\d+"})
	 */
	public function editPeriod(Request $request, int $id) {
		$repo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
		$p = $repo->find($id);
		if (empty($p)) {
			throw $this->createNotFoundException("Unable to location entity");
		}
		$form = $this->createForm(AppraisalPeriodForm::class, $p);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$p = $form->getData();
			$em = $this->getDoctrine()->getManager();
			$em->persist($p);
			$em->flush();
			return $this->redirectToRoute("appraisal_period_list_period");
		}
		return $this->render("render/simple_form.html.twig", [
			"form" => $form->createView(),
			"title" => "Edit Appraisal Period"
		]);
	}

	/**
	 * @Route("/admin/period/delete/{id}", name="appraisal_period_delete_period", requirements={"id": "\d+"})
	 */
	public function deletePeriod(int $id) {
		$repo = $this->getDoctrine()->getRepository(AppraisalPeriod::class);
		$p = $repo->find($id);
		if (empty($p)) {
			throw $this->createNotFoundException("Unable to location entity");
		}
		$em = $this->getDoctrine()->getManager();
		$em->remove($p);
		$em->flush();
		return $this->redirectToRoute("appraisal_period_list_period");
	}
}