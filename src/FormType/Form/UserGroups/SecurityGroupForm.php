<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 23/7/2018
 * Time: 1:50 PM
 */

namespace App\FormType\Form\UserGroups;

use App\Entity\Base\DirectoryObject;
use App\FormType\Component\CompositeCollectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SecurityGroupForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$dataObj = $builder->getData();
		$builder->add('name', TextType::class)
			->add("siteToken", TextType::class)
			->add("children", CollectionType::class, [
				// TODO: Better ordering and display
				"entry_type" => EntityType::class,
				"label" => "Members",
				"allow_add" => true,
				"allow_delete" => true,
				"prototype" => true,
				"entry_options" => [
					"class" => DirectoryObject::class,
					"choice_label" => "friendlyName",
					"expanded" => false,
					"multiple" => false,
					"query_builder" => function(EntityRepository $repo) use ($dataObj){
						if ($dataObj) {
							return $repo->createQueryBuilder("do")
								->andWhere('do.id != :id')
								->setParameter("id", $dataObj->getId());
						} else {
							return $repo->createQueryBuilder("do");
						}
					},
					"group_by" => function (DirectoryObject $obj) {
						return $obj->getFriendlyClassName();
					}
				]
			])->add("submit", SubmitType::class);
	}
}