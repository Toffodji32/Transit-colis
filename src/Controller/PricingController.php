<?php

namespace App\Controller;

use App\Repository\TarifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PricingController extends AbstractController
{
    public function __construct(
        private readonly TarifRepository $tarifRepository
    ) {
    }

    #[Route('/pricing', name: 'app_pricing')]
    public function index(): Response
    {
        // Récupérer tous les tarifs actifs
        $tarifs = $this->tarifRepository->findTarifsActifs();
        
        // Organiser par route
        $tarifsNGB = array_filter($tarifs, fn($t) => str_contains($t->getRoute(), 'Nigeria'));
        $tarifsBEN = array_filter($tarifs, fn($t) => str_contains($t->getRoute(), 'Bénin'));

        return $this->render('pricing/index.html.twig', [
            'tarifsNGB' => $tarifsNGB,
            'tarifsBEN' => $tarifsBEN,
        ]);
    }
}
