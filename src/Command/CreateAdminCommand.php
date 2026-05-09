<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si un admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@transitcolis.com']);
        if ($existingAdmin) {
            $io->warning('Un admin avec cet email existe déjà.');
            return Command::FAILURE;
        }

        // Créer l'admin
        $admin = new User();
        $admin->setEmail('admin@transitcolis.com');
        $admin->setNom('Admin');
        $admin->setPrenom('System');
        $admin->setTelephone('+22912345678');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setIsVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success([
            '✅ Utilisateur admin créé avec succès !',
            '📧 Email: admin@transitcolis.com',
            '🔑 Mot de passe: admin123',
            '⚠️  Changez ce mot de passe immédiatement après la première connexion !'
        ]);

        return Command::SUCCESS;
    }
}

