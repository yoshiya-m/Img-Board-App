<?php

namespace Models;

class Post
{
    private ?int $postId;
    private ?int $replyToId;
    private ?string $subject;
    private string $content;
    private ?string $createdAt;
    private ?string $updatedAt;


    public function __construct(
        ?int $postId,
        ?int $replyToId,
        ?string $subject,
        string $content,
        ?string $createdAt,
        ?string $updatedAt
    ) {
        $this->postId = $postId;
        $this->replyToId = $replyToId;
        $this->subject = $subject;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
    public function getPostId(): ?int 
    {
        return $this->postId;
    }
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }
    public function getReplyToId(): ?int 
    {
        return $this->replyToId;
    }
    public function setReplyToId(?int $replyToId): void
    {
        $this->replyToId = $replyToId;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setText(string $content): void
    {
        $this->content = $content;
    }
    
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
