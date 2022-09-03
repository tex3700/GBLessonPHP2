<?php

use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\HTTP\Actions\Comments\CreateComment;
use GeekBrains\LevelTwo\HTTP\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\HTTP\Actions\Posts\{CreatePost, DeletePost};
use GeekBrains\LevelTwo\HTTP\Actions\Users\FindByUsername;
use GeekBrains\LevelTwo\HTTP\Request;
use GeekBrains\LevelTwo\HTTP\SuccessfulResponse;
use GeekBrains\LevelTwo\Blog\Exceptions\{HttpException, JsonException};
use GeekBrains\LevelTwo\HTTP\ErrorResponse;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'),);


$routes = [
	'GET' => [
		'/users/show' => new FindByUsername(
			new SqliteUsersRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			)
		),
	],
	'POST' => [
		'/users/create' => new CreateUser(
			new SqliteUsersRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			)
		),

		'/posts/create' => new CreatePost(
			new SqliteUsersRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			),
			new SqlitePostsRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			)
		),

		'/posts/comment' => new CreateComment(
			new SqliteUsersRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			),
			new SqlitePostsRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			),
			new SqliteCommentsRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			)
		),
	],

	'DELETE' => [
		'/posts' => new DeletePost (
			new SqlitePostsRepository(
				new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
			)
		),
	]

];


try {
	$path = $request->path();
} catch (HttpException) {
	(new ErrorResponse)->send();
	return;
}

try {
	$method = $request->method();
} catch (HttpException) {
	(new ErrorResponse)->send();
	return;
}

if (!array_key_exists($method, $routes)) {
	(new ErrorResponse('Not found'))->send();
	return;
}

if (!array_key_exists($path, $routes[$method])) {
	(new ErrorResponse('Not found'))->send();
	return;
}

$action = $routes[$method][$path];

try {
	$response = $action->handle($request);
	$response->send();
} catch (Exception $e) {
	(new ErrorResponse($e->getMessage()))->send();
}

