<?php

use GeekBrains\LevelTwo\Person\{Name, Person};
use GeekBrains\LevelTwo\Blog\{Post, User, Comment};
use GeekBrains\LevelTwo\Blog\Repositories\InMemoryUsersRepository;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;

include __DIR__ . "/vendor/autoload.php";

$faker = Faker\Factory::create('ru_RU');

$name = new Name($faker->firstName(), $faker->lastName());
$person = new Person($name, new DateTimeImmutable());
$post = new Post(rand(1, 100), $person, $faker->sentence(5), $faker->realText(rand(50, 200)));

if (strtolower($argv[1]) == "user") {

    echo $name;
}

if (strtolower($argv[1]) == "post") {

    echo $post;
}

if (strtolower($argv[1]) == "comment") {

    $comment = new Comment(rand(1, 100), $person, $post, $faker->realText(rand(50, 150)));
    echo $comment;
}
