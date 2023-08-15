<?php

namespace Tests\Unit\Services;

use App\Models\Databases\Repositories\UserRepository;
use App\Models\Entities\UserEntity;
use App\Services\UserService;
use Tests\Unit\CustomTestCase;

final class UserServiceTest extends CustomTestCase
{
    public function testRegisterUser()
    {
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('existsNameOrPassword')->willReturn(false);
        $userId = "1";
        $userRepositoryMock->method('insert')->willReturn($userId);

        $userService = new UserService($userRepositoryMock);

        $name = 'testUser';
        $email = 'test@example.com';
        $plainPassword = 'Password_000';

        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        $userRepositoryMock->method('fetchUserById')->willReturn(new UserEntity($userId, $name, $email, $hashedPassword));
        // ユーザーが存在していた場合呼ばれていないことを確認
        $userRepositoryMock->expects($this->once())
                        ->method('insert');

        $actualUser = $userService->registerUser($name, $email, $plainPassword);

        $this->assertEquals($userId, $actualUser->getId());
        $this->assertEquals($name, $actualUser->getUserName());
        $this->assertTrue(password_verify($plainPassword, $hashedPassword));
    }

    public function testExistsUserCaseRegisterUser()
    {
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('existsNameOrPassword')->willReturn(true);
        $userRepositoryMock->method('insert')->willReturn('1');

        $userService = new UserService($userRepositoryMock);

        $name = 'testUser';
        $email = 'test@example.com';
        $plainPassword = 'Password_000';

        // ユーザーが存在していた場合呼ばれていないことを確認
        $userRepositoryMock->expects($this->never())
                        ->method('insert');

        $condition = $userService->registerUser($name, $email, $plainPassword);
        $this->assertFalse($condition);
    }

    public function testFailedRegisterUser()
    {
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('existsNameOrPassword')->willReturn(false);
        $userRepositoryMock->method('insert')->willReturn(false);

        $userService = new UserService($userRepositoryMock);

        $name = 'testUser';
        $email = 'test@example.com';
        $plainPassword = 'Password_000';

        $this->expectException(\PDOException::class);
        $actual = $userService->registerUser($name, $email, $plainPassword);
    }
}
