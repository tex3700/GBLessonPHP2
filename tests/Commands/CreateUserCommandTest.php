<?php

namespace GeekBrains\PHPUnit\Commands;

use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommandException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\DummyUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
	/**
	 * @throws ArgumentsException
	 * @throws InvalidArgumentException
	 */
	public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository());
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);

        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');

        // Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует имя пользователя

	/**
	 * @throws CommandException
	 * @throws InvalidArgumentException
	 */
	public function testItRequiresFirstName(): void
    {
// $usersRepository - это объект анонимного класса,
// реализующего контракт UsersRepositoryInterface
        $usersRepository = new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
// Ничего не делаем
            }

            public function get(UUID $uuid): User
            {
// И здесь ничего не делаем
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {

                // И здесь ничего не делаем
                throw new UserNotFoundException("Not found");
            }

			public function delete(UUID $uuid): void
			{
				// TODO: Implement delete() method.
			}
		};
// Передаём объект анонимного класса
// в качестве реализации UsersRepositoryInterface
        $command = new CreateUserCommand($usersRepository);
// Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
// Запускаем команду
        $command->handle(new Arguments(['username' => 'Ivan']));
    }


    // Тест проверяет, что команда действительно требует фамилию пользователя

	/**
	 * @throws CommandException
	 * @throws InvalidArgumentException
	 */
	public function testItRequiresLastName(): void
    {
// Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
// Нам нужно передать имя пользователя,
// чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
    }


    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User

            {
                throw new UserNotFoundException("Not found");
            }

			public function delete(UUID $uuid): void
			{
				// TODO: Implement delete() method.
			}
		};
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории

	/**
	 * @throws ArgumentsException
	 * @throws CommandException
	 * @throws InvalidArgumentException
	 */
	public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
// Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {

                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }

			public function delete(UUID $uuid): void
			{
				// TODO: Implement delete() method.
			}
		};

        $command = new CreateUserCommand($usersRepository);

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

}