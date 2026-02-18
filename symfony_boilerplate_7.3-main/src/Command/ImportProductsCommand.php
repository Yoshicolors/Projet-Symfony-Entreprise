<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-products',
    description: 'Importer des produits depuis un fichier CSV'
)]
class ImportProductsCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'Nom du fichier CSV dans le dossier public');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');
        $filepath = __DIR__ . '/../../public/' . $filename;

        if (!file_exists($filepath)) {
            $io->error('Le fichier ' . $filename . ' n\'existe pas dans le dossier public.');
            return Command::FAILURE;
        }

        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            $io->error('Impossible d\'ouvrir le fichier.');
            return Command::FAILURE;
        }

        $header = fgetcsv($handle, 0, ',');
        if ($header === false) {
            $io->error('Le fichier est vide ou invalide.');
            fclose($handle);
            return Command::FAILURE;
        }

        $count = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) < 3) {
                continue;
            }

            $product = new Product();
            $product->setName(trim($row[0]));
            $product->setDescription(trim($row[1]));
            $product->setPrice(trim($row[2]));
            $product->setProductType(Product::TYPE_PHYSICAL);
            $this->entityManager->persist($product);
            $count++;
        }

        fclose($handle);
        $this->entityManager->flush();

        $io->success($count . ' produit(s) importé(s) avec succès.');

        return Command::SUCCESS;
    }
}
