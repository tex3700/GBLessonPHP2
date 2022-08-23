<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Person;

class Post
{
    private int $id;
    private Person $author;
    private string $postHeader;
    private string $text;

    public function __construct(
        int $id,
        Person $author,
        string $postHeader,
        string $text
    )
    {
        $this->id = $id;
        $this->postHeader = $postHeader;
        $this->text = $text;
        $this->author = $author;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . $this->postHeader . " >>> " . $this->text  . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


}