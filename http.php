<?php

use GeekBrains\LevelTwo\HTTP\Actions\Comments\CreateComment;
use GeekBrains\LevelTwo\HTTP\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\HTTP\Actions\Likes\{CreateLikes, CreateCommentLikes};
use GeekBrains\LevelTwo\HTTP\Actions\Posts\{CreatePost, DeletePost};
use GeekBrains\LevelTwo\HTTP\Actions\Users\FindByUsername;
use GeekBrains\LevelTwo\HTTP\Request;
use GeekBrains\LevelTwo\Blog\Exceptions\{HttpException, JsonException};
use GeekBrains\LevelTwo\HTTP\ErrorResponse;

require_once __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
	$_GET,
	$_SERVER,
	file_get_contents('php://input'),
);

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

$routes = [
	'GET' => [
		'/users/show' => FindByUsername::class,
	],
	'POST' => [
		'/users/create' => CreateUser::class,

		'/posts/create' => CreatePost::class,

		'/posts/comment' => CreateComment::class,

		'/posts/likes' => CreateLikes::class,

		'/comments/likes' => CreateCommentLikes::class,
	],

	'DELETE' => [
		'/posts' => DeletePost::class,
	]

];

if (!array_key_exists($method, $routes)) {
	(new ErrorResponse('Not found'))->send();
	return;
}

if (!array_key_exists($path, $routes[$method])) {
	(new ErrorResponse('Not found'))->send();
	return;
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
	$response = $action->handle($request);
	$response->send();
} catch (Exception $e) {
	(new ErrorResponse($e->getMessage()))->send();
}

//$response->send();

