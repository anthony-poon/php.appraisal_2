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
		$builder->add("staff_name", TextType::class, [
                "disabled" => true
            ])
			->add("staff_department", TextType::class, [
                "disabled" => true
            ])
			->add("staff_position", TextType::class, [
                "disabled" => true
            ])
			->add("staff_office", TextType::class, [
                "disabled" => true
            ])
			->add("survey_period", TextType::class, [
                "disabled" => true
            ])
			->add("appraiser_name", TextType::class, [
                "disabled" => true
            ])
			->add("countersigner_name_all", TextType::class, [
                "disabled" => true
            ])
			->add("survey_commencement_date", TextType::class, [
                "disabled" => true
            ])
			->add("appraisal_type", TextType::class, [
                "disabled" => true
            ])
			->add("part_a", CompositeCollectionType::class, [
				"entry_type" => PartAType::class,
				"label" => false,
				"force_serial_index" => true,
                "entry_options" => [
                    "role" => $options["role"]
                ]
			])
			->add("part_a_overall_score", NumberType::class, [
                "disabled" => true,
                "scale" => 2
            ])
			->add("part_a_total", NumberType::class, [
                "disabled" => true,
                "scale" => 2
            ])
			->add("countersigner_part_a_score", NumberType::class, [
                "disabled" => $options["role"] !== "counter",
                "scale" => 2
            ])
			->add("part_b1_0", PartB1Type::class, [
				"label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][0]",
                "title" => "Teamwork and Support",
                "lhs_text" => [
                    "5. Fosters team spirit, encourages others to contribute and draws on wide variety of others' skills to achieve team success.",
                    "4. Cooperates with colleagues, willingly shares team values, listens, makes a constructive contribution to teams and builds on team success.",
                    "3. Liaises with colleagues, willingly shares team information and knowledge and makes a constructive contribution to teams. Recognize one's limit and seek for support without delay.",
                    "2. Did not demonstrate the willingness to work amicably with colleagues or proactively support others in times of need.",
                    "1. Behaves in a disruptive manner within team, is confrontational and negatively criticises others and their contributions. Not considered a team worker."
                ]
			])
            ->add("part_b1_1", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][1]",
                "title" => "Ownership",
                "lhs_text" => [
                    "5. Has a record of taking ownership for major problems, crises and issues and ensuring timely and well judged decisions are made and involving others as necessary.",
                    "4. Has a record of taking ownership for customer problems, team goals and challenging objectives and seeks assistance whenever appropriate.",
                    "3. Has a limited record of taking ownership for own decisions and outcomes and does not depend unduly on others, however, knows when to ask for assistance.",
                    "2. Has not demonstrated ownership.",
                    "1. Ignores potential problems, 'not my problem attitude', blames others for problems rather than helps to resolve problems."
                ]
            ])
            ->add("part_b1_2", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][2]",
                "title" => "Customer Focus",
                "lhs_text" => [
                    "5. Exceeds customers' expectations, develops mutually beneficial relationships with customers.",
                    "4. Has an in-depth understanding of customer needs (gained via experience and research), use this build customer confidence, to develop improvements in customer service levels and relationships.",
                    "3. Has correct understanding of customer needs, received good customer feedback, responding appropriately to customer issues and displays a concern to improve customer service levels.",
                    "2. Has no record of working with internal or external customers.",
                    "1. Has no observable desire to provide service to others. Past customers have requested that this person does no further work/is removed from site."
                ]
            ])
            ->add("part_b1_3", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][3]",
                "title" => "Initiative",
                "lhs_text" => [
                    "5. Has a record of creating, seizing and driving new ideas and opportunities to implementation.",
                    "4. Anticipates problems and takes preemptive action to deal with them, has a record of evaluating problems and developing more effective ways of doing things.",
                    "3. Gets on with jobs, does not need asking to do things and generates ideas for helping to resolve issues.",
                    "2. No evidence of using initiative and seizing opportunities to take action.",
                    "1. Shows no initiative at all, has to be asked to do things and requires supervision and guidance or set procedures to follow."
                ]
            ])
            ->add("part_b1_4", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][4]",
                "title" => "Attention to Detail",
                "lhs_text" => [
                    "5. Consistently high standard with work right first time, sets an example to others and source of advice and guidance.",
                    "4. Shows concern for quality, produces high quality work which is mostly right first time.",
                    "3. Concentrates, checks that work is accurate, make few mistakes and learns from them. Seeks advice/help as appropriate.",
                    "2. No evidence of concern for quality of the job.",
                    "1. Makes careless and simple mistakes, work in generally sloppy and has to be checked or re-worked, shows no concern for quality standards. Mistakes have impact on service quality."
                ]
            ])
            ->add("part_b1_5", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][5]",
                "title" => "Problem Solving and Decision Making",
                "lhs_text" => [
                    "5. Has record of developing timely solutions for major problems, looks at wider issues, is creative and uses wide range of tools and sources to develop solutions.",
                    "4. Has record of analysing and developing solutions to complex problems, searches widely for options, aware and proficient in a variety of techniques. Offered new ideas and solutions that are not tied to past method and result in order to increase the value of work.",
                    "3. Has record of handling straight forward problems and developing workable solutions including but not limited to reorganize work unit structure, job assignment or resources. Offered constructive and practical suggestions to tackle work problems.",
                    "2. No evidence of successful problem solving skills and not willing to handle challenging tasks or accept changes in role or situation.",
                    "1. Is generally unsuccessful in solving problems or takes longer than necessary even with straight forward problems. No concept of whom to ask for support/advice and can handle ordinary routine works only."
                ]
            ])
            ->add("part_b1_6", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][6]",
                "title" => "Achieving Results and Compliance",
                "lhs_text" => [
                    "5. Has a record of achieving nearly all goals set on schedule, in budget, and anticipating and managing complexities, changing priorities and needs - 80/20%, while the tasks completed are complying the Company goals, quality objectives, policies and procedures.",
                    "4. Has a record of mostly achieving goals agreed or set in budget and generally on schedule - 70/30%, while the tasks completed are complying the Company goals, quality objectives, policies and procedures.",
                    "3. Has a record of generally achieving goals agreed - 60/40%, while the tasks completed are complying the quality objectives, policies and procedures.",
                    "2. Not able to demonstrate record of achieving results or more than half of tasks completed are not complying the quality objectives, policies and procedures.",
                    "1. Fails to achieve own goals and hinders results of others."
                ]
            ])
            ->add("part_b1_7", PartB1Type::class, [
                "label" => false,
                "role" => $options["role"],
                "property_path" => "[part_b1][7]",
                "title" => "Communication and Interpersonal",
                "lhs_text" => [
                    "5. Highly articulate, using appropriate language and communication styles at all times, listening and feeding back to show understanding.",
                    "4. Listens and appropriately tailors communication approach to suit situation or person. Engages the enthusiastic cooperation and wholehearted participation of others in work tasks and relationships.",
                    "3. Regularly reports and updates on progress of responsible task, problems and achievements expected by the supervisors. Communicates clearly and concisely, both verbally and written, ensuring information relayed is accurate, listens to what is being communicated and seeks to understand by solid questioning skills.",
                    "2. No evidence of ability to give and receive information with accuracy, cannot explain one's idea and thoughts for acceptance by others.",
                    "1. Fails to communicate clearly, struggles to put points across verbally or written"
                ]
            ])
			->add("part_b1_overall_comment", TextareaType::class, [
                "disabled" => $options["role"] !== "appraiser"
            ])
			->add("part_b1_overall_score",NumberType::class, [
                "disabled" => true,
                "scale" => 2
            ])
			->add("countersigner_part_b_score", NumberType::class, [
                "disabled" => $options["role"] !== "counter",
                "scale" => 2
            ])
			->add("prof_competency_1", TextType::class, [
				"label" => "1. ",
                "disabled" => $options["role"] !== "appraiser"
			])
			->add("prof_competency_2", TextType::class, [
				"label" => "2. ",
                "disabled" => $options["role"] !== "appraiser"
			])
			->add("prof_competency_3", TextType::class, [
				"label" => "3. ",
                "disabled" => $options["role"] !== "appraiser"
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
				"label" => "1. ",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "2. ",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "3. ",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
			])
			->add("on_job_0_to_1_year", TextType::class, [
				"label" => "0-1 Year(s)",
                "disabled" => $options["role"] !== "appraiser"
			])->add("on_job_1_to_2_year", TextType::class, [
				"label" => "1-2 Year(s)",
                "disabled" => $options["role"] !== "appraiser"
			])->add("on_job_2_to_3_year", TextType::class, [
				"label" => "2-3 Year(s)",
                "disabled" => $options["role"] !== "appraiser"
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
				"label" => "0-1 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "1-2 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "2-3 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "0-1 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "1-2 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
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
				"label" => "0-1 Year(s)",
                "disabled" => $options["role"] !== "appraiser",
                "placeholder" => "Choose an option"
			])
			->add("part_d", CompositeCollectionType::class, [
				"entry_type" => PartDType::class,
				"label" => false,
				"force_serial_index" => true,
                "disabled" => $options["role"] !== "appraiser",
                "entry_options" => [
                    "role" => $options["role"]
                ]
			])
			->add("part_a_b_total", NumberType::class, [
			    "disabled" => true,
                "scale" => 2
            ])
			->add("part_b_total", NumberType::class, [
                "disabled" => true,
                "scale" => 2
            ])
			->add("survey_overall_comment", TextareaType::class, [
                "disabled" => $options["role"] === "owner" || $options["role"] === "appraiser"
            ])
			->add("submit", SubmitType::class);
		if ($data["is_senior"] ?? false) {
			$builder->add("part_b2_0", PartB1Type::class, [
                    "label" => false,
                    "role" => $options["role"],
                    "property_path" => "[part_b2][0]",
                    "title" => "Influence, Negotiation and Persuasion",
                    "lhs_text" => [
                        "5. Effective influencer and persuader at all levels, able to get most ideas accepted in diverse groups at most levels of seniority.",
                        "4. Inspires confidence, has credibility with colleagues and customers and is able to get complex ideas accepted. It is generally able to persuade from a basis of openness and clarity.",
                        "3. Make a positive impact, is clear, concise, articulate and assertive when providing information and considered logical and reasoned in presenting own case. Able to compromise with customers (or other stakeholder) by convincing them of one's ideas and thoughts from various point of view.",
                        "2. Has no involvement in making oral or written presentations or in getting ideas or views across to others. Not able to find acceptable solution for all the parties from mid/long term prospective instead of insisting on one's idea.",
                        "1. Colleagues and/or customers pay little attention to debate, proffered solutions or written work. Contributions within team or at meetings are generally not listened to."
                    ]
                ])
                ->add("part_b2_1", PartB1Type::class, [
                    "label" => false,
                    "role" => $options["role"],
                    "property_path" => "[part_b2][1]",
                    "title" => "Coaching and Developing",
                    "lhs_text" => [
                        "5. Takes time to coach and develop people for improved performance; Pro-active in idenitifying and developing high calibrae knowledge, and planning for enhancement of managerial qualities in self and others.",
                        "4. Provides regular feedback on performance, suggests improvements, listens and empathises with others, and gets people to commit to responsibilties and try new techniques; Pro-active in sharing knowledge, leading, training, supporting and motivating people to achieve results and improve their works.",
                        "3. Translates performance targets into clear objectives. Generally coaches and supports others on low level daily issues.",
                        "2. Is not involved in coaching and developing others.",
                        "1. Hinders the development of others and generally provides no or negative feedback. Unwilling to devote time to development of others."
                    ]
                ])
                ->add("part_b2_2", PartB1Type::class, [
                    "label" => false,
                    "role" => $options["role"],
                    "property_path" => "[part_b2][2]",
                    "title" => "Leadership and Strategic Thinking",
                    "lhs_text" => [
                        "5. Become a role model in upholding the Company philosophy. Has a record of leading teams achieving results in difficult situations and creates a climate where employees are highly motivated to achieve goals.",
                        "4. Become a role model in upholding the Company philosophy. Has a record of clear motivational leadership, recognition of other's achievements and development of visions, targets and techniques which have kept teams and/or individuals focused on the goals. Has introduced and managed change initiatives in own team effectively.",
                        "3. Able to set a good example in terms of diligency, integrity and ethically. Has record of achieving results through others when asked, or opportunities arise. Has explained to subordinates the Company values, goal and team quality objectives for their understanding and support.",
                        "2. Failed to set any good example in terms of diligency, integrity and ethically. Has not had the opportunity to demonstrate leadership qualities or shared the quality objectives with subordinates.",
                        "1. Set many bad examples in terms of diligency, integrity and ethically. Unable to lead teams, does not provide direct and lowers morale."
                    ]
                ])
                ->add("part_b2_overall_comment", TextareaType::class)
                ->add("part_b2_overall_score", NumberType::class, [
                    "disable" => true,
                    "scale" => 2
                ]);
		}
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		parent::buildView($view, $form, $options);
		$json = $form->getData();
		$view->vars["countersigner_name"] = $json["countersigner_name"];
	}


	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
		    "role" => "owner",
        ]);
	}

	public function getBlockPrefix() {
		return "version_1_main";
	}
}