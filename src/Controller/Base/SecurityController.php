<?php

namespace App\Controller\Base;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Service\BaseTemplateHelper;

class SecurityController extends Controller {
    public function __construct(BaseTemplateHelper $helper) {
        $helper->setTitle("Login");
    }
    /**
     * @Route("/security/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) {
        $form = $this->createFormBuilder()
            ->add("username", TextType::class, array(
                "attr" => array(
                    "name" => "_username"
                )
            ))
            ->add("password", PasswordType::class)
            ->add("submit", SubmitType::class)
            ->getForm();
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $form->addError(new FormError($error->getMessage()));
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('render/login.html.twig', array(
            "form" => $form->createView(),
            "last_username" => $lastUsername,
            "error" => $error,
        ));
    }
}
