<?php
// src/Form/Type/TaskType.php
namespace App\Form\Type;


use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', TextType::class)
                ->add('name', TextType::class);
                
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryDto::class,
            'csrf_protection'=>false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';   
    }

    public function getName() {
        return '';
    }
}