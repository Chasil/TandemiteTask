<?php

namespace App\Tests\Form;

use App\Form\UserForm;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validation;

class FormDataTest extends TypeTestCase
{
	protected FormFactoryInterface $formFactory;

	public static function nameLastnameProvider(): array
	{
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
		];
	}

	public static function fileConstraintsProvider(): array
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
			'invalid mime type' => [
				'formData' => [
					'name'     => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						$invalidMimePath,
						'invalidMime.txt',
						'text/plain',
						null,
						true
					),
				],
				'expectedError' => "attachment:\n    ERROR: Please upload a valid image file (JPEG, PNG, GIF).\n",
			],
			'too large attachment' => [
				'formData' => [
					'name'     => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						$tooLargeImagePath,
						'tooLargeImage.png',
						'image/png',
						null,
						true
					),
				],
				'expectedError' => "attachment:\n    ERROR: The file is too large (3.15 MB). Allowed maximum size is 2 MB.\n",
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
		$validImagePath   = sys_get_temp_dir() . '/validImage.png';
		$invalidMimePath  = sys_get_temp_dir() . '/invalidMime.txt';
		$tooLargeImagePath= sys_get_temp_dir() . '/tooLargeImage.png';

		foreach ([$validImagePath, $invalidMimePath, $tooLargeImagePath] as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}

		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider nameLastnameProvider
	 */
	public function testNameLastname(array $formData, bool $isValid): void
	{
		$form = $this->formFactory->create(UserForm::class);
		$form->submit($formData);

		$this->assertEquals($isValid, $form->isValid());
	}

	/**
	 * @dataProvider fileConstraintsProvider
	 */
	public function testFileConstraints(array $formData, ?string $expectedError): void
	{
		$form = $this->formFactory->create(UserForm::class);
		$form->submit($formData);

		$errorsString = (string) $form->getErrors(true, false);
		$this->assertStringContainsString($expectedError, $errorsString);
	}
}