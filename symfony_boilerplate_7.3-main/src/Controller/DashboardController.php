<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        ClientRepository $clientRepository
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'userCount' => count($userRepository->findAll()),
            'productCount' => count($productRepository->findAll()),
            'clientCount' => count($clientRepository->findAll()),
            'recentClients' => $clientRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'topProducts' => $productRepository->findBy([], ['price' => 'DESC'], 5),
        ]);
    }
}
