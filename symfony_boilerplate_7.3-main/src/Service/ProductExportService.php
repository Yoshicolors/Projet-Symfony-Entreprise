<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExportService
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function exportToCsv(): StreamedResponse
    {
        $products = $this->productRepository->findAllOrderedByPriceDesc();

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'description', 'price'], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->getName(),
                    $product->getDescription(),
                    $product->getPrice(),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits_export.csv"');

        return $response;
    }
}
