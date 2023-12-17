<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Slider;
use App\Form\ProductType;
use App\Form\SliderType;
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

    #[Route('/admin/products/update/{id}', name: 'products_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id) : Response
    {
        $productRepo = $entityManager->getRepository(Product::class);
        $product = $productRepo->find($id);

        if (!$product){
            $this->createNotFoundException('Aucun Produit trouvé pour l\'id '.$id);
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications du produit ont été enregistrées');

            return $this->redirectToRoute('products_show');
        }
        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'products' => $product
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'products_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id) : Response
    {
        $productRepo = $entityManager->getRepository(Product::class);
        $product = $productRepo->find($id);

        if (!$product){
            $this->createNotFoundException('Aucun Produit trouvé pour l\'id '.$id);
        }
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a été supprimé');

            return $this->redirectToRoute('products_show');
    }
}
