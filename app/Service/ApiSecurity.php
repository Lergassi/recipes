<?php

namespace App\Service;

use App\Entity\User;

//todo: Вариант общего класса конструктора для класса безопасности. Сессиии и куки и всё остельное отдельно.
class ApiSecurity
{
    private ?User $user = null;

    public function login(User $user): void
    {
        $this->user = $user;
    }

    public function logout(): void
    {
        $this->user = null;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function isAuth(): bool
    {
        return boolval($this->user);
    }
}