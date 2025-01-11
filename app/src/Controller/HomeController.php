<?php

namespace App\Controller;

use App\Form\UserForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
		$form = $this->createForm(UserForm::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->addFlash('success', 'Form submitted successfully!');
			return $this->redirectToRoute('app_home');
		}

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
	        'user_form' => $form->createView(),
        ]);
    }
}
