<?php

namespace App\Message;

readonly class SaveUserDataMessage
{
	public function __construct(
		private string  $name,
		private string  $lastname,
		private ?string $uploadedFilePath,
	) {
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getLastname(): string
	{
		return $this->lastname;
	}

	public function getUploadedFilePath(): ?string
	{
		return $this->uploadedFilePath;
	}
}