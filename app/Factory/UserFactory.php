<?php

namespace App\Factory;

use App\DataManager\UserManager;
use App\DataService\UserService;
use App\Entity\User;
use DI\Attribute\Inject;
use PDO;
use Respect\Validation\Validator;

//todo: Нужно делать тесты.
class UserFactory
{
    #[Inject] private PDO $pdo;
    #[Inject] private UserManager $userManager;
    #[Inject] private UserService $userService;

    public function create(
        string $email,
        string $password,
        array $userGroupIDs = [],
    ): User
    {
        Validator::email()->assert($email);
        Validator::falseVal()->assert($this->userManager->hasByEmail($email));
        Validator::length(8)->assert($password);

        $insertUserQuery = 'insert into users (email, password_hash, api_key) VALUES (:email, :password_hash, :api_key)';
        $insertUserStmt = $this->pdo->prepare($insertUserQuery);

        $insertUserStmt->bindValue(':email', $email);
        $insertUserStmt->bindValue(':password_hash', password_hash($password, PASSWORD_DEFAULT));
        $insertUserStmt->bindValue(':api_key', null);

        $insertUserStmt->execute();

        $userID = $this->pdo->lastInsertId();

        //todo: build
        $user = $this->userManager->findOneEntity($userID);

        $this->userService->addUserGroups($user, $userGroupIDs);

        return $user;
    }
}