<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\{Post, Repositories\UsersRepository\SqliteUsersRepository, UUID};
use GeekBrains\LevelTwo\Blog\Exceptions\{InvalidArgumentException, PostNotFoundException, UserNotFoundException};
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface
{

    private PDO $connection;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, post_title, post_text) 
VALUES (:uuid, :author_uuid, :post_title, :post_text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->getAuthor()->uuid(),
            ':post_title' => $post->getPostHeader(),
            ':post_text' => $post->getText()
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = ?'
        );
        $statement->execute([(string)$uuid]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(PDOStatement $statement, string $errString,): Post
    {

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $errString"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['post_title'],
            $result['post_text']
        );
    }


	public function delete(UUID $uuid): void
	{
		$statement = $this->connection->prepare(
			'DELETE FROM posts WHERE uuid = :uuid'
		);

		$statement->execute([':uuid' => $uuid]);
	}
}