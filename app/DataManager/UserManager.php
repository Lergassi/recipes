<?php

namespace App\DataManager;

use App\Entity\User;
use DI\Attribute\Inject;
use Respect\Validation\Validator;

class UserManager
{
    #[Inject] private \PDO $pdo;
    #[Inject] private UserGroupManager $userGroupManager;

    public function findOne(string $ID): ?array
    {
        $query = 'select * from users where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    //todo: Заменить на универсальный метод exists.
    public function hasByEmail(string $email): bool
    {
        $query = 'select count(*) as count from users where email = :email';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':email', $email);

        $stmt->execute();

        $result = $stmt->fetch();

        return $result['count'] === 1;
    }

    public function findOneByEmail(string $email): ?array
    {
        $query = 'select * from users where email = :email';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':email', $email);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findOneEntity(string $ID): ?User
    {
        $query = 'select * from users where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        $userData = $stmt->fetch();
        if (!$userData) return null;

//        $userGroupsData = $this->userGroupManager->findByUser($userData['id']);

        return $this->build($userData);
    }

    //todo: Можно разделить - DataManager и EntityManager.
    public function findOneByEmailEntity(string $email): ?User
    {
        $query = 'select * from users where email = :email';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':email', $email);

        $stmt->execute();

        $userData = $stmt->fetch();
        if (!$userData) return null;

        //todo: Для findAll() найти решение только с двумя запросами к user_groups. А метод переименовать в build.
//        $userGroupsData = $this->userGroupManager->findByUser($userData['id']); //todo: Конструктор. Но пользователь может работать по сценарию полной сборки всех данных для удобства. Но собираться лучше в другом месте.

        return $this->build($userData);
    }

    public function find(): array
    {
        $query = 'select u.* from users u';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll();
        foreach ($result as &$item) {
            $item['user_groups'] = $this->userGroupManager->findByUser($item['id']);
        }

        return $result;
    }

    public function findOneEntityByApiKey(string $apiKey): ?User
    {
        Validator::notBlank()->assert($apiKey);

        $query = 'select u.* from users u where u.api_key = :api_key';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':api_key', $apiKey);

        $stmt->execute();

        $userData = $stmt->fetch();
        if (!$userData) return null;

        return $this->build($userData);
    }

    private function build(array $data)
    {
        $userGroupsData = $this->userGroupManager->findByUser($data['id']);
        $userGroups = [];
        foreach ($userGroupsData as $userGroupsDatum) {
            $userGroups[] = $userGroupsDatum['id'];
        }

        return User::load(
            $data['id'],
            $data['email'],
            $data['password_hash'],
            $userGroups,
            $data['api_key'],
        );
    }
}