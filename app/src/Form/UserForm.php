<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'constraints' => [
					new NotBlank([
						'message' => 'Name cannot be blank.',
					]),
				],
			])
			->add('lastname', TextType::class, [
				'constraints' => [
					new NotBlank([
						'message' => 'Last name cannot be blank.',
					]),
				],
			])
			->add('attachment', FileType::class, [
				'constraints' => [
					new File([
						'maxSize' => '2M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
							'image/gif',
						],
						'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
					]),
				],
			])
			->add('save', SubmitType::class, [
				'label' => 'Save',
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([]);
	}
}