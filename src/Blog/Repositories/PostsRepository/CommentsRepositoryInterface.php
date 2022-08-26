<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\{Comment, UUID};

interface CommentsRepositoryInterface
{

    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;

}