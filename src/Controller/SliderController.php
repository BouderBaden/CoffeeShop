<?php

namespace App\Controller;

use App\Entity\Slider;
use App\Form\SliderType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SliderController extends AbstractController
{
    #[Route('/admin/slider', 'slider_show')]
    public function read(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request) : Response
    {
        $sliderRepo = $entityManager->getRepository(Slider::class);
        $sliders = $sliderRepo->findAll();
        $data = $paginator->paginate(
            $sliders, $request->query->getInt('page', 1),10
        );
        return $this->render('slider/index.html.twig', [
            'sliders' => $data,
        ]);
    }

    #[Route('/admin/slider/create', name: 'slider_form')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $slider = new Slider();
        $form = $this->createForm(SliderType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($slider);
            $entityManager->flush();
            $this->addFlash('success', 'Les informations du slider ont bien été enregistrées');
            return $this->redirectToRoute('slider_show');
        }
        return $this->render('slider/sliderCreate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/slider/update/{id}', 'slider_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id) :Response
    {
        $sliderRepo = $entityManager->getRepository(Slider::class);
        $slider = $sliderRepo->find($id);

        if (!$slider){
            $this->createNotFoundException('Aucun slider trouvé pour l\'id '.$id);
        }
        $form = $this->createForm(SliderType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications du slider ont été enregistrées');
            return $this->redirectToRoute('slider_show');
        }
        return $this->render('admin/sliderEdit.html.twig', [
            'form' => $form->createView(),
            'slider' => $slider
        ]);
    }

    #[Route('/admin/slider/delete/{id}', name: 'slider_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, $id) : Response
    {
        $sliderRepo = $entityManager->getRepository(Slider::class);
        $slider = $sliderRepo->find($id);

        if (!$slider) {
            throw $this->createNotFoundException('Aucun slider trouvé pour l\'id '.$id);
        }

        $entityManager->remove($slider);
        $entityManager->flush();

        $this->addFlash('success', 'Le slider a été supprimé');
        return $this->redirectToRoute('slider_show');
    }

}
