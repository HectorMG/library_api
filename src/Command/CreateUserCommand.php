<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    public function __construct(private UserPasswordEncoderInterface $encoder, private UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');


        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Select a role',
            ['ROLE_ADMIN', 'ROLE_MIEMBRO'],
            0
        );

        $role = $helper->ask($input, $output, $question);

        $user = new User();
        $user->setEmail($username);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setRoles([$role]);

        $this->userRepository->save($user);

        $output->writeln(sprintf('Created user with email: <comment>%s</comment>', $username));
        
        return Command::SUCCESS;
    }
}
