<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 25/7/2018
 * Time: 4:23 PM
 */

namespace App\FormType\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppraisalPeriodForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add("name", TextType::class)
			->add("startDate", DateTimeType::class)
			->add("endDate", DateTimeType::class);
	}
}