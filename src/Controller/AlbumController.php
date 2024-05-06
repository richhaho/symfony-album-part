<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/album")
 */
class AlbumController extends AbstractController
{
    /**
     * @Route("/", name="app_album_index", methods={"GET"})
     */
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('album/index.html.twig', [
            'albums' => $albumRepository->findAll(),
        ]);
    }
    /**
     * @Route("/api/albums", name="api_album_index", methods={"GET"})
     */
    public function apiIndex(AlbumRepository $albumRepository): JsonResponse
    {
        $albums = $albumRepository->findAll();
        $data = [];

        foreach ($albums as $album) {
            $data[] = [
                'id' => $album->getId(),
                'title' => $album->getTitle(),
                // Add other album properties you want to expose in the API response
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="app_album_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AlbumRepository $albumRepository): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumRepository->add($album);
            return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/new.html.twig', [
            'album' => $album,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/api/albums/new", name="api_album_new", methods={"POST"})
     */
    public function apiNew(Request $request, AlbumRepository $albumRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($request->get('title'))) {
            return new JsonResponse(['error' => 'Title is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $album = new Album();
        $album->setTitle($request->get('title'));
        $album->setGenre($request->get('genre'));
        $album->setArtist($request->get('artist'));
        $album->setReleaseDate(new \DateTime($request->get('release_date')));
        $album->setRatingCount($request->get('rating_count'));
        $album->setAverageRating($request->get('average_rating'));
        $album->setReviewCount($request->get('review_count'));

        // Set other properties for the album if needed.

        // Persist the new album to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($album);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Album created successfully.'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="app_album_show", methods={"GET"})
     */
    public function show(Album $album): Response
    {
        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    /**
     * @Route("/api/albums/{id}", name="api_album_show", methods={"GET"})
     */
    public function apiShow(Album $album): JsonResponse
    {
        $data = [
            'id' => $album->getId(),
            'title' => $album->getTitle(),
            'artist' => $album->getArtist(),
            'genre' => $album->getGenre(),
            'release_date' => $album->getReleaseDate()->format('Y-m-d'),
            'rating_count' => $album->getRatingCount(),
            'average_rating' => $album->getAverageRating(),
            'review_count' => $album->getReviewCount(),
            // Format the release date as desired
            // Add other album properties you want to expose in the API response
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/edit", name="app_album_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumRepository->add($album);
            return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/edit.html.twig', [
            'album' => $album,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/albums/{id}/edit", name="api_album_edit", methods={"PUT"})
     */
    public function apiEdit(Request $request, Album $album): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if the "title" property is provided in the JSON data and is not empty
        //$title = $request->get('title') ?? null;
        if ($request->get('title') !== null && !empty($request->get('title'))) {
            $album->setTitle($request->get('title'));
        }
        if ($request->get('artist') !== null && !empty($request->get('artist'))) {
            $album->setTitle($request->get('artist'));
        }
        if ($request->get('genre') !== null && !empty($request->get('genre'))) {
            $album->setTitle($request->get('genre'));
        }
        if ($request->get('release_date') !== null && !empty($request->get('release_date'))) {
            $album->setReleaseDate(new \DateTime($request->get('release_date')));
        }
        if ($request->get('genre') !== null && !empty($request->get('genre'))) {
            $album->setTitle($request->get('genre'));
        }

        if ($request->get('rating_count') !== null && !empty($request->get('rating_count'))) {
            $album->setTitle($request->get('rating_count'));
        }
        if ($request->get('average_rating') !== null && !empty($request->get('average_rating'))) {
            $album->setTitle($request->get('average_rating'));
        }
        if ($request->get('review_count') !== null && !empty($request->get('review_count'))) {
            $album->setTitle($request->get('review_count'));
        }


        // Check other properties and update them if provided in the JSON data
        // For example:


        // Persist the updated album to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Album updated successfully.'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="app_album_delete", methods={"POST"})
     */
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {
            $albumRepository->remove($album);
        }

        return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/api/albums/{id}/delete", name="api_album_delete", methods={"DELETE"})
     */
    public function apiDelete(Request $request, Album $album, AlbumRepository $albumRepository): JsonResponse
    {


        // Perform the deletion of the album
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($album);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Album deleted successfully.'], JsonResponse::HTTP_OK);
    }
}
