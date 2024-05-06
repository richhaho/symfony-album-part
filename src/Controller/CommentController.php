<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="app_comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/api/comments", name="api_comment_index", methods={"GET"})
     */
    public function apiIndex(CommentRepository $commentRepository): JsonResponse
    {
        $comments = $commentRepository->findAll();

        $data = [];
        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'comment_text' => $comment->getCommentText(),
                'date_posted' => $comment->getDatePosted()->format('Y-m-d H:i:s'),
                'like_count' => $comment->getLikeCount(),
                'dislike_count' => $comment->getDislikeCount(),
                // If you want to include the review and user details, you can add them as well
                // 'review' => [
                //     'id' => $comment->getReview()->getId(),
                //     'review_text' => $comment->getReview()->getReviewText(),
                // ],
                // 'user' => [
                //     'id' => $comment->getUserId()->getId(),
                //     'username' => $comment->getUserId()->getUsername(),
                // ],
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="app_comment_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->add($comment);
            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/comments/new", name="api_comment_new", methods={"POST"})
     */
    public function apiNew(Request $request, CommentRepository $commentRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if the required data (e.g., "comment_text") is provided in the JSON payload
        if (empty($request->get('comment_text'))) {
            return new JsonResponse(['error' => 'Comment text is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Create a new Comment entity and set its properties based on the data received
        $comment = new Comment();
        $comment->setCommentText($request->get('comment_text'));
        $comment->setLikeCount($request->get('like_count') ?? 0);
        $comment->setDislikeCount($request->get('dislike_count') ?? 0);

        // Assuming the JSON payload contains a valid DateTime string for "date_posted" property
        $datePosted = new \DateTime($request->get('date_posted') ?? 'now');
        $comment->setDatePosted($datePosted);

        // If you want to associate the comment with a review and user, you can do the following:
        // Assuming the JSON payload includes review_id and user_id properties for the respective entities.
        // $review = $entityManager->getRepository(Review::class)->find($data['review_id']);
        // $user = $entityManager->getRepository(User::class)->find($data['user_id']);
        // $comment->setReview($review);
        // $comment->setUserId($user);

        // Persist the new comment to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Comment created successfully.'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="app_comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_comment_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->add($comment);
            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/comments/{id}/edit", name="api_comment_edit", methods={"PUT"})
     */
    public function apiEdit(Request $request, Comment $comment, CommentRepository $commentRepository): JsonResponse
    {


        // Check if the required data (e.g., "comment_text") is provided in the JSON payload


        if ($request->get('comment_text') !== null && !empty($request->get('comment_text'))) {
            $comment->setCommentText($request->get('comment_text'));
        }
        if ($request->get('like_count') !== null && !empty($request->get('like_count'))) {
            $comment->setLikeCount($request->get('like_count'));
        }
        if ($request->get('dislike_count') !== null && !empty($request->get('dislike_count'))) {
            $comment->setDislikeCount($request->get('dislike_count'));
        }

        // Update the Comment entity with the new data


        // If you want to associate the comment with a review and user, you can do the following:
        // Assuming the JSON payload includes review_id and user_id properties for the respective entities.
        // $review = $entityManager->getRepository(Review::class)->find($data['review_id']);
        // $user = $entityManager->getRepository(User::class)->find($data['user_id']);
        // $comment->setReview($review);
        // $comment->setUserId($user);

        // Persist the updated comment to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Comment updated successfully.'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="app_comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment);
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/api/comments/{id}", name="api_comment_delete", methods={"DELETE"})
     */
    public function apiDelete(Request $request, Comment $comment, CommentRepository $commentRepository): JsonResponse
    {
       

        // Remove the comment from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Comment deleted successfully.'], JsonResponse::HTTP_OK);
    }
}
