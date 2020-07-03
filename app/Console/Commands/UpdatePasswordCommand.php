<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\ModelRepositories\UserRepository;
use App\Utils\PasswordGenerator;

class UpdatePasswordCommand extends Command
{
    protected $signature = 'update:password {email} {--password=}';

    protected $options = [
        'lowerCases' => false,
        'upperCases' => false,
        'numbers' => false,
        'symbols' => false,
        'excludeSimilar' => false,
        'lowerCaseLength' => 0,
        'upperCaseLength' => 0,
        'numberLength' => 0,
        'symbolLength' => 0,
    ];

    protected function go()
    {
        if (!$this->issetPassword()) {
            if ($this->confirm('Do you want to exclude similar characters (e.g. i, l, 1, L, o, 0, O ) ?', true)) {
                $this->options['excludeSimilar'] = true;
            }
            if ($this->confirm('Do you want password contain lower case characters?', true)) {
                $this->options['lowerCases'] = true;
                $this->options['lowerCaseLength'] = $this->ask('How many characters do you want?');
            }
            if ($this->confirm('Do you want password contain upper case characters?', true)) {
                $this->options['upperCases'] = true;
                $this->options['upperCaseLength'] = $this->ask('How many characters do you want?');
            }
            if ($this->confirm('Do you want password contain numbers?', true)) {
                $this->options['numbers'] = true;
                $this->options['numberLength'] = $this->ask('How many number do you want?');
            }
            if ($this->confirm('Do you want password contain symbols?', true)) {
                $this->options['symbols'] = true;
                $this->options['symbolLength'] = $this->ask('How many characters do you want?');
            }
            if ($this->validateHandledOptions()) {
                $this->updatePassword();
            }
        } else {
            $this->updatePassword();
        }

    }

    private function getEmail()
    {
        return $this->argument('email');
    }

    private function issetPassword()
    {
        return !empty($this->option('password')) ? true : false;
    }

    private function validateHandledOptions()
    {
        if (!is_int($this->options['lowerCaseLength']) || !is_int($this->options['upperCaseLength']) || !is_int($this->options['numberLength']) || !is_int($this->options['symbolLength'])) {
            $this->error('Number of characters must be numeric.');
            return false;
        }
        return true;
    }

    private function generatePassword()
    {
        return (new PasswordGenerator())
            ->excludeSimilarCharacter($this->options['excludeSimilar'])
            ->includeUpperCases($this->options['upperCases'])
            ->includeLowerCases($this->options['lowerCases'])
            ->includeNumbers($this->options['numbers'])
            ->includeSymbols($this->options['symbols'])
            ->setUpperCaseLength($this->options['upperCaseLength'])
            ->setLowerCaseLength($this->options['lowerCaseLength'])
            ->setNumberLength($this->options['numberLength'])
            ->setSymbolLength($this->options['symbolLength'])
            ->generate();
    }

    private function updatePassword()
    {
        $userRepository = new UserRepository();
        $user = $userRepository->getByEmail($this->getEmail(), false);
        if (empty($user)) {
            $this->error('Email does not exist!');
        } else {
            $password = $this->issetPassword() ? $this->option('password') : $this->generatePassword();
            $userRepository->model($user);
            $userRepository->updateWithAttributes([
                'password' => $password,
            ]);
            $this->warn(sprintf('%s was updated as password successfully!', $password));
        }
    }
}
