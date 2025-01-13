<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\UserForm;

class FormDataTest extends TypeTestCase
{
	/**
	 * Metoda data provider tworzy (jeśli nie istnieją) pliki testowe i zwraca
	 * scenariusze testowe. W ten sposób pliki będą dostępne, zanim testy się rozpoczną.
	 */
	public static function formDataProvider(): array
	{
		$testFilePath = sys_get_temp_dir() . '/testfile.txt';
		$largeFilePath = sys_get_temp_dir() . '/largefile.txt';

		// Tworzymy mały plik testowy (jeśli nie istnieje)
		if (!file_exists($testFilePath)) {
			file_put_contents($testFilePath, 'This is a test file.');
		}

		// Tworzymy duży plik (3 MB) (jeśli nie istnieje)
		if (!file_exists($largeFilePath)) {
			file_put_contents($largeFilePath, str_repeat('A', 3 * 1024 * 1024));
		}

		return [
			'valid data' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					),
				],
				'isValid' => true,
			],
			'empty name' => [
				'formData' => [
					'name' => '',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					),
				],
				'isValid' => false,
			],
			'empty lastname' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => '',
					'attachment' => new UploadedFile(
						$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					),
				],
				'isValid' => false,
			],
			'large attachment' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						$largeFilePath,
						'largefile.txt',
						'text/plain',
						3000000, // 3 MB
						true
					),
				],
				'isValid' => false,
			],
		];
	}

	/**
	 * Możesz zachować tę metodę, jeśli chcesz posprzątać pliki po zakończeniu wszystkich testów.
	 * Gdy testy się skończą, usuniemy pliki.
	 */
	public static function tearDownAfterClass(): void
	{
		$testFilePath = sys_get_temp_dir() . '/testfile.txt';
		$largeFilePath = sys_get_temp_dir() . '/largefile.txt';

		if (file_exists($testFilePath)) {
			unlink($testFilePath);
		}

		if (file_exists($largeFilePath)) {
			unlink($largeFilePath);
		}

		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider formDataProvider
	 */
	public function testSubmitValidData(array $formData, bool $isValid): void
	{
		$form = $this->factory->create(UserForm::class);
		$form->submit($formData);

		$this->assertEquals($isValid, $form->isValid());
	}
}