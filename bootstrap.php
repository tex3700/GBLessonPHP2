<?php

use GeekBrains\LevelTwo\Blog\Container\DIContainer;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\CommentLikesRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\SqliteCommentLikesRepository;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
	PDO::class,
	new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
	UsersRepositoryInterface::class,
	SqliteUsersRepository::class
);

$container->bind(
	PostsRepositoryInterface::class,
	SqlitePostsRepository::class
);

$container->bind(
	LikesRepositoryInterface::class,
	SqliteLikesRepository::class
);

$container->bind(
	CommentsRepositoryInterface::class,
	SqliteCommentsRepository::class
);

$container->bind(
	CommentLikesRepositoryInterface::class,
	SqliteCommentLikesRepository::class
);

return $container;