<?php

namespace GeekBrains\LevelTwo\HTTP\Auth;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\HTTP\Request;
use DateTimeImmutable;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
	private const HEADER_PREFIX = 'Bearer ';

	public function __construct(
		private AuthTokensRepositoryInterface $authTokensRepository,
		private UsersRepositoryInterface $usersRepository
	) {
	}

	/**
	 * @throws AuthException
	 */
	public function user(Request $request): User
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

		if ($authToken->expiresOn() <= new DateTimeImmutable()) {
			throw new AuthException("Token expired: [$token]");
		}

		$userUuid = $authToken->userUuid();

		return $this->usersRepository->get($userUuid);

	}
}