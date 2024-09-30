<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('landing.html.twig', []);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('pricing.html.twig', []);
    }

    #[Route('/terms_of_use', name: 'terms_of_use')]
    public function termsOfUse(): Response
    {
        return $this->render('termsOfUse.html.twig', []);
    }
}
