<?php

namespace App\Exception;

class AppException extends \Exception
{
    public static function userNotFound()
    {
        return new AppException('Пользователь не найден.');
    }

    public static function accessDenied()
    {
        throw new AppException('Доступ запрещен.');
    }

    public static function entityNotFound(string $message = 'Сущность не найдена.')
    {
        throw new AppException($message);
    }
}