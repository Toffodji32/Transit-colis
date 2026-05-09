<?php

namespace App\Controller;

use App\Repository\ColisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly ColisRepository $colisRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les statistiques
        $colisEnTransit = $this->colisRepository->count(['user' => $user, 'statutActuel' => 'en_transit']);
        $colisLivres30jours = $this->entityManager
            ->createQuery(
                'SELECT COUNT(c) FROM App\Entity\Colis c 
                WHERE c.user = :user AND c.statutActuel = :statut 
                AND c.dateLivraison >= :dateLimite'
            )
            ->setParameter('user', $user)
            ->setParameter('statut', 'livre')
            ->setParameter('dateLimite', new \DateTime('-30 days'))
            ->getSingleScalarResult();
        
        $actionRequise = $this->entityManager
            ->createQuery(
                'SELECT COUNT(c) FROM App\Entity\Colis c 
                WHERE c.user = :user AND c.statutActuel IN (:statuts)'
            )
            ->setParameter('user', $user)
            ->setParameter('statuts', ['probleme', 'arrive_destination'])
            ->getSingleScalarResult();

        // Récupérer les colis récents
        $colisRecents = $this->colisRepository->findBy(
            ['user' => $user],
            ['dateEnregistrement' => 'DESC'],
            10
        );

        // Calcul du total pour le graphique
        $totalColis = count($colisRecents);
        $pourcentageLivres = $totalColis > 0 ? round(($colisLivres30jours / $totalColis) * 100) : 0;
        $pourcentageEnTransit = $totalColis > 0 ? round(($colisEnTransit / $totalColis) * 100) : 0;
        $pourcentageActionRequise = $totalColis > 0 ? round(($actionRequise / $totalColis) * 100) : 0;

        return $this->render('dashboard/index.html.twig', [
            'colisEnTransit' => $colisEnTransit,
            'colisLivres30jours' => $colisLivres30jours,
            'actionRequise' => $actionRequise,
            'colisRecents' => $colisRecents,
            'totalColis' => $totalColis,
            'pourcentageLivres' => $pourcentageLivres,
            'pourcentageEnTransit' => $pourcentageEnTransit,
            'pourcentageActionRequise' => $pourcentageActionRequise,
        ]);
    }
}
