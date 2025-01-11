<?php

namespace App\Entity;

use App\Repository\UserDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDataRepository::class)]
class UserData
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(length: 255)]
	private ?string $lastname = null;

	// Zamiast 'attachement' -> 'attachment'
	#[ORM\Column(length: 255, nullable: true)]
	private ?string $attachment = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getLastname(): ?string
	{
		return $this->lastname;
	}

	public function setLastname(string $lastname): static
	{
		$this->lastname = $lastname;

		return $this;
	}

	public function getAttachment(): ?string
	{
		return $this->attachment;
	}

	public function setAttachment(?string $attachment): static
	{
		$this->attachment = $attachment;

		return $this;
	}
}