<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Nom'
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'Prénom'])
            ->add('email', EmailType::class, ['required' => true, 'label' => 'Email'])
            ->add('phone', TelType::class, ['required' => true, 'label' => 'Téléphone'])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Message'])
        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!empty($data['phone']) || !empty($data['email'])) {
                if (!empty($data['phone'])) {
                    $form->add('email', EmailType::class, ['required' => false, 'label' => 'Email']);
                }
                if (!empty($data['email'])) {
                    $form->add('phone', TextType::class, ['required' => false, 'label' => 'Téléphone']);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
