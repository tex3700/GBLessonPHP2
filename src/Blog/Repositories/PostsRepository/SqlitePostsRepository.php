<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\{Post, Repositories\UsersRepository\SqliteUsersRepository, User, UUID};
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Exceptions\{InvalidArgumentException, UserNotFoundException, PostNotFoundException};
use \PDO;
use \PDOStatement;

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
VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->getAuthor()->uuid(),
            ':title' => $post->getPostHeader(),
            ':text' => $post->getText()
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
    private function getPost(PDOStatement          $statement,
                             string                $errString,
                             SqliteUsersRepository $usersRepository): Post
    {

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $errString"
            );
        }

        $user = $usersRepository->get($result['author_uuid']);

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }



}