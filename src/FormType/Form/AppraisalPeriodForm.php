<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 25/7/2018
 * Time: 4:23 PM
 */

namespace App\FormType\Form;


use App\Entity\Appraisal\AppraisalPeriod;
use App\Entity\Appraisal\AppVersion1;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppraisalPeriodForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add("name", TextType::class)
			->add("startDate", DateType::class, array(
				'widget' => 'single_text',
				'input' => 'datetime_immutable',
			))
			->add("endDate", DateType::class, array(
				'widget' => 'single_text',
				'input' => 'datetime_immutable',
			))
			->add("classPath", ChoiceType::class, [
				"choices" => [
					"Version 1" => AppVersion1::class
				],
				"label" => "Version",
			])
			->add("isEnabled", CheckboxType::class, ["label" => "Enabled"])
			->add("submit", SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			"data_class" => AppraisalPeriod::class
		]);
	}


}