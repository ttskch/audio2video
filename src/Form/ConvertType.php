<?php

namespace App\Form;

use App\Entity\ConvertCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class ConvertType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('audioFile', FileType::class, [
                'label' => $this->translator->trans('Audio file'),
                'attr' => [
                    'accept' => 'audio/*',
                ],
            ])
            ->add('outputFormat', ChoiceType::class, [
                'required' => false,
                'label' => $this->translator->trans('Output format'),
                'placeholder' => false,
                'choices' => [
                    'mp4' => 'mp4',
                    'm4v' => 'm4v',
                    'avi' => 'avi',
                    'wmv' => 'wmv',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'inline' => true,
                ],
            ])
            ->add('frameRate', IntegerType::class, [
                'required' => false,
                'label' => $this->translator->trans('Frame rate (fps)'),
                'attr' => [
                    'min' => 1,
                    'max' => 60,
                ],
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => $this->translator->trans('Image file'),
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('imageResolution', ChoiceType::class, [
                'required' => false,
                'label' => $this->translator->trans('Resolution'),
                'placeholder' => false,
                'choices' => [
                    '400 x 300' => '400x300',
                    '800 x 600' => '800x600',
                    '800 x 450' => '800x450',
                    '1600 x 900' => '1600x900',
                    '600 x 600' => '600x600',
                    '1200 x 1200' => '1200x1200',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('imageColor', TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('Background Color'),
                'attr' => [
                    'pattern' => '^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$',
                ],
            ])
            ->add('showAdvanced', HiddenType::class, [
                'mapped' => false,
                'data' => 0,
            ])
            ->add('selectedTab', HiddenType::class, [
                'mapped' => false,
                'data' => '#tab1',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConvertCriteria::class,
        ]);
    }
}
