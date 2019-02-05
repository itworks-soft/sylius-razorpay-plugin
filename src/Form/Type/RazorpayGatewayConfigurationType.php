<?php

namespace ITWorks\SyliusRazorpayPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class RazorpayGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('api_key', TextType::class, [
                'label' => 'itworks.razorpay_plugin.api_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'itworks.razorpay_plugin.gateway_configuration.api_key.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('api_secret', TextType::class, [
                'label' => 'itworks.razorpay_plugin.api_secret',
                'constraints' => [
                    new NotBlank([
                        'message' => 'itworks.razorpay_plugin.gateway_configuration.api_secret.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
        ;
    }
}
