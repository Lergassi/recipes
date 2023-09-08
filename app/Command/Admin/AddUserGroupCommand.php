<?php

namespace App\Command\Admin;

use App\DataManager\UserGroupManager;
use App\DataManager\UserManager;
use App\Service\DataService\UserService;
use App\Type\CommandID;
use App\Type\UserGroupID;
use DI\Attribute\Inject;
use Respect\Validation\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUserGroupCommand extends Command
{
    protected static $defaultName = CommandID::Admin_AddUserGroup->value;

    #[Inject] private UserManager $userManager;
    #[Inject] private UserService $userService;

    protected function configure()
    {
        $this->addArgument('user_id', InputArgument::REQUIRED);
        $this->addArgument('user_group_id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userID = strval($input->getArgument('user_id'));
        $userGroupID = strval($input->getArgument('user_group_id'));

        $user = $this->userManager->findOneEntity($userID);
        Validator::notBlank()->assert($user);

        $this->userService->addUserGroups($user, [$userGroupID]);

        return Command::SUCCESS;
    }
}