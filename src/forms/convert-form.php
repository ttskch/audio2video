<?php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

$form = $app['form.factory']->createBuilder(FormType::class)
    ->add('audio_file', FileType::class, [
        'label' => $app['translator']->trans('Audio file'),
        'attr' => [
            'accept' => 'audio/*',
        ],
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\File(),
        ],
    ])
    ->add('output_format', ChoiceType::class, [
        'required' => false,
        'label' => $app['translator']->trans('Output format'),
        'placeholder' => false,
        'attr' => [
            'class' => 'inline',
        ],
        'choices' => [
            'mp4' => 'mp4',
            'm4v' => 'm4v',
            'avi' => 'avi',
            'wmv' => 'wmv',
        ],
        'data' => 'mp4',
        'expanded' => true,
        'multiple' => false,
        'constraints' => [
            new Assert\Choice([
                'mp4',
                'm4v',
                'avi',
                'wmv',
            ]),
        ],
    ])
    ->add('frame_rate', IntegerType::class, [
        'required' => false,
        'label' => $app['translator']->trans('Frame rate'),
        'attr' => [
            'min' => 1,
            'max' => 60,
        ],
        'data' => 30,
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\GreaterThanOrEqual(1),
            new Assert\LessThanOrEqual(60),
        ],
    ])
    ->add('image_file', FileType::class, [
        'required' => false,
        'label' => $app['translator']->trans('Image file'),
        'attr' => [
            'accept' => 'image/*',
        ],
        'constraints' => [
            new Assert\Image(),
        ],
    ])
    ->add('image_resolution', ChoiceType::class, [
        'required' => false,
        'label' => $app['translator']->trans('Resolution'),
        'placeholder' => false,
        'choices' => [
            '400 x 300' => '400x300',
            '800 x 600' => '800x600',
            '800 x 450' => '800x450',
            '1600 x 900' => '1600x900',
            '600 x 600' => '600x600',
            '1200 x 1200' => '1200x1200',
        ],
        'data' => '800x450',
        'expanded' => false,
        'multiple' => false,
        'constraints' => [
            new Assert\Regex([
                'pattern' => '/^\d+x\d+$/',
            ]),
        ],
    ])
    ->add('image_color', TextType::class, [
        'required' => false,
        'label' => $app['translator']->trans('Background Color'),
        'attr' => [
            'pattern' => '^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$',
        ],
        'data' => '000',
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => '/^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
            ]),
        ],
    ])
    ->getForm()
;

return $form;
