<?php

namespace App\Controller;

use App\Entity\Colis;
use App\Entity\HistoriqueStatut;
use App\Entity\Tarif;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\ColisRepository;
use App\Repository\TarifRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ColisController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ColisRepository $colisRepository,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly TarifRepository $tarifRepository
    ) {
    }

    #[Route('/colis', name: 'app_colis_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $colis = $this->colisRepository->findByUser($user->getId());

        return $this->render('colis/index.html.twig', [
            'colis' => $colis,
        ]);
    }

    #[Route('/colis/new', name: 'app_colis_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $numeroColis = trim($request->request->get('numero_colis'));
            
            // CAS 1: Créer l'expédition à partir d'un colis existant créé par l'entrepôt
            if ($numeroColis) {
                $colisExistant = $this->colisRepository->findByNumero($numeroColis);
                
                if (!$colisExistant) {
                    $this->addFlash('error', 'Aucun colis trouvé avec ce numéro de suivi.');
                    return $this->render('colis/new.html.twig', ['numero_search' => $numeroColis]);
                }

                if ($colisExistant->getUser()) {
                    $this->addFlash('error', 'Ce colis est déjà associé à un client.');
                    return $this->render('colis/new.html.twig', ['numero_search' => $numeroColis]);
                }

                // Associer le colis au client connecté et changer le statut
                $colisExistant->setUser($user);
                $colisExistant->setStatutActuel(Colis::STATUT_EXPEDITION_EN_COURS);
                
                // Créer un historique pour l'expédition
                $historiqueExpedition = new HistoriqueStatut();
                $historiqueExpedition->setColis($colisExistant)
                    ->setStatut(Colis::STATUT_EXPEDITION_EN_COURS)
                    ->setCommentaire('Expédition créée par le client')
                    ->setUserModificateur($user);

                $this->entityManager->persist($historiqueExpedition);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre expédition a été créée avec succès ! Numéro de suivi : ' . $numeroColis);
                return $this->redirectToRoute('app_colis_show', ['id' => $colisExistant->getId()]);
            }

            // CAS 2: Créer un colis classique (ancien workflow)
            $poids = $request->request->get('poids');
            $description = $request->request->get('description');
            $paysOrigine = $request->request->get('pays_origine');
            $paysDestination = $request->request->get('pays_destination');
            
            $destinataireNom = $request->request->get('destinataire_nom');
            $destinataireTel = $request->request->get('destinataire_tel');
            $destinataireEmail = $request->request->get('destinataire_email');
            $destinataireAdresse = $request->request->get('destinataire_adresse');

            // Validation
            if (!$poids || !$paysOrigine || !$paysDestination || !$destinataireNom || !$destinataireTel || !$destinataireEmail) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('app_colis_new');
            }

            // Générer un nouveau numéro de suivi
            $numeroColis = $this->genererNumeroColis();

            // Créer le colis
            $colis = new Colis();
            $colis->setUser($user);
            $colis->setNumeroColis($numeroColis);
            $colis->setPoids($poids);
            $colis->setDescription($description);
            $colis->setPaysOrigine($paysOrigine);
            $colis->setPaysDestination($paysDestination);
            $colis->setStatutActuel(Colis::STATUT_ENREGISTRE);
            $colis->setStatutPaiement(Colis::PAIEMENT_NON_PAYE);
            $colis->setExpediteurNom($user->getFullName());
            $colis->setExpediteurTel($user->getTelephone());
            $colis->setExpediteurEmail($user->getEmail());
            $colis->setDestinataireNom($destinataireNom);
            $colis->setDestinataireTel($destinataireTel);
            $colis->setDestinataireEmail($destinataireEmail);
            $colis->setDestinataireAdresse($destinataireAdresse);

            // Déterminer les entrepôts
            if ($paysOrigine === 'Nigeria') {
                $warehouseOrigine = $this->warehouseRepository->findByNom('Entrepôt Principal Lagos');
            } else {
                $warehouseOrigine = $this->warehouseRepository->findByNom('Entrepôt Principal Cotonou');
            }
            
            if ($paysDestination === 'Nigeria') {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Lagos');
            } else {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Cotonou');
            }

            if ($warehouseOrigine) {
                $colis->setWarehouseOrigine($warehouseOrigine);
            }
            if ($warehouseDestination) {
                $colis->setWarehouseDestination($warehouseDestination);
            }

            // Calculer les frais
            $montantFrais = $this->calculerFrais($paysOrigine, $paysDestination, $poids);
            $colis->setMontantFrais($montantFrais);

            // Créer l'historique
            $historique = new HistoriqueStatut();
            $historique->setColis($colis);
            $historique->setStatut(Colis::STATUT_ENREGISTRE);
            $historique->setCommentaire('Colis enregistré dans le système');
            $historique->setUserModificateur($user);

            $this->entityManager->persist($colis);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre expédition a été créée avec succès ! Numéro de suivi : ' . $numeroColis);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('colis/new.html.twig');
    }

    #[Route('/colis/new/manual', name: 'app_colis_new_manual')]
    #[IsGranted('ROLE_USER')]
    public function newManual(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $poids = $request->request->get('poids');
            $description = $request->request->get('description');
            $paysOrigine = $request->request->get('pays_origine');
            $paysDestination = $request->request->get('pays_destination');
            
            $destinataireNom = $request->request->get('destinataire_nom');
            $destinataireTel = $request->request->get('destinataire_tel');
            $destinataireEmail = $request->request->get('destinataire_email');
            $destinataireAdresse = $request->request->get('destinataire_adresse');

            // Validation
            if (!$poids || !$paysOrigine || !$paysDestination || !$destinataireNom || !$destinataireTel || !$destinataireEmail) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('app_colis_new_manual');
            }

            // Générer un nouveau numéro de suivi
            $numeroColis = $this->genererNumeroColis();

            // Créer le colis
            $colis = new Colis();
            $colis->setUser($user);
            $colis->setNumeroColis($numeroColis);
            $colis->setPoids($poids);
            $colis->setDescription($description);
            $colis->setPaysOrigine($paysOrigine);
            $colis->setPaysDestination($paysDestination);
            $colis->setStatutActuel(Colis::STATUT_ENREGISTRE);
            $colis->setStatutPaiement(Colis::PAIEMENT_NON_PAYE);
            $colis->setExpediteurNom($user->getFullName());
            $colis->setExpediteurTel($user->getTelephone());
            $colis->setExpediteurEmail($user->getEmail());
            $colis->setDestinataireNom($destinataireNom);
            $colis->setDestinataireTel($destinataireTel);
            $colis->setDestinataireEmail($destinataireEmail);
            $colis->setDestinataireAdresse($destinataireAdresse);

            // Déterminer les entrepôts
            if ($paysOrigine === 'Nigeria') {
                $warehouseOrigine = $this->warehouseRepository->findByNom('Entrepôt Principal Lagos');
            } else {
                $warehouseOrigine = $this->warehouseRepository->findByNom('Entrepôt Principal Cotonou');
            }
            
            if ($paysDestination === 'Nigeria') {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Lagos');
            } else {
                $warehouseDestination = $this->warehouseRepository->findByNom('Entrepôt Principal Cotonou');
            }

            if ($warehouseOrigine) {
                $colis->setWarehouseOrigine($warehouseOrigine);
            }
            if ($warehouseDestination) {
                $colis->setWarehouseDestination($warehouseDestination);
            }

            // Calculer les frais
            $montantFrais = $this->calculerFrais($paysOrigine, $paysDestination, $poids);
            $colis->setMontantFrais($montantFrais);

            // Créer l'historique
            $historique = new HistoriqueStatut();
            $historique->setColis($colis);
            $historique->setStatut(Colis::STATUT_ENREGISTRE);
            $historique->setCommentaire('Colis enregistré dans le système');
            $historique->setUserModificateur($user);

            $this->entityManager->persist($colis);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre expédition a été créée avec succès ! Numéro de suivi : ' . $numeroColis);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('colis/new_manual.html.twig');
    }

    #[Route('/colis/{id}', name: 'app_colis_show')]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): Response
    {
        $colis = $this->colisRepository->find($id);
        
        if (!$colis) {
            throw $this->createNotFoundException('Colis non trouvé');
        }

        // Vérifier que l'utilisateur peut voir ce colis
        $user = $this->getUser();
        if ($colis->getUser() && $colis->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce colis');
        }

        return $this->render('colis/show.html.twig', [
            'colis' => $colis,
        ]);
    }

    #[Route('/colis/{id}/edit', name: 'app_colis_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        $colis = $this->colisRepository->find($id);
        
        if (!$colis) {
            throw $this->createNotFoundException('Colis non trouvé');
        }

        if ($request->isMethod('POST')) {
            $nouveauStatut = $request->request->get('statut');
            $commentaire = $request->request->get('commentaire');

            if ($nouveauStatut && $nouveauStatut !== $colis->getStatutActuel()) {
                // Créer un nouvel historique
                $historique = new HistoriqueStatut();
                $historique->setColis($colis);
                $historique->setStatut($nouveauStatut);
                $historique->setCommentaire($commentaire ?? 'Changement de statut');
                $historique->setUserModificateur($this->getUser());

                $colis->setStatutActuel($nouveauStatut);
                $colis->addHistoriqueStatut($historique);

                $this->entityManager->flush();

                $this->addFlash('success', 'Statut mis à jour avec succès !');
                
                // TODO: Envoyer email de notification au destinataire
            }

            return $this->redirectToRoute('app_colis_show', ['id' => $id]);
        }

        return $this->render('colis/edit.html.twig', [
            'colis' => $colis,
            'statuts' => Colis::getStatuts(),
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

    /**
     * Calcule les frais de transport
     */
    private function calculerFrais(string $origine, string $destination, string $poids): string
    {
        $route = $origine . ' → ' . $destination;
        $tarif = $this->tarifRepository->findTarifActif($route, $poids);

        if ($tarif) {
            try {
                return $tarif->calculerPrix($poids);
            } catch (\Exception $e) {
                // Si le tarif n'est pas applicable, utiliser un calcul basique
                return number_format((float) $poids * 500, 2, '.', '');
            }
        }

        // Tarif par défaut si aucun tarif trouvé
        return number_format((float) $poids * 500, 2, '.', '');
    }
}
