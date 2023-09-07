<?php

namespace App\Service\DataService;

use App\DataManager\UserGroupManager;
use App\DataManager\UserManager;
use App\Entity\User;
use DI\Attribute\Inject;

class UserService
{
    #[Inject] private \PDO $pdo;
    #[Inject] private UserManager $userManager;
    #[Inject] private UserGroupManager $userGroupManager;

    //todo: Считается, что пользователь и группа уже есть в системе. Можно добавить настройку, которая будет включать/отключать проверку или сделать отдельным классом. Или отдельный метод init без проверок.
    public function addUserGroups(User $user, array $userGroupIDs): void
    {
        $insertUserGroupQuery = 'insert into users_has_groups (user_id, user_group_id) VALUES (:user_id, :user_group_id)';
        $insertUserGroupStmt = $this->pdo->prepare($insertUserGroupQuery);

        foreach ($userGroupIDs as $userGroupID) {
            if ($user->addUserGroup($userGroupID)) {
                $insertUserGroupStmt->bindValue(':user_id', $user->getID());
                $insertUserGroupStmt->bindValue(':user_group_id', $userGroupID);

                $insertUserGroupStmt->execute();
            }
        }
    }

    public function removeUserGroup(User $user, array $userGroupIDs): void
    {
        $deleteUserGroupQuery = 'delete from users_has_groups where user_id = :user_id and user_group_id = :user_group_id';
        $deleteUserGroupStmt = $this->pdo->prepare($deleteUserGroupQuery);

        foreach ($userGroupIDs as $userGroupID) {
            if ($user->removeUserGroup($userGroupID)) {
                $deleteUserGroupStmt->bindValue(':user_id', $user->getID());
                $deleteUserGroupStmt->bindValue(':user_group_id', $userGroupID);

                $deleteUserGroupStmt->execute();
            }
        }
    }

    public function setApiKey(User $user, ?string $apiKey): void
    {
        $user->setApiKey($apiKey);

        $query = 'update users set api_key = :api_key where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $user->getID());
        $stmt->bindValue(':api_key', $apiKey);

        $stmt->execute();
    }
}