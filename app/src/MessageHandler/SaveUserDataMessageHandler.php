<?php

namespace App\MessageHandler;

use App\Entity\UserData;
use App\Message\SaveUserDataMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SaveUserDataMessageHandler
{
	public function __construct(
		private readonly EntityManagerInterface $em
	) {
	}

	public function __invoke(SaveUserDataMessage $message)
	{
		$userData = new UserData();
		$userData->setName($message->getName());
		$userData->setLastname($message->getLastname());

		$userData->setAttachment($message->getUploadedFilePath());

		$this->em->persist($userData);
		$this->em->flush();
	}
}