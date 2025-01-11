<?php

namespace App\Controller;

use App\Repository\UserDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserDataController extends AbstractController
{
    #[Route('/admin/user-data', name: 'app_user_data_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function list(UserDataRepository $userDataRepository): Response
    {
		$users = $userDataRepository->findAll();

        return $this->render('user_data/index.html.twig', [
            'users' => $users,
        ]);
    }
}
