<?php

namespace App\Tests\Form;

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
			file_put_contents($validImagePath, str_repeat('A', 1024 * 1024)); // 1 MB
		}

		if (!file_exists($invalidMimePath)) {
			file_put_contents($invalidMimePath, 'Some text content');
		}

		if (!file_exists($tooLargeImagePath)) {
			file_put_contents($tooLargeImagePath, str_repeat('A', 3 * 1024 * 1024));
		}

		return [
			// 1) Brak pliku => powinno przejść, bo pole jest 'required' => false
			'valid no file' => [
				'formData' => [
					'name'      => 'Jan',
					'lastname'  => 'Kowalski',
					'attachment' => null, // brak pliku
				],
				'isValid' => true,
			],

			// 2) Wszystko poprawne, plik < 2 MB, MIME = image/png
			'valid data with file' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$validImagePath,
						'validImage.png',
						'image/png',
						null,   // czwarty argument to $error => null => UPLOAD_ERR_OK
						false   // piąty argument to $test => false
					),
				],
				'isValid' => true,
			],

			// 3) Puste pole name => ma nie przejść
			'empty name' => [
				'formData' => [
					'name'      => '',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$validImagePath,
						'validImage.png',
						'image/png',
						null,
						false
					),
				],
				'isValid' => false,
			],

			// 4) Puste pole lastname => ma nie przejść
			'empty lastname' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => '',
					'attachment' => new UploadedFile(
						$validImagePath,
						'validImage.png',
						'image/png',
						null,
						false
					),
				],
				'isValid' => false,
			],

			// 5) Plik przekracza limit 2 MB => ma nie przejść
			'too large attachment' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$tooLargeImagePath,
						'tooLargeImage.png',
						'image/png',
						null,
						false
					),
				],
				'isValid' => false,
			],

			// 6) Niedozwolony typ MIME (text/plain) => ma nie przejść
			'invalid mime type' => [
				'formData' => [
					'name'      => 'Mateusz',
					'lastname'  => 'Wojcik',
					'attachment' => new UploadedFile(
						$invalidMimePath,
						'invalidMime.txt',
						'text/plain',
						null,
						false
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