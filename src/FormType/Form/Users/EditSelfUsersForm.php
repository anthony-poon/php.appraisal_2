<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 23/7/2018
 * Time: 12:11 PM
 */

namespace App\FormType\Form\Users;

use App\Entity\Base\SecurityGroup;
use App\Entity\Base\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditSelfUsersForm extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		/* @var User $user */
		$builder->add("fullName", TextType::class)
			->add("plainPassword", RepeatedType::class, array(
				"type" => PasswordType::class,
				"required" => false,
				"invalid_message" => "Repeat password did not match.",
				"first_options"  => array(
					"required" => false,
					"label" => "Password"
				),
				"second_options" => array(
					"required" => false,
					"label" => "Repeat Password"
				),
			))
			->add('email', EmailType::class)
			->add("submit", SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}