<?php

namespace App\DataFixtures;

use App\Entity\Warehouse;
use App\Entity\Tarif;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer les entrepôts
        $warehouseNigeria = new Warehouse();
        $warehouseNigeria->setNom('Entrepôt Principal Lagos');
        $warehouseNigeria->setPays('Nigeria');
        $warehouseNigeria->setAdresse('Plot 1234, Airport Road, Ikeja, Lagos');
        $warehouseNigeria->setVille('Lagos');
        $warehouseNigeria->setTelephone('+234 1 234 5678');
        $warehouseNigeria->setEmail('lagos@transitcolis.com');
        $warehouseNigeria->setHorairesOuverture('Lundi - Vendredi: 8h - 18h / Samedi: 9h - 14h');
        $warehouseNigeria->setCapaciteMaximale('10000.00');
        $manager->persist($warehouseNigeria);

        $warehouseBenin = new Warehouse();
        $warehouseBenin->setNom('Entrepôt Principal Cotonou');
        $warehouseBenin->setPays('Benin');
        $warehouseBenin->setAdresse('Boulevard du Général de Gaulle, Quartier Ganhi');
        $warehouseBenin->setVille('Cotonou');
        $warehouseBenin->setTelephone('+229 21 23 45 67');
        $warehouseBenin->setEmail('cotonou@transitcolis.com');
        $warehouseBenin->setHorairesOuverture('Lundi - Vendredi: 8h - 18h / Samedi: 9h - 14h');
        $warehouseBenin->setCapaciteMaximale('8000.00');
        $manager->persist($warehouseBenin);

        // Créer les tarifs Nigeria → Bénin
        $tarifNGStandard = new Tarif();
        $tarifNGStandard->setRoute('Nigeria → Bénin');
        $tarifNGStandard->setPoidsMin(null);
        $tarifNGStandard->setPoidsMax(null);
        $tarifNGStandard->setPrixParKg('500.00');
        $tarifNGStandard->setDateDebut(new \DateTime());
        $tarifNGStandard->setDescription('Tarif standard pour tous poids');
        $tarifNGStandard->setActif(true);
        $manager->persist($tarifNGStandard);

        $tarifNGExpress = new Tarif();
        $tarifNGExpress->setRoute('Nigeria → Bénin Express');
        $tarifNGExpress->setPoidsMin(null);
        $tarifNGExpress->setPoidsMax(null);
        $tarifNGExpress->setPrixParKg('750.00');
        $tarifNGExpress->setDateDebut(new \DateTime());
        $tarifNGExpress->setDescription('Service express - Livraison en 24-48h');
        $tarifNGExpress->setActif(true);
        $manager->persist($tarifNGExpress);

        // Créer les tarifs Bénin → Nigeria
        $tarifBNStandard = new Tarif();
        $tarifBNStandard->setRoute('Bénin → Nigeria');
        $tarifBNStandard->setPoidsMin(null);
        $tarifBNStandard->setPoidsMax(null);
        $tarifBNStandard->setPrixParKg('550.00');
        $tarifBNStandard->setDateDebut(new \DateTime());
        $tarifBNStandard->setDescription('Tarif standard pour tous poids');
        $tarifBNStandard->setActif(true);
        $manager->persist($tarifBNStandard);

        $tarifBNExpress = new Tarif();
        $tarifBNExpress->setRoute('Bénin → Nigeria Express');
        $tarifBNExpress->setPoidsMin(null);
        $tarifBNExpress->setPoidsMax(null);
        $tarifBNExpress->setPrixParKg('800.00');
        $tarifBNExpress->setDateDebut(new \DateTime());
        $tarifBNExpress->setDescription('Service express - Livraison en 24-48h');
        $tarifBNExpress->setActif(true);
        $manager->persist($tarifBNExpress);

        $manager->flush();
    }
}

