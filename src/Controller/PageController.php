<?php
// src/Controller/PageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContactoRepository;

class PageController extends AbstractController
{
    #[Route('/', name: 'inicio')]
    public function inicio(ContactoRepository $contactoRepository): Response
    {
        $contactos = $contactoRepository->findAll();

        return $this->render('inicio.html.twig', [
            'contactos' => $contactos
        ]);
    }
}

