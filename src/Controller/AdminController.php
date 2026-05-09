<?php

namespace App\Controller;

use App\Entity\Colis;
use App\Entity\HistoriqueStatut;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\ColisRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly UserRepository $userRepository,
        private readonly ColisRepository $colisRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        // Statistiques générales
        $totalColis = count($this->colisRepository->findAll());
        $colisParStatut = [
            'enregistre' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_ENREGISTRE])),
            'en_preparation' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_EN_PREPARATION])),
            'en_transit' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_EN_TRANSIT])),
            'arrive_destination' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_ARRIVE_DESTINATION])),
            'pret_retrait' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_PRET_RETRAIT])),
            'livre' => count($this->colisRepository->findBy(['statutActuel' => Colis::STATUT_LIVRE])),
        ];

        $totalUsers = count($this->userRepository->findAll());
        $totalWarehouses = count($this->warehouseRepository->findAll());
        $colisRecents = $this->colisRepository->findBy([], ['dateEnregistrement' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'totalColis' => $totalColis,
            'colisParStatut' => $colisParStatut,
            'totalUsers' => $totalUsers,
            'totalWarehouses' => $totalWarehouses,
            'colisRecents' => $colisRecents,
        ]);
    }

    // ==================== GESTION ENTREPÔTS ====================

    #[Route('/warehouses', name: 'app_admin_warehouses')]
    public function warehouses(): Response
    {
        $warehouses = $this->warehouseRepository->findAll();

        return $this->render('admin/warehouses/list.html.twig', [
            'warehouses' => $warehouses,
        ]);
    }

    #[Route('/warehouses/new', name: 'app_admin_warehouse_new')]
    public function warehouseNew(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $pays = $request->request->get('pays');
            $adresse = $request->request->get('adresse');
            $ville = $request->request->get('ville');
            $telephone = $request->request->get('telephone');
            $email = $request->request->get('email');
            $horairesOuverture = $request->request->get('horaires_ouverture');
            $capaciteMaximale = $request->request->get('capacite_maximale');
            $responsableId = $request->request->get('responsable_id');

            $warehouse = new Warehouse();
            $warehouse->setNom($nom)
                ->setPays($pays)
                ->setAdresse($adresse)
                ->setVille($ville)
                ->setTelephone($telephone)
                ->setEmail($email)
                ->setHorairesOuverture($horairesOuverture)
                ->setCapaciteMaximale($capaciteMaximale);

            // Assigner le responsable si fourni
            if ($responsableId) {
                $responsable = $this->userRepository->find($responsableId);
                if ($responsable) {
                    $warehouse->setResponsable($responsable);
                }
            }

            $this->entityManager->persist($warehouse);
            $this->entityManager->flush();

            $this->addFlash('success', 'Entrepôt créé avec succès !');
            return $this->redirectToRoute('app_admin_warehouses');
        }

        // Récupérer les utilisateurs avec ROLE_ENTREPOT
        $usersEntrepot = $this->userRepository->findAll();
        $usersEntrepot = array_filter($usersEntrepot, function($user) {
            return in_array('ROLE_ENTREPOT', $user->getRoles(), true);
        });

        return $this->render('admin/warehouses/new.html.twig', [
            'usersEntrepot' => $usersEntrepot,
        ]);
    }

    #[Route('/warehouses/{id}/edit', name: 'app_admin_warehouse_edit')]
    public function warehouseEdit(Request $request, int $id): Response
    {
        $warehouse = $this->warehouseRepository->find($id);
        if (!$warehouse) {
            throw $this->createNotFoundException('Entrepôt non trouvé');
        }

        if ($request->isMethod('POST')) {
            $warehouse->setNom($request->request->get('nom'))
                ->setPays($request->request->get('pays'))
                ->setAdresse($request->request->get('adresse'))
                ->setVille($request->request->get('ville'))
                ->setTelephone($request->request->get('telephone'))
                ->setEmail($request->request->get('email'))
                ->setHorairesOuverture($request->request->get('horaires_ouverture'))
                ->setCapaciteMaximale($request->request->get('capacite_maximale'));

            // Assigner le responsable si fourni
            $responsableId = $request->request->get('responsable_id');
            if ($responsableId) {
                $responsable = $this->userRepository->find($responsableId);
                if ($responsable) {
                    $warehouse->setResponsable($responsable);
                }
            } else {
                $warehouse->setResponsable(null);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Entrepôt modifié avec succès !');
            return $this->redirectToRoute('app_admin_warehouses');
        }

        // Récupérer les utilisateurs avec ROLE_ENTREPOT
        $usersEntrepot = $this->userRepository->findAll();
        $usersEntrepot = array_filter($usersEntrepot, function($user) {
            return in_array('ROLE_ENTREPOT', $user->getRoles(), true);
        });

        return $this->render('admin/warehouses/edit.html.twig', [
            'warehouse' => $warehouse,
            'usersEntrepot' => $usersEntrepot,
        ]);
    }

    // ==================== GESTION UTILISATEURS ====================

    #[Route('/users', name: 'app_admin_users')]
    public function users(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/new', name: 'app_admin_user_new')]
    public function userNew(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $telephone = $request->request->get('telephone');
            $password = $request->request->get('password');
            $roles = $request->request->all('roles');

            // Vérifier si l'email existe déjà
            $userExistant = $this->userRepository->findOneBy(['email' => $email]);
            if ($userExistant) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->redirectToRoute('app_admin_user_new');
            }

            $user = new User();
            $user->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email)
                ->setTelephone($telephone)
                ->setRoles($roles);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/users/new.html.twig');
    }

    #[Route('/users/{id}/edit', name: 'app_admin_user_edit')]
    public function userEdit(Request $request, int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setTelephone($request->request->get('telephone'));

            $newRoles = $request->request->all('roles');
            $user->setRoles($newRoles);

            $newPassword = $request->request->get('password');
            if ($newPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès !');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
        ]);
    }

    // ==================== GESTION COLIS ====================

    #[Route('/colis/new', name: 'app_admin_colis_new')]
    public function colisNew(Request $request): Response
    {
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
                return $this->redirectToRoute('app_admin_colis_new');
            }

            $colis = new Colis();
            $colis->setNumeroColis($numeroColis)
                ->setPoids($poids)
                ->setDescription($description)
                ->setPaysOrigine($paysOrigine)
                ->setPaysDestination($paysDestination)
                ->setStatutActuel(Colis::STATUT_ENREGISTRE)
                ->setStatutPaiement(Colis::PAIEMENT_NON_PAYE)
                ->setExpediteurNom($expediteurNom)
                ->setExpediteurTel($expediteurTel)
                ->setExpediteurEmail($expediteurEmail)
                ->setDestinataireNom($destinataireNom)
                ->setDestinataireTel($destinataireTel)
                ->setDestinataireEmail($destinataireEmail)
                ->setDestinataireAdresse($destinataireAdresse);

            // Déterminer les entrepôts selon les pays
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

            // Créer l'historique initial
            $historique = new HistoriqueStatut();
            $historique->setColis($colis)
                ->setStatut(Colis::STATUT_ENREGISTRE)
                ->setCommentaire('Colis enregistré par l\'administration')
                ->setUserModificateur($this->getUser());

            $this->entityManager->persist($colis);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();

            $this->addFlash('success', 'Colis créé avec succès ! Numéro de suivi : ' . $numeroColis);
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $warehouses = $this->warehouseRepository->findAll();

        return $this->render('admin/colis/new.html.twig', [
            'warehouses' => $warehouses,
        ]);
    }

    #[Route('/colis', name: 'app_admin_colis')]
    public function colis(): Response
    {
        $colis = $this->colisRepository->findBy([], ['dateEnregistrement' => 'DESC']);

        return $this->render('admin/colis/list.html.twig', [
            'colis' => $colis,
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
        // Calcul basique pour l'instant
        return number_format((float) $poids * 500, 2, '.', '');
    }
}

