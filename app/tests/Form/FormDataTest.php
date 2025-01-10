<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use App\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormDataTest extends TypeTestCase
{
	private static string $testFilePath;
	private static string $largeFilePath;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		self::$testFilePath = sys_get_temp_dir() . '/testfile.txt';
		self::$largeFilePath = sys_get_temp_dir() . '/largefile.txt';

		file_put_contents(self::$testFilePath, 'This is a test file.');
		file_put_contents(self::$largeFilePath, str_repeat('A', 3 * 1024 * 1024));
	}

	public static function tearDownAfterClass(): void
	{
		if (file_exists(self::$testFilePath)) {
			unlink(self::$testFilePath);
		}

		if (file_exists(self::$largeFilePath)) {
			unlink(self::$largeFilePath);
		}

		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider formDataProvider
	 */
	public function testSubmitValidData($formData, $isValid)
	{
		$form = $this->factory->create(Form::class);

		$form->submit($formData);

		$this->assertEquals($isValid, $form->isValid());
	}

	public static function formDataProvider(): array
	{
		return [
			'valid data' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						self::$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					)
				],
				'isValid' => true
			],
			'empty name' => [
				'formData' => [
					'name' => '',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						self::$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					)
				],
				'isValid' => false
			],
			'empty lastname' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => '',
					'attachment' => new UploadedFile(
						self::$testFilePath,
						'testfile.txt',
						'text/plain',
						null,
						true
					)
				],
				'isValid' => false
			],
			'large attachment' => [
				'formData' => [
					'name' => 'Mateusz',
					'lastname' => 'Wojcik',
					'attachment' => new UploadedFile(
						self::$largeFilePath,
						'largefile.txt',
						'text/plain',
						3000000, // 3 MB
						true
					)
				],
				'isValid' => false
			]
		];
	}
}