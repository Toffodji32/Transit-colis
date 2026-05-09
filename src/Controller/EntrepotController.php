<?php

namespace App\Controller;

use App\Entity\Colis;
use App\Entity\HistoriqueStatut;
use App\Entity\User;
use App\Repository\ColisRepository;
use App\Repository\WarehouseRepository;
use App\Service\EmailNotificationService;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/entrepot')]
#[IsGranted('ROLE_ENTREPOT')]
class EntrepotController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly ColisRepository $colisRepository,
        private readonly UploadImageService $uploadImageService,
        private readonly EmailNotificationService $emailNotificationService
    ) {
    }

    #[Route('', name: 'app_entrepot_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // Trouver l'entrepôt géré par ce responsable
        $warehouse = $this->warehouseRepository->findByResponsable($user->getId());
        if (!$warehouse) {
            throw $this->createNotFoundException('Aucun entrepôt assigné à votre compte');
        }

        // Statistiques de l'entrepôt
        $colisOrigine = $this->colisRepository->findBy(['warehouseOrigine' => $warehouse], ['dateEnregistrement' => 'DESC']);
        $colisDestination = $this->colisRepository->findBy(['warehouseDestination' => $warehouse], ['dateEnregistrement' => 'DESC']);
        
        $totalOrigine = count($colisOrigine);
        $totalDestination = count($colisDestination);
        $totalColis = $totalOrigine + $totalDestination;

        // Statuts
        $statutsOrigine = ['enregistre' => 0, 'en_preparation' => 0, 'en_transit' => 0, 'arrive_destination' => 0, 'pret_retrait' => 0, 'livre' => 0];
        $statutsDestination = ['enregistre' => 0, 'en_preparation' => 0, 'en_transit' => 0, 'arrive_destination' => 0, 'pret_retrait' => 0, 'livre' => 0];

        foreach ($colisOrigine as $colis) {
            if (isset($statutsOrigine[$colis->getStatutActuel()])) {
                $statutsOrigine[$colis->getStatutActuel()]++;
            }
        }

        foreach ($colisDestination as $colis) {
            if (isset($statutsDestination[$colis->getStatutActuel()])) {
                $statutsDestination[$colis->getStatutActuel()]++;
            }
        }

        // Colis récents (10 derniers)
        $colisRecents = array_slice(array_merge($colisOrigine, $colisDestination), 0, 10);

        return $this->render('entrepot/dashboard.html.twig', [
            'warehouse' => $warehouse,
            'totalColis' => $totalColis,
            'totalOrigine' => $totalOrigine,
            'totalDestination' => $totalDestination,
            'statutsOrigine' => $statutsOrigine,
            'statutsDestination' => $statutsDestination,
            'colisRecents' => $colisRecents,
        ]);
    }

    #[Route('/colis', name: 'app_entrepot_colis')]
    public function colis(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $warehouse = $this->warehouseRepository->findByResponsable($user->getId());
        if (!$warehouse) {
            throw $this->createNotFoundException('Aucun entrepôt assigné à votre compte');
        }

        // Colis partis de cet entrepôt OU arrivés à cet entrepôt
        $colisOrigine = $this->colisRepository->findBy(['warehouseOrigine' => $warehouse], ['dateEnregistrement' => 'DESC']);
        $colisDestination = $this->colisRepository->findBy(['warehouseDestination' => $warehouse], ['dateEnregistrement' => 'DESC']);
        
        $tousColis = array_merge($colisOrigine, $colisDestination);

        return $this->render('entrepot/colis/list.html.twig', [
            'warehouse' => $warehouse,
            'colis' => $tousColis,
        ]);
    }

    #[Route('/colis/new', name: 'app_entrepot_colis_new')]
    public function colisNew(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $warehouse = $this->warehouseRepository->findByResponsable($user->getId());
        if (!$warehouse) {
            throw $this->createNotFoundException('Aucun entrepôt assigné à votre compte');
        }

        if ($request->isMethod('POST')) {
            $numeroColis = $request->request->get('numero_colis');
            $poids = $request->request->get('poids');
            $description = $request->request->get('description');
            $paysOrigine = $request->request->get('pays_origine');
            $paysDestination = $request->request->get('pays_destination');
            
            $expediteurNom = $request->request->get('expediteur_nom');
            $expediteurTel = $request->request->get('expediteur_tel');
            $expediteurEmail = $request->request->get('expediteur_email');
            
            $destinataireNom = $request->request->get('destinataire_nom');
            $destinataireTel = $request->request->get('destinataire_tel');
            $destinataireEmail = $request->request->get('destinataire_email');
            $destinataireAdresse = $request->request->get('destinataire_adresse');

            // Si pas de numéro fourni, en générer un
            if (!$numeroColis) {
                $numeroColis = $this->genererNumeroColis();
            }

            // Vérifier que le numéro n'existe pas déjà
            $colisExistant = $this->colisRepository->findByNumero($numeroColis);
            if ($colisExistant) {
                $this->addFlash('error', 'Ce numéro de suivi existe déjà.');
                return $this->redirectToRoute('app_entrepot_colis_new');
            }

            $colis = new Colis();
            $colis->setNumeroColis($numeroColis)
                ->setPoids($poids)
                ->setDescription($description)
                ->setPaysOrigine($paysOrigine)
                ->setPaysDestination($paysDestination)
                ->setStatutActuel(Colis::STATUT_CREE)
                ->setStatutPaiement(Colis::PAIEMENT_NON_PAYE)
                ->setExpediteurNom($expediteurNom)
                ->setExpediteurTel($expediteurTel)
                ->setExpediteurEmail($expediteurEmail)
                ->setDestinataireNom($destinataireNom)
                ->setDestinataireTel($destinataireTel)
                ->setDestinataireEmail($destinataireEmail)
                ->setDestinataireAdresse($destinataireAdresse)
                ->setWarehouseOrigine($warehouse); // L'entrepôt actuel est toujours l'origine

            // Déterminer l'entrepôt de destination
            if ($paysDestination === 'Nigeria') {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Lagos');
            } else {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Cotonou');
            }

            if ($warehouseDestination) {
                $colis->setWarehouseDestination($warehouseDestination);
            }

            // Gérer l'upload de photo
            $photo = $request->files->get('photo');
            if ($photo) {
                $filename = $this->uploadImageService->upload($photo);
                if ($filename) {
                    $colis->setImages([$filename]);
                }
            }

            // Calculer les frais
            $montantFrais = $this->calculerFrais($paysOrigine, $paysDestination, $poids);
            $colis->setMontantFrais($montantFrais);

            // Créer l'historique initial
            $historique = new HistoriqueStatut();
            $historique->setColis($colis)
                ->setStatut(Colis::STATUT_CREE)
                ->setCommentaire('Colis créé par ' . $warehouse->getNom())
                ->setUserModificateur($user);

            $this->entityManager->persist($colis);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();

            // Envoyer l'email de notification au destinataire
            try {
                $this->emailNotificationService->sendColisRegisteredNotification($colis);
                $this->addFlash('success', 'Colis enregistré avec succès ! Numéro de suivi : ' . $numeroColis . '. Email envoyé au destinataire.');
            } catch (\Exception $e) {
                $this->addFlash('success', 'Colis enregistré avec succès ! Numéro de suivi : ' . $numeroColis);
                $this->addFlash('error', 'Erreur envoi email : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_entrepot_colis');
        }

        return $this->render('entrepot/colis/new.html.twig', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Génère un numéro de suivi unique
     */
    private function genererNumeroColis(): string
    {
        do {
            $numero = 'TC-' . date('Y') . '-' . strtoupper(substr(uniqid(), 0, 8));
        } while ($this->colisRepository->findByNumero($numero));

        return $numero;
    }

    #[Route('/colis/{id}/update-status', name: 'app_entrepot_colis_update_status', methods: ['POST'])]
    public function updateColisStatus(Request $request, int $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $warehouse = $this->warehouseRepository->findByResponsable($user->getId());
        $colis = $this->colisRepository->find($id);

        if (!$colis) {
            throw $this->createNotFoundException('Colis non trouvé');
        }

        // Vérifier que le colis appartient à l'entrepôt du gestionnaire
        if ($colis->getWarehouseOrigine() !== $warehouse && $colis->getWarehouseDestination() !== $warehouse) {
            throw $this->createAccessDeniedException('Ce colis ne relève pas de votre entrepôt');
        }

        $nouveauStatut = $request->request->get('statut');
        $commentaire = $request->request->get('commentaire', '');

        if (!in_array($nouveauStatut, [
            Colis::STATUT_EN_PREPARATION,
            Colis::STATUT_EN_TRANSIT,
            Colis::STATUT_ARRIVE_DESTINATION,
            Colis::STATUT_PRET_RETRAIT,
            Colis::STATUT_LIVRE,
            Colis::STATUT_DOMMAGE
        ], true)) {
            $this->addFlash('error', 'Statut invalide');
            return $this->redirectToRoute('app_entrepot_colis');
        }

        $colis->setStatutActuel($nouveauStatut);
        
        // Gérer les dates spéciales
        if ($nouveauStatut === Colis::STATUT_EN_TRANSIT && !$colis->getDateDepart()) {
            $colis->setDateDepart(new \DateTime());
        }
        if ($nouveauStatut === Colis::STATUT_ARRIVE_DESTINATION && !$colis->getDateLivraison()) {
            $colis->setDateLivraison(new \DateTime());
        }

        // Créer l'historique
        $historique = new HistoriqueStatut();
        $historique->setColis($colis)
            ->setStatut($nouveauStatut)
            ->setCommentaire($commentaire ?: 'Statut mis à jour par ' . $warehouse->getNom())
            ->setUserModificateur($user);

        $this->entityManager->persist($historique);
        $this->entityManager->flush();

        // Envoyer email de notification
        try {
            $this->emailNotificationService->sendStatutUpdateNotification($colis, $nouveauStatut, $commentaire);
        } catch (\Exception $e) {
            // Log l'erreur silencieusement
        }

        $this->addFlash('success', 'Statut mis à jour avec succès');
        return $this->redirectToRoute('app_entrepot_colis');
    }

    /**
     * Calcule les frais de transport
     */
    private function calculerFrais(string $origine, string $destination, string $poids): string
    {
        // Calcul basique pour l'instant
        return number_format((float) $poids * 500, 2, '.', '');
    }
}

