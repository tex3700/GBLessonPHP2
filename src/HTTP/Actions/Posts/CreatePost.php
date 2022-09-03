<?php

namespace GeekBrains\LevelTwo\HTTP\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\HTTP\Actions\ActionInterface;
use GeekBrains\LevelTwo\HTTP\ErrorResponse;
use GeekBrains\LevelTwo\HTTP\Request;
use GeekBrains\LevelTwo\HTTP\Response;
use GeekBrains\LevelTwo\HTTP\SuccessfulResponse;
use GeekBrains\LevelTwo\Person\Name;

class CreatePost implements ActionInterface
{
	public function __construct(
		private UsersRepositoryInterface $usersRepository,
		private PostsRepositoryInterface $postsRepository
	) {
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function handle(Request $request): Response
	{
		try {
			$authorUuid = new UUID ($request->jsonBodyField('author_uuid'));
		} catch (HttpException | InvalidArgumentException $exception) {
			return new ErrorResponse($exception->getMessage());
		}

		try {
			$user = $this->usersRepository->get($authorUuid);
		} catch (UserNotFoundException $exception) {
			return new ErrorResponse($exception->getMessage());
		}

		$newPostUuid = UUID::random();

		try {
			$post = new Post(
				$newPostUuid,
				$user,
				$request->jsonBodyField('post_title'),
				$request->jsonBodyField('post_text')
			);
		} catch (HttpException $exception) {
			return new ErrorResponse($exception->getMessage());
		}

		$this->postsRepository->save($post);

		return new SuccessfulResponse([
			'uuid' => (string)$newPostUuid
		]);
	}
}