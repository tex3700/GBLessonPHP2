<?php

namespace GeekBrains\LevelTwo\Blog;


class Comment
{
    private UUID $uuid;
    private User $author;
    private Post $recensionPost;
    private string $text;

    public function __construct(
        UUID    $uuid,
        User $author,
        Post   $recensionPost,
        string $text
    )
    {
        $this->uuid = $uuid;
        $this->author = $author;
        $this->recensionPost = $recensionPost;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->author . ' пишет к статье: ' . PHP_EOL .'"'. $this->recensionPost->getText() .'"'. PHP_EOL . " комментарий >>> " . $this->text  . PHP_EOL;
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setId(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Post
     */
    public function getRecensionPost(): Post
    {
        return $this->recensionPost;
    }

    /**
     * @param Post $recensionPost
     */
    public function setRecensionPost(Post $recensionPost): void
    {
        $this->recensionPost = $recensionPost;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }



}