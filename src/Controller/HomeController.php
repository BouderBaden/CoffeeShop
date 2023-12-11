<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
        $productRepo = $entityManager->getRepository(Product::class);
        $data = $productRepository->findByBestSellers();
        $product = $paginator->paginate($data, $request->query->getInt('page', 1), 10);
        return $this->render('home/index.html.twig', [
           'products' => $data
        ]);
    }
}