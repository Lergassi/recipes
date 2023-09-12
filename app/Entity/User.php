<?php

namespace App\Entity;

use App\Interface\SerializeInterface;
use App\Service\SerializeReader;
use Respect\Validation\Validator;

//class User implements SerializeInterface
class User implements \JsonSerializable
{
    private int $id;
    private string $email;
    private string $passwordHash;
    private array $userGroups;
    private ?string $apiKey;

    public function getID(): string
    {
        return $this->id;
    }

    //todo: Хотя почему данные из бд не надо проверять?
    public static function load(
        string    $id,
        string    $email,
        string    $passwordHash,
        array     $userGroups,
        ?string    $apiKey,
    )
    {
        $user = new User();

        $user->id = $id;
        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->userGroups = $userGroups;
        $user->apiKey = $apiKey;

        return $user;
    }

    public function verifyPassword(string $password)
    {
        Validator::notBlank()->assert($password);

        return password_verify($password, $this->passwordHash);
    }

    public function hasUserGroup(string $userGroupID): bool
    {
        return in_array($userGroupID, $this->userGroups);
    }

    //todo: Нужна проверка группы или объект.
    //todo: Или наоборот: пользователя добавлять в группу. И проверять не user->hasGroup(groupID), а group->hasUser(userID)
    public function addUserGroup(string $userGroupID): bool
    {
        if ($this->hasUserGroup($userGroupID)) return false;

        $this->userGroups[] = $userGroupID;

        return true;
    }

    //todo: Возможно нужно запретить удаление из группы player. Пользователя без групп быть не может, даже заблокированных.
    public function removeUserGroup(string $userGroupID): bool
    {
        if (!$this->hasUserGroup($userGroupID)) return false;

        $this->userGroups = array_filter($this->userGroups, function ($item) use ($userGroupID) {
            return $item !== $userGroupID;
        });

        return true;
    }

    //todo: Пока так для отработки сценария контроллер - %логика% - сервис.
    /*
     * Алгоритм по аналогии с addGroup().
     *  Действие с пользователем в подходящем месте. Сейчас это: контроллер -> ApiSecurity/Security.
     *  Если логин прошол - выполнить запрос в бд связанный с данным действием (сценарием). Сейчас void без исключений.
     *  Запросы в бд пока решено размещать в сервисах. Вне сервиса запросы могут быть только в контроллерах.
     */
    public function setApiKey(string $apiKey): void
    {
        Validator::notBlank()->assert($apiKey);

        $this->apiKey = $apiKey;
    }

    public function resetApiKey(): void
    {
        $this->apiKey = null;
    }

//    public function serialize(SerializeReader $reader): void
//    {
//        $reader->add('user_groups', $this->userGroups);
//    }

    // todo: Временное решение для настройки Quality/ReferenceProduct для ui.
    public function jsonSerialize(): mixed
    {
        return [
            'email' => $this->email,
            'user_groups' => $this->userGroups,
        ];
    }

    private function __construct() {}
}