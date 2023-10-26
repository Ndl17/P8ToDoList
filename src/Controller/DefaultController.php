<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function indexAction()
    {
        //retourne la vue de la page d'accueil
        return $this->render('default/index.html.twig');
    }
}
