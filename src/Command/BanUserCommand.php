<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ban-user',
    description: 'Ban or unban a user',
)]
class BanUserCommand extends Command
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('days', InputArgument::OPTIONAL, 'Number of days to ban (omit to unban)', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $days = $input->getArgument('days');

        $user = $this->users->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('User "%s" not found.', $email));
            return Command::FAILURE;
        }

        if ($days === null) {
            $user->setBannedUntil(null);
            $this->em->flush();
            $io->success(sprintf('User "%s" has been unbanned.', $email));
        } else {
            $bannedUntil = new \DateTime(sprintf('+%d days', (int) $days));
            $user->setBannedUntil($bannedUntil);
            $this->em->flush();
            $io->success(sprintf('User "%s" has been banned until %s.', $email, $bannedUntil->format('Y-m-d H:i')));
        }

        return Command::SUCCESS;
    }
}