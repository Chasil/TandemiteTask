<?php

namespace App\Controller;

use App\Entity\UserData;
use App\Form\UserForm;
use App\Message\SaveUserDataMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	#[Route('/', name: 'app_home')]
	public function index(Request $request, MessageBusInterface $bus): Response
	{
		$userData = new UserData();

		$form = $this->createForm(UserForm::class, $userData);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$uploadedFile = $form->get('attachment')->getData();
			$newFilename = null;

			if ($uploadedFile) {
				$newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

				try {
					$uploadedFile->move(
						$this->getParameter('uploads_directory'),
						$newFilename
					);
				} catch (\Exception $e) {
					$this->addFlash('error', 'An error occurred while uploading the file.');
					return $this->redirectToRoute('app_home');
				}
			}

			$bus->dispatch(
				new SaveUserDataMessage(
					$userData->getName(),
					$userData->getLastname(),
					$newFilename
				)
			);

			$this->addFlash('success', 'User data submitted successfully!');
			return $this->redirectToRoute('app_home');
		}

		return $this->render('home/index.html.twig', [
			'controller_name' => 'HomeController',
			'user_form' => $form->createView(),
		]);
	}
}