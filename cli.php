<?php

use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use GeekBrains\LevelTwo\Blog\Commands\{CreateUserCommand, Arguments};

include __DIR__ . "/vendor/autoload.php";

$container = require __DIR__ . 'bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {
	$command->handle(Arguments::fromArgv($argv));
} catch (AppException $exception) {
	echo "{$exception->getMessage()}\n";
}


