<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class UserGroupManager
{
    #[Inject] private \PDO $pdo;

    public function findByUser(string $userID): array
    {
        $groupsQuery =
            'select
                ug.*
            from users u
                left join users_has_groups uh_g on u.id = uh_g.user_id
                left join user_groups ug on uh_g.user_group_id = ug.id
            where
                uh_g.user_id = :uh_g_user_id';
        $groupsStmt = $this->pdo->prepare($groupsQuery);

        $groupsStmt->bindValue(':uh_g_user_id', $userID);

        $groupsStmt->execute();

        return $groupsStmt->fetchAll();
    }

    public function hasGroup(string $userID, string $userGroupID): bool
    {
        $existsUserGroupQuery =
            'select
                count(*) as count
            from users_has_groups
            where
                user_id = :user_id
                and user_group_id = :user_group_id'
        ;
        $stmt = $this->pdo->prepare($existsUserGroupQuery);

        $stmt->bindValue(':user_id', $userID);
        $stmt->bindValue(':user_group_id', $userGroupID);

        $stmt->execute();

        return $stmt->fetch()['count'] === 1;
    }
}