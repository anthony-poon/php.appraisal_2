<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 3/9/2018
 * Time: 6:41 PM
 */

namespace App\FormType\Form\Appraisal\Version1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartAType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add("respon_name", TextareaType::class, [
				"label" => "Key Responsibilities or Objectives"
			])
			->add("respon_result", TextareaType::class, [
				"label" => "Achievements/Results Achieved"
			])
			->add("respon_comment", TextareaType::class, [
				"label" => "Comments by Appraising Officer"
			])
			->add("respon_weight", NumberType::class, [
				"label" => "Weight (%)"
			])
			->add("respon_score", NumberType::class, [
				"label" => "Score"
			])
		;
	}

	public function getBlockPrefix() {
		return "version_1_a";
	}

	public function configureOptions(OptionsResolver $resolver) {
	}


}