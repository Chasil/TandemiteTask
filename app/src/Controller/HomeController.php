<?php

namespace App\Controller;

use App\Entity\UserData;
use App\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $emi): Response
    {
		$userData = new UserData();
		$form = $this->createForm(UserForm::class, $userData);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$uploadedFile = $form->get('attachment')->getData();

			if($uploadedFile) {

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

				$userData->setAttachment($newFilename);
			}

			$emi->persist($userData);
			$emi->flush();

			$this->addFlash('success', 'Form submitted successfully!');
			return $this->redirectToRoute('app_home');
		}

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
	        'user_form' => $form->createView(),
        ]);
    }
}
