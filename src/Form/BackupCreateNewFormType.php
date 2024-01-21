<?php

namespace App\Form;

use App\Entity\Backup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use App\UniqueNameInterface\BackupInterface;

class BackupCreateNewFormType extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(BackupInterface::FORM_NAME, TextType::class, [
                'label'         => 'Backup name',
                'attr'          => [
                        'required'  => 'true',
                        'class'     => 'form-control',
                        'value'     => $options['defaultBackupName']
                ],
                'constraints'   => [
                    new Length([
                        'min'           => 6,
                        'minMessage'    => 'Your backup name must be at least 6 characters long',
                        'max'           => 4096,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr'  => [
                    'class'     => 'btn btn-lg btn-primary',
                ],
                'label' => 'Backup'
            ])
            ->setAction($this->router->generate('backup_create_new'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'            => null,
            'csrf_protection'       => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name'       => 'csrf_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'         => 'login_form',
            'defaultBackupName'     => null
        ]);
    }
}
