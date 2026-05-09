<?php

namespace App\Service;

use App\Entity\Colis;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailNotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    /**
     * Envoie un email de notification au destinataire lors de l'enregistrement d'un colis
     */
    public function sendColisRegisteredNotification(Colis $colis): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('berekiaadamou14@gmail.com', 'Transit Colis'))
            ->to(new Address($colis->getDestinataireEmail(), $colis->getDestinataireNom()))
            ->subject('🎁 Votre colis a été enregistré - ' . $colis->getNumeroColis())
            ->htmlTemplate('emails/colis_registered.html.twig')
            ->context([
                'colis' => $colis,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur avec détails
            error_log('Erreur envoi email à ' . $colis->getDestinataireEmail() . ' : ' . $e->getMessage());
            // Relancer l'exception pour que le controller puisse la gérer
            throw $e;
        }
    }

    /**
     * Envoie un email de notification quand un colis change de statut
     */
    public function sendStatutUpdateNotification(Colis $colis, string $nouveauStatut, ?string $commentaire = null): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('berekiaadamou14@gmail.com', 'Transit Colis'))
            ->to(new Address($colis->getDestinataireEmail(), $colis->getDestinataireNom()))
            ->subject('📦 Mise à jour : ' . $this->formatStatut($nouveauStatut) . ' - ' . $colis->getNumeroColis())
            ->htmlTemplate('emails/statut_update.html.twig')
            ->context([
                'colis' => $colis,
                'nouveauStatut' => $nouveauStatut,
                'commentaire' => $commentaire,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email à ' . $colis->getDestinataireEmail() . ' : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Formate un statut pour l'affichage
     */
    private function formatStatut(string $statut): string
    {
        $statuts = [
            'cree' => 'Colis Créé',
            'enregistre' => 'Colis Enregistré',
            'expedition_en_cours' => 'Expédition en Cours',
            'en_preparation' => 'En Préparation',
            'en_transit' => 'En Transit',
            'arrive_destination' => 'Arrivé à Destination',
            'pret_retrait' => 'Prêt Pour Retrait',
            'livre' => 'Livré',
            'probleme' => 'Problème Détecté',
            'dommage' => 'Dommage',
        ];

        return $statuts[$statut] ?? $statut;
    }
}

