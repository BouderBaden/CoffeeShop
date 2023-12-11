<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $productRepo = $entityManager->getRepository(Product::class);
        $data = $productRepo->findAll();
        $product = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('product/index.html.twig', [
            'products' => $product,
        ]);
    }

    #[Route('/admin/products', name: 'products_show')]
    public function show(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $productRepo = $entityManager->getRepository(Product::class);
        $data = $productRepo->findAll();
        $product = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('product/show.html.twig', [
            'products' => $product,
        ]);
    }

    #[Route('/admin/products/create', name: 'products_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $products = new Product();
        $form = $this->createForm(ProductType::class, $products);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($products);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a bien été ajouté');
            return $this->redirectToRoute('products_show');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }
}
