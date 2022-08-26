<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository;


use GeekBrains\LevelTwo\Blog\{Post, UUID};

interface PostsRepositoryInterface
{

    public function save(Post $post): void;
    public function get(UUID $uuid): Post;

}