<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\UserForm;
use Symfony\Component\Validator\Validation;

class FormDataTest extends TypeTestCase
{
	protected FormFactoryInterface $formFactory;

	/**
	 * @return array[]
	 */
	public static function formDataProvider(): array
	{
		$validImagePath   = sys_get_temp_dir() . '/validImage.png';
		$invalidMimePath  = sys_get_temp_dir() . '/invalidMime.txt';
		$tooLargeImagePath= sys_get_temp_dir() . '/tooLargeImage.png';

		if (!file_exists($validImagePath)) {
			file_put_contents($validImagePath, str_repeat('A', 1024 * 1024));
		}

		if (!file_exists($invalidMimePath)) {
			file_put_contents($invalidMimePath, 'Some text content');
		}

		if (!file_exists($tooLargeImagePath)) {
			file_put_contents($tooLargeImagePath, str_repeat('A', 3 * 1024 * 1024));
		}

		return [
			'valid no file' => [
				'formData' => [
					'name'      => 'Jan',
					'lastname'  => 'Kowalski',
					'attachment' => null,
				],
				'isValid' => true,
			],

			'empty lastname' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => '',
					'attachment' => null,
				],
				'isValid' => false,
			],

			'empty name' => [
				'formData' => [
					'name'      => '',
					'lastname'  => 'Wojcik',
					'attachment' => null,
				],
				'isValid' => false,
			],

			'valid data with file' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$validImagePath,
						'validImage.png',
						'image/png',
						null,
						true
					),
				],
				'isValid' => true,
			],

			'too large attachment' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$tooLargeImagePath,
						'tooLargeImage.png',
						'image/png',
						null,
						true
					),
				],
				'isValid' => false,
			],

			'invalid mime type' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$invalidMimePath,
						'invalidMime.txt',
						'text/plain',
						null,
						true
					),
				],
				'isValid' => false,
			],
		];
	}

	protected function setUp(): void
	{
		$validator = Validation::createValidator();

		$this->formFactory = Forms::createFormFactoryBuilder()
			->addExtension(new ValidatorExtension($validator))
			->addExtension(new HttpFoundationExtension())
			->getFormFactory();

		parent::setUp();
	}

	public static function tearDownAfterClass(): void
	{
		$validImagePath = sys_get_temp_dir() . '/validImage.png';
		$invalidMimePath = sys_get_temp_dir() . '/invalidMime.txt';
		$tooLargeImagePath = sys_get_temp_dir() . '/tooLargeImage.png';

		if (file_exists($validImagePath)) {
			unlink($validImagePath);
		}

		if (file_exists($invalidMimePath)) {
			unlink($invalidMimePath);
		}

		if (file_exists($tooLargeImagePath)) {
			unlink($tooLargeImagePath);
		}

		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider formDataProvider
	 */
	public function testSubmitValidData(array $formData, bool $isValid): void
	{
		$form = $this->formFactory->create(UserForm::class);
		$form->submit($formData);

		$this->assertEquals($isValid, $form->isValid());
	}
}