<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clients = [
            ['Lucas', 'Moreau', 'lucas.moreau@email.com', '0612345678', '12 Rue de la Paix, 75002 Paris'],
            ['Emma', 'Leroy', 'emma.leroy@email.com', '0623456789', '5 Avenue Victor Hugo, 69002 Lyon'],
            ['Hugo', 'Roux', 'hugo.roux@email.com', '0634567890', '8 Boulevard Gambetta, 13001 Marseille'],
            ['Chloé', 'Fournier', 'chloe.fournier@email.com', '0645678901', '22 Rue du Commerce, 31000 Toulouse'],
            ['Louis', 'Girard', 'louis.girard@email.com', '0656789012', '3 Place Bellecour, 69002 Lyon'],
            ['Léa', 'Bonnet', 'lea.bonnet@email.com', '0667890123', '15 Rue de Rivoli, 75004 Paris'],
            ['Gabriel', 'Lambert', 'gabriel.lambert@email.com', '0678901234', '9 Cours Mirabeau, 13100 Aix-en-Provence'],
            ['Manon', 'Fontaine', 'manon.fontaine@email.com', '0689012345', '27 Rue Sainte-Catherine, 33000 Bordeaux'],
            ['Raphaël', 'Rousseau', 'raphael.rousseau@email.com', '0690123456', '6 Quai des Chartrons, 33000 Bordeaux'],
            ['Jade', 'Vincent', 'jade.vincent@email.com', '0701234567', '18 Boulevard Haussmann, 75009 Paris'],
            ['Arthur', 'Muller', 'arthur.muller@email.com', '0712345678', '11 Rue Kléber, 67000 Strasbourg'],
            ['Louise', 'Lefèvre', 'louise.lefevre@email.com', '0723456789', '4 Place du Capitole, 31000 Toulouse'],
            ['Jules', 'Mercier', 'jules.mercier@email.com', '0734567890', '20 Rue de la République, 69001 Lyon'],
            ['Alice', 'Dumont', 'alice.dumont@email.com', '0745678901', '7 Rue du Faubourg, 75010 Paris'],
            ['Liam', 'Petit', 'liam.petit@email.com', '0756789012', '14 Avenue Jean Jaurès, 69007 Lyon'],
            ['Rose', 'Gauthier', 'rose.gauthier@email.com', '0767890123', '2 Place de la Comédie, 34000 Montpellier'],
            ['Adam', 'Garcia', 'adam.garcia@email.com', '0778901234', '30 Rue Nationale, 59000 Lille'],
            ['Inès', 'Thomas', 'ines.thomas@email.com', '0789012345', '16 Rue des Arts, 31000 Toulouse'],
            ['Noah', 'Robert', 'noah.robert@email.com', '0790123456', '10 Quai de la Daurade, 31000 Toulouse'],
            ['Mila', 'Richard', 'mila.richard@email.com', '0601234567', '25 Rue de la Liberté, 21000 Dijon'],
        ];

        foreach ($clients as $data) {
            $client = new Client();
            $client->setFirstname($data[0]);
            $client->setLastname($data[1]);
            $client->setEmail($data[2]);
            $client->setPhoneNumber($data[3]);
            $client->setAddress($data[4]);
            $client->setCreatedAt(new \DateTime('-' . rand(1, 365) . ' days'));
            $manager->persist($client);
        }

        $manager->flush();
    }
}
