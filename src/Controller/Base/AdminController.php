<?php

namespace App\Controller\Base;

use App\Entity\Base\SecurityGroup;
use App\Entity\Base\User;
use App\Entity\Base\DirectoryGroup;
use App\FormType\Form\UserGroups\ChooseUserGroupsTypeForm;
use App\FormType\Form\UserGroups\CreateDirectoryGroupsForm;
use App\FormType\Form\UserGroups\SecurityGroupForm;
use App\FormType\Form\Users\CreateUsersForm;
use App\FormType\Form\Users\EditUsersForm;
use App\FormType\Form\UserGroups\EditDirectoryGroupsForm;
use App\Service\BaseTemplateHelper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends Controller {
    public function __construct(BaseTemplateHelper $helper, RouterInterface $router) {
        $helper->setTitle("Administration");
        $helper->addSideMenuItem([
        		"text" => "User Management",
				"url" => $router->generate("admin_list_user")
			])->addSideMenuItem([
				"text" => "Group Management",
				"url" => $router->generate("admin_list_user_group")
			]);
    }

    /**
     * @Route("/admin", name="admin_list_user")
     */
    public function index() {
        return $this->redirectToRoute("admin_list_user");
    }

    /**
     * @Route("/admin/users", name="admin_list_user")
     */
    public function listUser(BaseTemplateHelper $helper, RouterInterface $router) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $userList = $repo->findAll();
        $helper->addJsParam([
            "addPath" => $this->generateUrl("admin_create_user"),
            "editPath" => $router->getRouteCollection()->get("admin_edit_user")->getPath(),
            "deletePath" => $router->getRouteCollection()->get("api_admin_delete_user")->getPath(),
			"uploadPath" => $this->generateUrl("admin_upload_user")
        ]);
        return $this->render('render/admin/list_user.html.twig', [
            "userList" => $userList
        ]);
    }

    /**
     * @Route("/admin/users/create", name="admin_create_user")
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(CreateUsersForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var \App\Entity\Base\User $user */
            $user = $form->getData();
            $pw = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($pw);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("admin_list_user");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Create User",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="admin_edit_user", requirements={"id"="\d+"})
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $encoder, int $id) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        if (!$user) {
            throw new HttpException(404, "Unable to locate entity.");
        }
        $form = $this->createForm(EditUsersForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var \App\Entity\Base\User $user */
            $user = $form->getData();
            $pw = $user->getPlainPassword();
            if ($pw) {
                $pw = $encoder->encodePassword($user, $pw);
                $user->setPassword($pw);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("admin_list_user");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Edit User",
            "form" => $form->createView()
        ]);
    }

	/**
	 * @Route("/admin/user-groups", name="admin_list_user_group")
	 */
    public function listUserGroup(BaseTemplateHelper $helper, RouterInterface $router) {
        $grpRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
        $grpList = $grpRepo->findAll();
        $helper->addJsParam([
        	"addPath" => $this->generateUrl("admin_create_user_group"),
			"editPath" => $router->getRouteCollection()->get("admin_edit_user_group")->getPath(),
			"deletePath" => $router->getRouteCollection()->get("api_admin_delete_user_group")->getPath()
		]);
        return $this->render("render/admin/list_user_group.html.twig", [
        	"grpList" => $grpList
		]);
    }

	/**
	 * @Route("/admin/user-groups/create", name="admin_create_user_group")
	 */
	public function createUserGroup(BaseTemplateHelper $helper, Request $request) {
		$formType = $request->get("t");
		$em = $this->getDoctrine()->getManager();
		switch ($formType) {
			case "directory_group":
				$form = $this->createForm(CreateDirectoryGroupsForm::class);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$group = $form->getData();
					$em->persist($group);
					$em->flush();
					return $this->redirectToRoute("admin_list_user_group");
				}
				break;
			case "security_group":
				$form = $this->createForm(SecurityGroupForm::class);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$group = $form->getData();
					$em->persist($group);
					$em->flush();
					return $this->redirectToRoute("admin_list_user_group");
				}
				break;
			default:
				$form = $this->createForm(ChooseUserGroupsTypeForm::class);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$formType = $form->getData()["groupType"];
					return $this->redirectToRoute("admin_create_user_group", ["t" => $formType]);
				}
				break;
		}
		return $this->render("render/simple_form.html.twig", [
			"title" => "Create Group",
			"form" => $form->createView()
		]);
	}

	/**
	 * @Route("/admin/user-groups/{id}", name="admin_edit_user_group", requirements={"id"="\d+"})
	 */
	public function editUserGroup(BaseTemplateHelper $helper, Request $request, int $id) {
		$em = $this->getDoctrine()->getManager();
		$groupRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
		$group = $groupRepo->find($id);
		switch (get_class($group)) {
			case SecurityGroup::class:
				$form = $this->createForm(SecurityGroupForm::class, $group);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$group = $form->getData();
					$em->persist($group);
					$em->flush();
					$this->redirectToRoute("admin_list_user_group");
				}
				break;
			// Catch all
			case DirectoryGroup::class:
				$form = $this->createForm(EditDirectoryGroupsForm::class, $group);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$group = $form->getData();
					$em->persist($group);
					$em->flush();
					$this->redirectToRoute("admin_list_user_group");
				}
				break;
		}
		return $this->render("render/simple_form.html.twig", [
			"title" => "Edit Group",
			"form" => $form->createView()
		]);
	}

    /**
     * @Route("/api/admin/users/delete",
     *     name="api_admin_delete_user",
     *     methods={"POST", "DELETE"})
     */
    public function deleteUser(Request $request) {
        $idArr = json_decode($request->getContent(), true);
        $userArr = $this->getDoctrine()->getRepository(User::class)->findBy([
            "id" => $idArr
        ]);
        $em = $this->getDoctrine()->getManager();
        foreach ($userArr as $user) {
            $em->remove($user);
        }
        $em->flush();
        return new JsonResponse([
            "status" => "success"
        ]);
    }

	/**
	 * @Route("/api/admin/user-groups/delete",
	 *     name="api_admin_delete_user_group",
	 *     methods={"POST", "DELETE"})
	 */
	public function deleteUserGroup(Request $request) {
		$idArr = json_decode($request->getContent(), true);
		$grpList = $this->getDoctrine()->getRepository(DirectoryGroup::class)->findBy([
			"id" => $idArr
		]);
		$em = $this->getDoctrine()->getManager();
		foreach ($grpList as $group) {
			$em->remove($group);
		}
		$em->flush();
		return new JsonResponse([
			"status" => "success"
		]);
	}
}
