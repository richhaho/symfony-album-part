<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment_text;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date_posted;

    /**
     * @ORM\Column(type="integer", nullable=true,options={"default"=0})
     */
    private $like_count=0;

    /**
     * @ORM\Column(type="integer", nullable=true,options={"default"=0})
     */
    private $dislike_count=0;

    /**
     * @ORM\ManyToOne(targetEntity=Review::class, inversedBy="comments")
     */
    private $review;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    private $user_id;
    public function __toString(): string
    {
        return $this->comment_text; // Return the title of the album as the string representation
    }

    public function __construct()
    {
        $this->date_posted = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentText(): ?string
    {
        return $this->comment_text;
    }

    public function setCommentText(?string $comment_text): self
    {
        $this->comment_text = $comment_text;

        return $this;
    }

    public function getDatePosted(): ?\DateTimeInterface
    {
        return $this->date_posted;
    }

    public function setDatePosted(?\DateTimeInterface $date_posted): self
    {
        $this->date_posted = $date_posted;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->like_count;
    }

    public function setLikeCount(?int $like_count): self
    {
        $this->like_count = $like_count;

        return $this;
    }

    public function getDislikeCount(): ?int
    {
        return $this->dislike_count;
    }

    public function setDislikeCount(?int $dislike_count): self
    {
        $this->dislike_count = $dislike_count;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
