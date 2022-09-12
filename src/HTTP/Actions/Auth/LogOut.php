<?php

namespace GeekBrains\LevelTwo\HTTP\Actions\Auth;

use GeekBrains\LevelTwo\Blog\AuthToken;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\HTTP\Actions\ActionInterface;
use GeekBrains\LevelTwo\HTTP\Auth\TokenAuthenticationInterface;
use GeekBrains\LevelTwo\HTTP\ErrorResponse;
use GeekBrains\LevelTwo\HTTP\Request;
use GeekBrains\LevelTwo\HTTP\Response;
use GeekBrains\LevelTwo\HTTP\SuccessfulResponse;

class LogOut implements ActionInterface
{
	private const HEADER_PREFIX = 'Bearer ';

	public function __construct(
		private AuthTokensRepositoryInterface $authTokensRepository
	) {
	}

	/**
	 * @throws AuthException
	 */
	public function handle(Request $request): Response
	{
		try {
			$header = $request->header('Authorization');
		} catch (HttpException $exception) {
			throw new AuthException($exception->getMessage());
		}

		if (!str_starts_with($header, self::HEADER_PREFIX)) {
			throw new AuthException("Malformed token: [$header]");
		}

		$token = mb_substr($header, strlen(self::HEADER_PREFIX));

		try {
			$authToken = $this->authTokensRepository->get($token);
		} catch (AuthTokenNotFoundException $exception) {
			throw new AuthException($exception->getMessage());
		}

		$outToken = new AuthToken(
			$token,
			$authToken->userUuid(),
			new \DateTimeImmutable("now")
		);

		$this->authTokensRepository->save($outToken);

		return new SuccessfulResponse([
			'token' => (string)$authToken->token()
		]);



	}
}