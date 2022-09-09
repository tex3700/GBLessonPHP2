<?php

use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use GeekBrains\LevelTwo\Blog\Commands\{CreateUserCommand, Arguments};
use Psr\Log\LoggerInterface;

include __DIR__ . "/vendor/autoload.php";

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

$logger= $container->get(LoggerInterface::class);

try {
	$command->handle(Arguments::fromArgv($argv));
} catch (AppException $exception) {
	$logger->error($exception->getMessage(), ['exception' => $exception]);
}


