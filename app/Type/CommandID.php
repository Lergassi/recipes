<?php

namespace App\Type;

enum CommandID: string
{
    case Admin_AddUserGroup = 'admin.add_user_group';
    case Admin_RemoveUserGroup = 'admin.remove_user_group';
}
