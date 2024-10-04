<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginFormType;
use App\Repository\LoginRepository;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        Request                     $request,
        UserAuthenticatorInterface  $userAuthenticator,
        LoginAuthenticator          $authenticator,
        LoginRepository             $loginRepository,
        AuthenticationUtils         $authenticationUtils
    ): Response
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('server_preview');
        }

        $form = $this
            ->createForm(LoginFormType::class)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $loginRepository->findOneBy([
                'username' => $form->get('username')->getData()
            ]);

            if ($user) {
                $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }

            return $this->redirectToRoute('server_preview');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('auth/login.html.twig', [
            'login_form'    => $form->createView(),
            'error'         => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
