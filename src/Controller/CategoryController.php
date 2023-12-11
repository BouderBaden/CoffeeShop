<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Form\BrandType;
use App\Form\CatgoeryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'category_show')]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $categoryRepo = $entityManager->getRepository(Category::class);
        $data = $categoryRepo->findAll();
        $category = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        return $this->render('category/index.html.twig', [
            'categorys' => $category,
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CatgoeryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a bien été ajoutée');
            return $this->redirectToRoute('category_show');
        }
        return $this->render('category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'category_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $categoryRepo = $entityManager->getRepository(Category::class);
        $category = $categoryRepo->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Aucune catégorie trouvé pour l\'id '.$id);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'La catégorie a été supprimé');
        return $this->redirectToRoute('category_show');
    }

    #[Route('/admin/category/update/{id}', 'category_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id) :Response
    {
        $categoryRepo = $entityManager->getRepository(Category::class);
        $category = $categoryRepo->find($id);

        if (!$category){
            $this->createNotFoundException('Aucune catégorie trouvé pour l\'id '.$id);
        }
        $form = $this->createForm(CatgoeryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications de la catégorie ont été enregistrées');
            return $this->redirectToRoute('category_show');
        }
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}
