<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Product\Step\ProductConfirmationStepType;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use App\Service\ProductExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::VIEW);

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAllOrderedByPriceDesc(),
        ]);
    }

    #[Route('/new/{step}', name: 'app_product_new', defaults: ['step' => 1])]
    public function new(int $step, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::CREATE);

        $productData = $session->get('product_form_data', []);
        $product = new Product();

        if (!empty($productData)) {
            if (isset($productData['productType'])) {
                $product->setProductType($productData['productType']);
            }
            if (isset($productData['name'])) {
                $product->setName($productData['name']);
            }
            if (isset($productData['description'])) {
                $product->setDescription($productData['description']);
            }
            if (isset($productData['price'])) {
                $product->setPrice($productData['price']);
            }
            if (isset($productData['weight'])) {
                $product->setWeight($productData['weight']);
            }
            if (isset($productData['dimensions'])) {
                $product->setDimensions($productData['dimensions']);
            }
            if (isset($productData['stock'])) {
                $product->setStock($productData['stock']);
            }
            if (isset($productData['licenseDuration'])) {
                $product->setLicenseDuration($productData['licenseDuration']);
            }
            if (isset($productData['maxActivations'])) {
                $product->setMaxActivations($productData['maxActivations']);
            }
        }

        $steps = $this->buildSteps($product);
        $totalSteps = count($steps);

        if ($step < 1 || $step > $totalSteps) {
            $step = 1;
        }

        $currentStepConfig = $steps[$step - 1];

        if ($currentStepConfig['type'] === 'summary') {
            if ($request->isMethod('POST')) {
                $em->persist($product);
                $em->flush();
                $session->remove('product_form_data');
                $this->addFlash('success', 'Produit créé avec succès.');
                return $this->redirectToRoute('app_product_index');
            }

            return $this->render('product/new.html.twig', [
                'step' => $step,
                'totalSteps' => $totalSteps,
                'steps' => $steps,
                'product' => $product,
                'isSummary' => true,
                'form' => null,
                'stepTitle' => $currentStepConfig['title'],
            ]);
        }

        $form = $this->createForm($currentStepConfig['type'], $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productData = array_merge($productData, $this->extractFormData($product, $step));
            $session->set('product_form_data', $productData);

            $nextStep = $step + 1;
            $updatedSteps = $this->buildSteps($product);

            if ($nextStep > count($updatedSteps)) {
                $nextStep = count($updatedSteps);
            }

            return $this->redirectToRoute('app_product_new', ['step' => $nextStep]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
            'step' => $step,
            'totalSteps' => $totalSteps,
            'steps' => $steps,
            'product' => $product,
            'isSummary' => false,
            'stepTitle' => $currentStepConfig['title'],
        ]);
    }

    #[Route('/{id}/edit/{step}', name: 'app_product_edit', defaults: ['step' => 1])]
    public function edit(Product $product, int $step, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product);

        $sessionKey = 'product_edit_data_' . $product->getId();

        if ($step === 1 && !$request->isMethod('POST')) {
            $session->remove($sessionKey);
        }

        $steps = $this->buildSteps($product);
        $totalSteps = count($steps);

        if ($step < 1 || $step > $totalSteps) {
            $step = 1;
        }

        $currentStepConfig = $steps[$step - 1];

        if ($currentStepConfig['type'] === 'summary') {
            if ($request->isMethod('POST')) {
                $em->flush();
                $session->remove($sessionKey);
                $this->addFlash('success', 'Produit modifié avec succès.');
                return $this->redirectToRoute('app_product_index');
            }

            return $this->render('product/edit.html.twig', [
                'step' => $step,
                'totalSteps' => $totalSteps,
                'steps' => $steps,
                'product' => $product,
                'isSummary' => true,
                'form' => null,
                'stepTitle' => $currentStepConfig['title'],
            ]);
        }

        $form = $this->createForm($currentStepConfig['type'], $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nextStep = $step + 1;
            $updatedSteps = $this->buildSteps($product);

            if ($nextStep > count($updatedSteps)) {
                $nextStep = count($updatedSteps);
            }

            return $this->redirectToRoute('app_product_edit', ['id' => $product->getId(), 'step' => $nextStep]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
            'step' => $step,
            'totalSteps' => $totalSteps,
            'steps' => $steps,
            'product' => $product,
            'isSummary' => false,
            'stepTitle' => $currentStepConfig['title'],
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/export', name: 'app_product_export')]
    public function export(ProductExportService $exportService): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::EXPORT);

        return $exportService->exportToCsv();
    }

    private function buildSteps(Product $product): array
    {
        $steps = [
            ['title' => 'Type de produit', 'type' => ProductTypeStepType::class],
            ['title' => 'Détails', 'type' => ProductDetailsStepType::class],
        ];

        if ($product->getProductType() === Product::TYPE_PHYSICAL) {
            $steps[] = ['title' => 'Logistique', 'type' => ProductLogisticsStepType::class];
        } elseif ($product->getProductType() === Product::TYPE_DIGITAL) {
            $steps[] = ['title' => 'Licence / Accès', 'type' => ProductLicenseStepType::class];
        }

        if ($product->getPrice() !== null && (float) $product->getPrice() > 1000) {
            $steps[] = ['title' => 'Confirmation prix élevé', 'type' => ProductConfirmationStepType::class];
        }

        $steps[] = ['title' => 'Récapitulatif', 'type' => 'summary'];

        return $steps;
    }

    private function extractFormData(Product $product, int $step): array
    {
        $data = [];

        if ($step === 1) {
            $data['productType'] = $product->getProductType();
        } elseif ($step === 2) {
            $data['name'] = $product->getName();
            $data['description'] = $product->getDescription();
            $data['price'] = $product->getPrice();
        } else {
            if ($product->isPhysical()) {
                $data['weight'] = $product->getWeight();
                $data['dimensions'] = $product->getDimensions();
                $data['stock'] = $product->getStock();
            } else {
                $data['licenseDuration'] = $product->getLicenseDuration();
                $data['maxActivations'] = $product->getMaxActivations();
            }
        }

        return $data;
    }
}
