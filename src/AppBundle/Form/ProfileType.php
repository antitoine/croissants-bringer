<?php

namespace AppBundle\Form;

use AppBundle\Enum\UserPreferenceEnum;
use AppBundle\Enum\UserStatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $preferenceList = [];
        foreach (UserPreferenceEnum::getShortNameList() as $item) {
            $preferenceList[UserPreferenceEnum::getFullName($item)] = $item;
        }

        $statusList = [];
        foreach (UserStatusEnum::getShortNameList() as $item) {
            $statusList[UserStatusEnum::getFullName($item)] = $item;
        }

        $builder
            ->add('participant')
            ->add('preference', ChoiceType::class, [
                'choices'  => $preferenceList,
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => $statusList,
            ]);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
