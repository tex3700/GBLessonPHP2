<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\{InvalidArgumentException, UserNotFoundException};
use GeekBrains\LevelTwo\Blog\{User, UUID};
use GeekBrains\LevelTwo\Person\Name;
use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
		private PDO $connection,
		private LoggerInterface $logger
	) {
    }


    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
        VALUES (:uuid, :username, :first_name, :last_name)'
        );

        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last()
        ]);

		$this->logger->info("User ({$user->uuid()}) was saved to database");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );

        $statement->execute([(string) $uuid]);

        return $this->getUser($statement, $uuid);

    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(PDOStatement $statement, string $errString): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
			$this->logger->warning("User ($errString) not found");
            throw new UserNotFoundException(
                "Cannot find user: $errString"
            );
        }

        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }


	public function delete(UUID $uuid): void
	{
		$statement = $this->connection->prepare(
			'DELETE FROM users WHERE uuid = :uuid'
		);

		$statement->execute([':uuid' => $uuid]);
	}
}