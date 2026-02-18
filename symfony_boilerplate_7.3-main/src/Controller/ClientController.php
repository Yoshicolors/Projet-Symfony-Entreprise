<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Security\Voter\ClientVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clients')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index')]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::VIEW);

        $search = $request->query->get('search', '');
        $clients = $clientRepository->findAllOrderedByCreatedAt();

        if ($search) {
            $clients = array_filter($clients, function (Client $client) use ($search) {
                $searchLower = mb_strtolower($search);
                return str_contains(mb_strtolower($client->getFirstname()), $searchLower)
                    || str_contains(mb_strtolower($client->getLastname()), $searchLower)
                    || str_contains(mb_strtolower($client->getEmail()), $searchLower)
                    || str_contains(mb_strtolower($client->getPhoneNumber() ?? ''), $searchLower);
            });
        }

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_client_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CREATE);

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();
            $this->addFlash('success', 'Client créé avec succès.');
            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit')]
    public function edit(Client $client, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::EDIT, $client);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Client modifié avec succès.');
            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form,
            'client' => $client,
        ]);
    }
}
