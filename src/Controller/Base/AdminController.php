<?php

namespace App\Controller\Base;

use App\Entity\Base\SecurityGroup;
use App\Entity\Base\User;
use App\Entity\Base\DirectoryGroup;
use App\FormType\Form\UserGroups\ChooseUserGroupsTypeForm;
use App\FormType\Form\UserGroups\DirectoryGroupsForm;
use App\FormType\Form\UserGroups\SecurityGroupForm;
use App\FormType\Form\Users\CreateUsersForm;
use App\FormType\Form\Users\EditUsersForm;
use App\Service\BaseTemplateHelper;
use App\Service\EntityTableHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends Controller {
    public function __construct(BaseTemplateHelper $helper, RouterInterface $router) {
        $helper->setTitle("Administration");
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
    public function listUser(EntityTableHelper $helper, RouterInterface $router) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        $helper->setAddPath("admin_create_user");
        $helper->setDelPath("admin_delete_user");
        $helper->setEditPath("admin_edit_user");
        $helper->setHeader([
        	"#",
			"Username",
			"Full Name",
			"Email"
		]);
        $helper->setTitle("Users");
        foreach ($users as $u) {
        	/* @var User $u */
			$helper->addRow($u->getId(), [
				$u->getId(),
				$u->getUsername(),
				$u->getFullName(),
				$u->getEmail()
			]);
		}
        return $this->render("render/entity_table.html.twig", $helper->compile());
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
            throw new NotFoundHttpException("Unable to locate entity.");
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
	 * @Route("/admin/users/delete/{id}",
	 *     name="admin_delete_user")
	 */
	public function deleteUser(int $id) {
		$repo = $this->getDoctrine()->getRepository(User::class);
		$user = $repo->find($id);
		if (!$user) {
			throw new NotFoundHttpException("Unable to locate entity.");
		}
		$em = $this->getDoctrine()->getManager();
		$em->remove($user);
		$em->flush($user);
		return $this->redirectToRoute("admin_list_user");
	}

	/**
	 * @Route("/admin/user-groups", name="admin_list_user_group")
	 */
    public function listUserGroup(EntityTableHelper $helper, RouterInterface $router) {
        $grpRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
        $grps = $grpRepo->findAll();
        $helper->setHeader([
        	"#",
			"Group Name",
			"Group Type",
			"Member count"
		]);
        foreach ($grps as $g) {
        	/* @var DirectoryGroup $g */
        	$helper->addRow($g->getId(), [
        		$g->getId(),
				$g->getName(),
				$g->getFriendlyClassName(),
				$g->getChildren()->count()
			]);
		}
        $helper->setAddPath("admin_create_user_group");
        $helper->setEditPath("admin_edit_user_group");
        $helper->setDelPath("admin_delete_user_group");
        $helper->setTitle("User Groups");
        return $this->render("render/entity_table.html.twig", $helper->compile());
    }

	/**
	 * @Route("/admin/user-groups/create", name="admin_create_user_group")
	 */
	public function createUserGroup(BaseTemplateHelper $helper, Request $request) {
		$formType = $request->get("t");
		$em = $this->getDoctrine()->getManager();
		switch ($formType) {
			case "directory_group":
				$form = $this->createForm(DirectoryGroupsForm::class);
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
	public function editUserGroup(Request $request, int $id) {
		$em = $this->getDoctrine()->getManager();
		$groupRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
		$group = $groupRepo->find($id);
		if (empty($group)) {
			throw new NotFoundHttpException("Unable to locate entity.");
		}
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
			default:
				$form = $this->createForm(DirectoryGroupsForm::class, $group);
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
	 * @Route("/admin/user-groups/delete/{id}",
	 *     name="admin_delete_user_group")
	 */
	public function deleteUserGroup(int $id) {
		$repo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
		$grp = $repo->find($id);
		if (!$grp) {
			throw new NotFoundHttpException("Unable to locate entity.");
		}
		$em = $this->getDoctrine()->getManager();
		$em->remove($grp);
		$em->flush();
		return $this->redirectToRoute("admin_list_user_group");
	}
}
