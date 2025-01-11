<?php

namespace App\Message;

class SaveUserDataMessage
{
	public function __construct(
		private readonly string $name,
		private readonly string $lastname,
		private readonly ?string $uploadedFilePath,  // ścieżka lub nazwa pliku
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