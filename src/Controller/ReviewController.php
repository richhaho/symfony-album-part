<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="app_review_index", methods={"GET"})
     */
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }
    /**
     * @Route("/api/reviews", name="api_review_index", methods={"GET"})
     */
    public function apiIndex(ReviewRepository $reviewRepository): JsonResponse
    {
        $reviews = $reviewRepository->findAll();

        $data = [];
        foreach ($reviews as $review) {
            $data[] = [
                'id' => $review->getId(),
                'review_text' => $review->getReviewText(),
                'rating' => $review->getRating(),
                'date_posted' => $review->getDatePosted()->format('Y-m-d H:i:s'),
                'like_count' => $review->getLikeCount(),
                'dislike_count' => $review->getDislikeCount(),
                // If you want to include the album and user details, you can add them as well
                // 'album' => [
                //     'id' => $review->getAlbum()->getId(),
                //     'title' => $review->getAlbum()->getTitle(),
                // ],
                // 'user' => [
                //     'id' => $review->getUserId()->getId(),
                //     'username' => $review->getUserId()->getUsername(),
                // ],
            ];
        }

        return new JsonResponse($data);
    }


    /**
     * @Route("/new", name="app_review_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReviewRepository $reviewRepository): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review);
            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/api/reviews/new", name="api_review_new", methods={"POST"})
     */
    public function apiNew(Request $request, ReviewRepository $reviewRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if the required data (e.g., "review_text", "rating") is provided in the JSON payload
        if (empty($request->get('review_text')) || empty($request->get('rating'))) {
            return new JsonResponse(['error' => 'Review text and rating are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Create a new Review entity and set its properties based on the data received
        $review = new Review();
        $review->setReviewText($request->get('review_text'));
        $review->setRating($request->get('rating'));

        // Optional fields - set them if provided in the JSON payload
        $review->setLikeCount($request->get('like_count') ?? 0);
        $review->setDislikeCount($request->get('dislike_count') ?? 0);

        // If you have set the options={"default"="CURRENT_TIMESTAMP"} in the entity for date_posted,
        // the date will be automatically set when persisting the review.

        // Optional: If you want to associate the review with an album and user, you can do the following:
        // Assuming the JSON payload includes album_id and user_id properties for the respective entities.
        // $album = $entityManager->getRepository(Album::class)->find($data['album_id']);
        // $user = $entityManager->getRepository(User::class)->find($data['user_id']);
        // $review->setAlbum($album);
        // $review->setUserId($user);

        // Persist the new review to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($review);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Review created successfully.'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="app_review_show", methods={"GET"})
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }
    /**
     * @Route("/api/reviews/{id}", name="api_review_show", methods={"GET"})
     */
    public function apiShow(Review $review): JsonResponse
    {
        // Create an associative array with the review's properties
        $data = [
            'id' => $review->getId(),
            'review_text' => $review->getReviewText(),
            'rating' => $review->getRating(),
            'date_posted' => $review->getDatePosted()->format('Y-m-d H:i:s'),
            'like_count' => $review->getLikeCount(),
            'dislike_count' => $review->getDislikeCount(),
            // If you want to include the album and user details, you can add them as well
            // 'album' => [
            //     'id' => $review->getAlbum()->getId(),
            //     'title' => $review->getAlbum()->getTitle(),
            // ],
            // 'user' => [
            //     'id' => $review->getUserId()->getId(),
            //     'username' => $review->getUserId()->getUsername(),
            // ],
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/edit", name="app_review_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review);
            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/reviews/{id}/edit", name="api_review_edit", methods={"PUT"})
     */
    public function apiEdit(Request $request, Review $review, ReviewRepository $reviewRepository): JsonResponse
    {


        // Check if the required data (e.g., "review_text", "rating") is provided in the JSON payload
        if ($request->get('review_text') !== null && !empty($request->get('review_text'))) {
            $review->setReviewText($request->get('review_text'));
        }
        if ($request->get('rating') !== null && !empty($request->get('rating'))) {
            $review->setRating($request->get('rating'));
        }
        if ($request->get('like_count') !== null && !empty($request->get('like_count'))) {
            $review->setLikeCount($request->get('like_count'));
        }
        if ($request->get('dislike_count') !== null && !empty($request->get('dislike_count'))) {
            $review->setDislikeCount($request->get('dislike_count'));
        }

        // Update the Review entity with the new data

        // Update other properties for the review if needed.

        // Persist the updated review to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Review updated successfully.'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="app_review_delete", methods={"POST"})
     */
    public function delete(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review);
        }

        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/api/reviews/{id}", name="api_review_delete", methods={"DELETE"})
     */
    public function apiDelete(Request $request, Review $review, ReviewRepository $reviewRepository): JsonResponse
    {


        // Perform the deletion of the review
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($review);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Review deleted successfully.'], JsonResponse::HTTP_OK);
    }
}
