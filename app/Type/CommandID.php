<?php

namespace App\Type;

enum CommandID: string
{
    case Admin_ShowUsers = 'admin.users';
    case Admin_AddUserGroup = 'admin.user.add_group';
    case Admin_RemoveUserGroup = 'admin.user.remove_group';
}
