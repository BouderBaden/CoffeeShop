<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Form\BrandType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    #[Route('/admin/brand', name: 'brand_show')]
    public function show(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $brandRepo = $entityManager->getRepository(Brand::class);
        $data = $brandRepo->findAll();
        $brand = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('brand/index.html.twig', [
            'brands' => $brand,
        ]);
    }


    #[Route('/admin/brand/create', name: 'brand_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($brand);
            $entityManager->flush();
            $this->addFlash('success', 'La marque ' . $brand->getName() . 'a bien été ajouté');
            return $this->redirectToRoute('brand_show');
        }

        return $this->render('brand/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/brand/delete/{id}', name: 'brand_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $brandRepo = $entityManager->getRepository(Brand::class);
        $productRepo = $entityManager->getRepository(Product::class);

        $brand = $brandRepo->find($id);

        if (!$brand) {
            throw $this->createNotFoundException('Aucune marque trouvé pour l\'id '.$id);
        }

        // Check if the brand is associated with any products
        $products = $productRepo->findBy(['brand' => $brand]);
        if (count($products) > 0) {
            // Prevent deletion and return a message
            $this->addFlash('error', 'Suppression impossible: cette marque est associée à des produits.');
            return $this->redirectToRoute('brand_show');
        }

        // Proceed with deletion if no products are associated
        $entityManager->remove($brand);
        $entityManager->flush();

        $this->addFlash('success', 'La marque a été supprimée.');
        return $this->redirectToRoute('brand_show');
    }


    #[Route('/admin/brand/update/{id}', 'brand_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id) :Response
    {
        $brandRepo = $entityManager->getRepository(brand::class);
        $brand = $brandRepo->find($id);

        if (!$brand){
            $this->createNotFoundException('Aucune marque trouvé pour l\'id '.$id);
        }
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications de la marque ont été enregistrées');
            return $this->redirectToRoute('brand_show');
        }
        return $this->render('brand/edit.html.twig', [
            'form' => $form->createView(),
            'brand' => $brand
        ]);
    }
}
