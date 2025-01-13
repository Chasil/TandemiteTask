<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class FileUploader
{
	public function __construct(private string $uploadDirectory)
	{
	}

	public function upload(UploadedFile $file): string
	{
		$newFilename = uniqid().'.'.$file->guessExtension();
		$file->move($this->uploadDirectory, $newFilename);

		return $newFilename;
	}
}