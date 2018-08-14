<?php
namespace App\FormType\SurveyForm;

use App\Entity\SurveyData;
use App\Entity\Template\Type1\PartA;
use App\Entity\Template\Type1\Survey;
use App\FormType\JsonDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Type1 extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add("staff_name", TextType::class, [
                "disabled" => true
            ])->add("department", TextType::class, [
                "disabled" => true
            ])
            ->add("position", TextType::class, [
                "disabled" => true
            ])
            ->add("office", TextType::class, [
                "disabled" => true
            ])
            ->add("ao_name", TextType::class, [
                "disabled" => true
            ])
            ->add("co_name", TextType::class, [
                "disabled" => true
            ])
            ->add("survey_period", TextType::class, [
                "disabled" => true
            ])
            ->add("commence_date", TextType::class, [
                "disabled" => true
            ])
            ->add("part_a", CollectionType::class, [
                "entry_type" => PartAInputType::class,
                'allow_add' => true,
                "entry_options" => [
                    'label' => false
                ]
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
    }
}