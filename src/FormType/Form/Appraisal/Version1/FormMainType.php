<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 3/9/2018
 * Time: 6:05 PM
 */

namespace App\FormType\Form\Appraisal\Version1;

use App\FormType\Component\CompositeCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormMainType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$data = $builder->getData();
		$builder->add("staff_name", TextType::class)
			->add("staff_department", TextType::class)
			->add("staff_position", TextType::class)
			->add("staff_office", TextType::class)
			->add("survey_period", TextType::class)
			->add("appraiser_name", TextType::class)
			->add("countersigner_name", TextType::class)
			->add("survey_commencement_date", TextType::class)
			->add("appraisal_type", TextType::class)
			->add("part_a", CompositeCollectionType::class, [
				"entry_type" => PartAType::class,
				"label" => false,
				"force_serial_index" => true
			])
			->add("part_a_overall_score", TextType::class)
			->add("part_a_total", TextType::class)
			->add("countersigner_1_name", TextType::class)
			->add("countersigner_2_name", TextType::class)
			->add("countersigner_1_part_a_score", NumberType::class)
			->add("countersigner_2_part_a_score", NumberType::class)
			->add("part_b1", CollectionType::class, [
				"entry_type" => PartB1Type::class,
				"label" => false
			])
			->add("part_b1_overall_comment", TextareaType::class)
			->add("part_b1_overall_score",NumberType::class)
			->add("countersigner_1_part_b_score", NumberType::class)
			->add("countersigner_2_part_b_score", NumberType::class)
			->add("prof_competency_1", TextType::class, [
				"label" => "1. "
			])
			->add("prof_competency_2", TextType::class, [
				"label" => "2. "
			])
			->add("prof_competency_3", TextType::class, [
				"label" => "3. "
			])
			->add("core_competency_1", ChoiceType::class, [
				"choices" => [
					"Teamwork and Support" => "Teamwork and Support",
					"Ownership" => "Ownership",
					"Customer Focus" => "Customer Focus",
					"Initiative" => "Initiative",
					"Attention to Detail" => "Attention to Detail",
					"Problem Solving and Decision Making" => "Problem Solving and Decision Making",
					"Achieving Results and Compliance" => "Achieving Results and Compliance",
					"Communication and Interpersonal" => "Communication and Interpersonal",
					"Influence, Negotiation and Persuasion" => "Influence, Negotiation and Persuasion",
					"Coaching and Developing" => "Coaching and Developing",
					"Leadership and Strategic Thinking" => "Leadership and Strategic Thinking",
					"Nil" => "Nil",
				],
				"label" => "1. "
			])
			->add("core_competency_2", ChoiceType::class, [
				"choices" => [
					"Teamwork and Support" => "Teamwork and Support",
					"Ownership" => "Ownership",
					"Customer Focus" => "Customer Focus",
					"Initiative" => "Initiative",
					"Attention to Detail" => "Attention to Detail",
					"Problem Solving and Decision Making" => "Problem Solving and Decision Making",
					"Achieving Results and Compliance" => "Achieving Results and Compliance",
					"Communication and Interpersonal" => "Communication and Interpersonal",
					"Influence, Negotiation and Persuasion" => "Influence, Negotiation and Persuasion",
					"Coaching and Developing" => "Coaching and Developing",
					"Leadership and Strategic Thinking" => "Leadership and Strategic Thinking",
					"Nil" => "Nil",
				],
				"label" => "2. "
			])
			->add("core_competency_3", ChoiceType::class, [
				"choices" => [
					"Teamwork and Support" => "Teamwork and Support",
					"Ownership" => "Ownership",
					"Customer Focus" => "Customer Focus",
					"Initiative" => "Initiative",
					"Attention to Detail" => "Attention to Detail",
					"Problem Solving and Decision Making" => "Problem Solving and Decision Making",
					"Achieving Results and Compliance" => "Achieving Results and Compliance",
					"Communication and Interpersonal" => "Communication and Interpersonal",
					"Influence, Negotiation and Persuasion" => "Influence, Negotiation and Persuasion",
					"Coaching and Developing" => "Coaching and Developing",
					"Leadership and Strategic Thinking" => "Leadership and Strategic Thinking",
					"Nil" => "Nil",
				],
				"label" => "3. "
			])
			->add("on_job_0_to_1_year", TextType::class, [
				"label" => "0-1 Year(s)"
			])->add("on_job_1_to_2_year", TextType::class, [
				"label" => "1-2 Year(s)"
			])->add("on_job_2_to_3_year", TextType::class, [
				"label" => "2-3 Year(s)"
			])
			->add("function_training_0_to_1_year", ChoiceType::class, [
				"choices" => [
					"Business Contract Law Knowledge for Non Legal Practitioner" => "Business Contract Law Knowledge for Non Legal Practitioner",
					"Business Law & Practices" => "Business Law & Practices",
					"Business Operations Knowledge (Acquisition of companies or forming strategic alliance)" => "Business Operations Knowledge (Acquisition of companies or forming strategic alliance)",
					"Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)" => "Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)",
					"Business Operations Knowledge (Logistics and Related Legal Requirements)" => "Business Operations Knowledge (Logistics and Related Legal Requirements)",
					"Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)" => "Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)",
					"Business Operations Knowledge (Sales, Marketing and Operations)" => "Business Operations Knowledge (Sales, Marketing and Operations)",
					"Data Protection knowledge in Human Resources Management" => "Data Protection knowledge in Human Resources Management",
					"Data Protection knowledge in Internal IT Management" => "Data Protection knowledge in Internal IT Management",
					"Finance Knowledge for Non-Finance People (General)" => "Finance Knowledge for Non-Finance People (General)",
					"Legal Knowledge on Data Protection" => "Legal Knowledge on Data Protection",
					"Marketing Knowledge (Manganese Market & Product Knowledge)" => "Marketing Knowledge (Manganese Market & Product Knowledge)",
					"Project Management Skills: The Fundamentals and Process" => "Project Management Skills: The Fundamentals and Process",
					"Stakeholder Relationship Skills for Project Managers" => "Stakeholder Relationship Skills for Project Managers",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "0-1 Year(s)"
			])
			->add("function_training_1_to_2_year", ChoiceType::class, [
				"choices" => [
					"Business Contract Law Knowledge for Non Legal Practitioner" => "Business Contract Law Knowledge for Non Legal Practitioner",
					"Business Law & Practices" => "Business Law & Practices",
					"Business Operations Knowledge (Acquisition of companies or forming strategic alliance)" => "Business Operations Knowledge (Acquisition of companies or forming strategic alliance)",
					"Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)" => "Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)",
					"Business Operations Knowledge (Logistics and Related Legal Requirements)" => "Business Operations Knowledge (Logistics and Related Legal Requirements)",
					"Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)" => "Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)",
					"Business Operations Knowledge (Sales, Marketing and Operations)" => "Business Operations Knowledge (Sales, Marketing and Operations)",
					"Data Protection knowledge in Human Resources Management" => "Data Protection knowledge in Human Resources Management",
					"Data Protection knowledge in Internal IT Management" => "Data Protection knowledge in Internal IT Management",
					"Finance Knowledge for Non-Finance People (General)" => "Finance Knowledge for Non-Finance People (General)",
					"Legal Knowledge on Data Protection" => "Legal Knowledge on Data Protection",
					"Marketing Knowledge (Manganese Market & Product Knowledge)" => "Marketing Knowledge (Manganese Market & Product Knowledge)",
					"Project Management Skills: The Fundamentals and Process" => "Project Management Skills: The Fundamentals and Process",
					"Stakeholder Relationship Skills for Project Managers" => "Stakeholder Relationship Skills for Project Managers",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "1-2 Year(s)"
			])
			->add("function_training_2_to_3_year", ChoiceType::class, [
				"choices" => [
					"Business Contract Law Knowledge for Non Legal Practitioner" => "Business Contract Law Knowledge for Non Legal Practitioner",
					"Business Law & Practices" => "Business Law & Practices",
					"Business Operations Knowledge (Acquisition of companies or forming strategic alliance)" => "Business Operations Knowledge (Acquisition of companies or forming strategic alliance)",
					"Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)" => "Business Operations Knowledge (Geology / Mining Industry / Plant Knowledge)",
					"Business Operations Knowledge (Logistics and Related Legal Requirements)" => "Business Operations Knowledge (Logistics and Related Legal Requirements)",
					"Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)" => "Business Operations Knowledge (Production / Acquisition / Developing Mines and Smelting Plants)",
					"Business Operations Knowledge (Sales, Marketing and Operations)" => "Business Operations Knowledge (Sales, Marketing and Operations)",
					"Data Protection knowledge in Human Resources Management" => "Data Protection knowledge in Human Resources Management",
					"Data Protection knowledge in Internal IT Management" => "Data Protection knowledge in Internal IT Management",
					"Finance Knowledge for Non-Finance People (General)" => "Finance Knowledge for Non-Finance People (General)",
					"Legal Knowledge on Data Protection" => "Legal Knowledge on Data Protection",
					"Marketing Knowledge (Manganese Market & Product Knowledge)" => "Marketing Knowledge (Manganese Market & Product Knowledge)",
					"Project Management Skills: The Fundamentals and Process" => "Project Management Skills: The Fundamentals and Process",
					"Stakeholder Relationship Skills for Project Managers" => "Stakeholder Relationship Skills for Project Managers",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "2-3 Year(s)"
			])
			->add("generic_training_0_to_1_year", ChoiceType::class, [
				"choices" => [
					"Team Building and Cross Culture Awareness" => "Team Building and Cross Culture Awareness",
					"Change Management Skills" => "Change Management Skills",
					"Risk and Crisis Management Skills" => "Risk and Crisis Management Skills",
					"Customer Services Skills" => "Customer Services Skills",
					"Self Leadership Skills" => "Self Leadership Skills",
					"Microsoft Excel 2010/2007 VBA Macro Programming Skills" => "Microsoft Excel 2010/2007 VBA Macro Programming Skills",
					"MS Access Fundamentals Skills" => "MS Access Fundamentals Skills",
					"MS Access Skills for Expert User" => "MS Access Skills for Expert User",
					"MS Excel Fundamentals Skills" => "MS Excel Fundamentals Skills",
					"MS Excel Skills for Expert User" => "MS Excel Skills for Expert User",
					"MS PowerPoint Skills for Expert User" => "MS PowerPoint Skills for Expert User",
					"MS Project Fundamentals Skills" => "MS Project Fundamentals Skills",
					"MS Project Skills for Proficient User" => "MS Project Skills for Proficient User",
					"MS Word Fundamentals Skills" => "MS Word Fundamentals Skills",
					"MS Word Skills Expert User" => "MS Word Skills Expert User",
					"Creative Problem Solving and Decision Making Skills" => "Creative Problem Solving and Decision Making Skills",
					"Time Management Skills" => "Time Management Skills",
					"Elementary Putonghua Skills" => "Elementary Putonghua Skills",
					"Phone Manner Skills and Business Etiquette" => "Phone Manner Skills and Business Etiquette",
					"Presentation Skills in English" => "Presentation Skills in English",
					"Spoken Business English and Social English Skills" => "Spoken Business English and Social English Skills",
					"Writing Skills for Business" => "Writing Skills for Business",
					"Writing Skills on Clear Actionable Emails" => "Writing Skills on Clear Actionable Emails",
					"Negotiation and Persuasion Skills for Business Executives" => "Negotiation and Persuasion Skills for Business Executives",
					"Coaching Skills" => "Coaching Skills",
					"Leadership Skills For Supervisor" => "Leadership Skills For Supervisor",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "0-1 Year(s)"
			])
			->add("generic_training_1_to_2_year", ChoiceType::class, [
				"choices" => [
					"Team Building and Cross Culture Awareness" => "Team Building and Cross Culture Awareness",
					"Change Management Skills" => "Change Management Skills",
					"Risk and Crisis Management Skills" => "Risk and Crisis Management Skills",
					"Customer Services Skills" => "Customer Services Skills",
					"Self Leadership Skills" => "Self Leadership Skills",
					"Microsoft Excel 2010/2007 VBA Macro Programming Skills" => "Microsoft Excel 2010/2007 VBA Macro Programming Skills",
					"MS Access Fundamentals Skills" => "MS Access Fundamentals Skills",
					"MS Access Skills for Expert User" => "MS Access Skills for Expert User",
					"MS Excel Fundamentals Skills" => "MS Excel Fundamentals Skills",
					"MS Excel Skills for Expert User" => "MS Excel Skills for Expert User",
					"MS PowerPoint Skills for Expert User" => "MS PowerPoint Skills for Expert User",
					"MS Project Fundamentals Skills" => "MS Project Fundamentals Skills",
					"MS Project Skills for Proficient User" => "MS Project Skills for Proficient User",
					"MS Word Fundamentals Skills" => "MS Word Fundamentals Skills",
					"MS Word Skills Expert User" => "MS Word Skills Expert User",
					"Creative Problem Solving and Decision Making Skills" => "Creative Problem Solving and Decision Making Skills",
					"Time Management Skills" => "Time Management Skills",
					"Elementary Putonghua Skills" => "Elementary Putonghua Skills",
					"Phone Manner Skills and Business Etiquette" => "Phone Manner Skills and Business Etiquette",
					"Presentation Skills in English" => "Presentation Skills in English",
					"Spoken Business English and Social English Skills" => "Spoken Business English and Social English Skills",
					"Writing Skills for Business" => "Writing Skills for Business",
					"Writing Skills on Clear Actionable Emails" => "Writing Skills on Clear Actionable Emails",
					"Negotiation and Persuasion Skills for Business Executives" => "Negotiation and Persuasion Skills for Business Executives",
					"Coaching Skills" => "Coaching Skills",
					"Leadership Skills For Supervisor" => "Leadership Skills For Supervisor",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "1-2 Year(s)"
			])
			->add("generic_training_2_to_3_year", ChoiceType::class, [
				"choices" => [
					"Team Building and Cross Culture Awareness" => "Team Building and Cross Culture Awareness",
					"Change Management Skills" => "Change Management Skills",
					"Risk and Crisis Management Skills" => "Risk and Crisis Management Skills",
					"Customer Services Skills" => "Customer Services Skills",
					"Self Leadership Skills" => "Self Leadership Skills",
					"Microsoft Excel 2010/2007 VBA Macro Programming Skills" => "Microsoft Excel 2010/2007 VBA Macro Programming Skills",
					"MS Access Fundamentals Skills" => "MS Access Fundamentals Skills",
					"MS Access Skills for Expert User" => "MS Access Skills for Expert User",
					"MS Excel Fundamentals Skills" => "MS Excel Fundamentals Skills",
					"MS Excel Skills for Expert User" => "MS Excel Skills for Expert User",
					"MS PowerPoint Skills for Expert User" => "MS PowerPoint Skills for Expert User",
					"MS Project Fundamentals Skills" => "MS Project Fundamentals Skills",
					"MS Project Skills for Proficient User" => "MS Project Skills for Proficient User",
					"MS Word Fundamentals Skills" => "MS Word Fundamentals Skills",
					"MS Word Skills Expert User" => "MS Word Skills Expert User",
					"Creative Problem Solving and Decision Making Skills" => "Creative Problem Solving and Decision Making Skills",
					"Time Management Skills" => "Time Management Skills",
					"Elementary Putonghua Skills" => "Elementary Putonghua Skills",
					"Phone Manner Skills and Business Etiquette" => "Phone Manner Skills and Business Etiquette",
					"Presentation Skills in English" => "Presentation Skills in English",
					"Spoken Business English and Social English Skills" => "Spoken Business English and Social English Skills",
					"Writing Skills for Business" => "Writing Skills for Business",
					"Writing Skills on Clear Actionable Emails" => "Writing Skills on Clear Actionable Emails",
					"Negotiation and Persuasion Skills for Business Executives" => "Negotiation and Persuasion Skills for Business Executives",
					"Coaching Skills" => "Coaching Skills",
					"Leadership Skills For Supervisor" => "Leadership Skills For Supervisor",
					"Others, please specify" => "Others, please specify",
					"Nil" => "Nil",
				],
				"label" => "0-1 Year(s)"
			])
			->add("part_d", CompositeCollectionType::class, [
				"entry_type" => PartDType::class,
				"label" => false,
				"force_serial_index" => true
			])
			->add("part_a_b_total", NumberType::class)
			->add("part_b_total", NumberType::class)
			->add("survey_overall_comment", TextareaType::class)
			->add("submit", SubmitType::class);
		if ($data["is_senior"] ?? false) {
			$builder->add("part_b2", CollectionType::class, [
				"entry_type" => PartB2Type::class,
				"label" => false
			])
				->add("part_b2_overall_comment", TextareaType::class)
				->add("part_b2_overall_score", NumberType::class);
		}
		$role = $options["controller_context"] ?? "owner";
		$disabledFields = [
			"staff_name",
			"staff_department",
			"staff_office",
			"staff_position",
			"appraiser_name",
			"countersigner_name",
			"survey_commencement_date",
			"survey_period",
			"part_a_b_total",
			"part_a_total",
			"part_b_total",
			"part_a_overall_score",
			"part_b1_overall_score",
			"part_b2_overall_score"
		];
		switch($role) {
			case "owner":
				break;
			case "appraiser":
				break;
			case "counter":
				break;
		}
		foreach ($disabledFields as $f) {
			if ($builder->has($f)) {
				$builder->get($f)->setDisabled(true);
			}
		}

	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		parent::buildView($view, $form, $options);
		$json = $form->getData();
		$view->vars["countersigner_1_name"] = $json["countersigner_1_name"];
		$view->vars["countersigner_2_name"] = $json["countersigner_2_name"];
	}


	public function configureOptions(OptionsResolver $resolver) {
		parent::configureOptions($resolver);
		$resolver->setDefaults(["controller_context" => null]);
	}

	public function getBlockPrefix() {
		return "version_1_main";
	}
}