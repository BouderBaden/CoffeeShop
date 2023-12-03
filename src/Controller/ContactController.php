<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($contact);
            $manager->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/contact/', 'contact_show')]
    public function show(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator) : Response
    {
        $contactRepo = $entityManager->getRepository(ContactRepository::class);
        $contact = $contactRepo->findAll();
        $data = $paginator->paginate($contact, $request->query->getInt('page', 1),10);
        return $this->render('contact/show.html.twig', [
            'contact' => $data
        ]);
    }
}
