<?php

namespace App\Command\Admin;

use App\DataManager\UserManager;
use App\Type\CommandID;
use DI\Attribute\Inject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowUsersCommand extends Command
{
    protected static $defaultName = CommandID::Admin_ShowUsers->value;

    #[Inject] private UserManager $userManager;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userManager->find();
        foreach ($users as $user) {
            $output->writeln(implode(' | ',
                [
                    $user['id'],
                    $user['email'],
                    $user['api_key'],
                ]
            ));
        }

        return Command::SUCCESS;
    }
}