<?php

namespace App\Type;

enum UserGroupID: string
{
    case Admin = 'admin';
    case User = 'user';
}