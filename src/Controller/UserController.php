<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_management')]
    public function management(): Response
    {
        $user = $this->getUser();

        return $this->render('user/user.html.twig', [
            'user'  => $user
        ]);
    }

    #[Route('/delete_account', name: 'user_delete_account')]
    public function deleteAccount(): Response
    {
        $user = $this->getUser();

        return $this->render('landing.html.twig', []);
    }

}
