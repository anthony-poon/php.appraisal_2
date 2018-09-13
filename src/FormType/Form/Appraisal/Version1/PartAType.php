<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 3/9/2018
 * Time: 6:41 PM
 */

namespace App\FormType\Form\Appraisal\Version1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class PartAType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
        $context = $options["controller_context"] ?? null;
		$builder->add("respon_name", TextareaType::class, [
				"label" => "Key Responsibilities or Objectives",
            "disabled" => $options["role"] === "appraiser"
			])
			->add("respon_result", TextareaType::class, [
				"label" => "Achievements/Results Achieved",
                "disabled" => $options["role"] === "appraiser"
			])
			->add("respon_comment", TextareaType::class, [
				"label" => "Comments by Appraising Officer",
                "disabled" => $options["role"] === "owner"
			])
			->add("respon_weight", IntegerType::class, [
				"label" => "Weight (%)",
                "disabled" => $options["role"] === "owner",
                "constraints" => [
                    new Range([
                        "min" => 1,
                        "max" => 100
                    ])
                ]
			])
			->add("respon_score", IntegerType::class, [
				"label" => "Score",
                "disabled" => $options["role"] === "owner",
                "constraints" => [
                    new Range([
                        "min" => 1,
                        "max" => 5
                    ])
                ]
			])
		;
	}

	public function getBlockPrefix() {
		return "version_1_a";
	}

	public function configureOptions(OptionsResolver $resolver) {
	    $resolver->setDefaults([
	        "role" => "owner"
        ]);
	}


}