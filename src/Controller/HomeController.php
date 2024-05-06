<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ReviewRepository $reviewRepository,AlbumRepository $albumRepository,CommentRepository $commentRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'reviews' => $reviewRepository->findAll(),
            'albums' => $albumRepository->findAll(),
            'comments' => $commentRepository->findAll(),
        ]);
    }
}
