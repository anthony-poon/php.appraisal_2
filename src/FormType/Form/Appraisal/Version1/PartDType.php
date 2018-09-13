<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 5/9/2018
 * Time: 10:54 AM
 */

namespace App\FormType\Form\Appraisal\Version1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartDType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add("key_respon", TextareaType::class, [
            "disabled" => $options["role"] !== "appraiser"
        ])
			->add("goal_name", TextareaType::class, [
                "disabled" => $options["role"] !== "appraiser"
            ])
			->add("measurement_name", TextareaType::class, [
                "disabled" => $options["role"] !== "appraiser"
            ])
			->add("goal_weight", NumberType::class, [
                "disabled" => $options["role"] !== "appraiser"
            ])
			->add("complete_date", TextType::class, [
                "disabled" => $options["role"] !== "appraiser"
            ])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		parent::buildView($view, $form, $options); // TODO: Change the autogenerated stub
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
            "role" => "owner"
        ]);
	}

	public function getBlockPrefix() {
		return "version_1_d";
	}

}