<?php

namespace App\Controller\Base;

use App\FormType\Form\Users\EditSelfUsersForm;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile", name="profile_edit")
     */
    public function edit(Request $request) {
        $self = $this->getUser();
        $form = $this->createForm(EditSelfUsersForm::class, $self);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $self = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($self);
            $em->flush();
        }
        return $this->render('render/simple_form.html.twig', [
            "title" => "Profile Management",
            "form" => $form->createView()
        ]);
    }
}
