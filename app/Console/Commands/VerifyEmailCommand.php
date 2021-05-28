<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Console\Commands\Base\UserCommandTrait;
use App\ModelRepositories\Base\IUserVerifyEmailRepository;

class VerifyEmailCommand extends Command
{
    use UserCommandTrait;

    protected $signature = 'verify:email {--code=} {--user=} {--unverified} {--no-fresh}';

    protected function go()
    {
        $this->getUserRepository();
        if ($this->option('unverified')) {
            $this->goUnverifying();
        }
        else {
            $this->goVerifying();
        }
    }

    protected function goVerifying()
    {
        if ($this->userRepository instanceof IUserVerifyEmailRepository) {
            if ($code = $this->option('code')) {
                if (is_null($this->userRepository->skipProtected()->verifyEmailByCode($code))) {
                    $this->error(
                        sprintf(
                            'Cannot verify the user having code [%s]',
                            $code
                        )
                    );
                }
                else {
                    $this->info(sprintf('User having code [%s] has been verified.', $code));
                }
                return;
            }

            if ($this->parseUser('user', false)) {
                if (is_null($this->userRepository->skipProtected()->verifyEmail())) {
                    $this->error(
                        sprintf(
                            'Cannot verify the user [%s]',
                            $this->userRepository->getId()
                        )
                    );
                }
                else {
                    $this->info(sprintf('User [%s] has been verified.', $this->userRepository->getId()));
                }
                return;
            }
        }
        $this->warn('No verification.');
    }

    protected function goUnverifying()
    {
        if ($this->userRepository instanceof IUserVerifyEmailRepository) {
            if ($code = $this->option('code')) {
                if (is_null($this->userRepository->skipProtected()->unverifyEmailByCode($code, !$this->option('no-fresh')))) {
                    $this->error(
                        sprintf(
                            'Cannot unverify the user having code [%s]',
                            $code
                        )
                    );
                }
                else {
                    $this->info(sprintf('User having code [%s] has been unverified.', $code));
                }
                return;
            }

            if ($this->parseUser('user', false)) {
                if (is_null($this->userRepository->skipProtected()->unverifyEmail(!$this->option('no-fresh')))) {
                    $this->error(
                        sprintf(
                            'Cannot unverify the user [%s]',
                            $this->userRepository->getId()
                        )
                    );
                }
                else {
                    $this->info(sprintf('User [%s] has been unverified.', $this->userRepository->getId()));
                }
                return;
            }
        }
        $this->warn('No unverification.');
    }
}