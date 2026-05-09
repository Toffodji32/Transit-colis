<?php

namespace App\Controller;

use App\Repository\ColisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TrackingController extends AbstractController
{
    public function __construct(
        private readonly ColisRepository $colisRepository
    ) {
    }

    #[Route('/tracking', name: 'app_tracking')]
    public function index(Request $request): Response
    {
        $numeroColis = $request->query->get('numero');
        $colis = null;
        
        if ($numeroColis) {
            $colis = $this->colisRepository->findByNumero($numeroColis);
        }

        return $this->render('tracking/index.html.twig', [
            'colis' => $colis,
            'numeroRecherche' => $numeroColis,
        ]);
    }
}
