<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Person\Person;

class Comment
{
    private int $id;
    private Person $author;
    private Post $recensionPost;
    private string $text;

    public function __construct(
        int    $id,
        Person $author,
        Post   $recensionPost,
        string $text
    )
    {
        $this->id = $id;
        $this->author = $author;
        $this->recensionPost = $recensionPost;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->author . ' пишет к статье: ' . PHP_EOL .'"'. $this->recensionPost->getText() .'"'. PHP_EOL . " комментарий >>> " . $this->text  . PHP_EOL;
    }
}