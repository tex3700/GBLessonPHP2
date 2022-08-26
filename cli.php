<?php

use GeekBrains\LevelTwo\Blog\{Comment, Post, User, UUID};
use GeekBrains\LevelTwo\Person\{Name, Person};
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\LevelTwo\Blog\Exceptions\{AppException,
    InvalidArgumentException, PostNotFoundException, UserNotFoundException};

include __DIR__ . "/vendor/autoload.php";
include "sqlite.php";
//$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$faker = Faker\Factory::create('ru_RU');

$user1 = new User(UUID::random(), new Name($faker->firstName(), $faker->lastName()), $faker->userName());
$post1 = new Post(UUID::random(), $user1, $faker->sentence(5), $faker->realText(rand(50, 200)));

$usersRepository = new SqliteUsersRepository($connection);
$usersRepository->save($user1);

$postsRepository = new SqlitePostsRepository($connection);
$postsRepository->save($post1);

$commentRepository = new SqliteCommentsRepository($connection);
$commentRepository->save(new Comment(UUID::random(), $user1, $post1, $faker->realText(rand(50, 200))));
