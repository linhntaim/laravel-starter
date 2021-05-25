<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Console\Commands\Base\UserCommandTrait;
use App\Utils\PasswordGenerator;

class UpdatePasswordCommand extends Command
{
    use UserCommandTrait;

    protected $signature = 'update:password {user} {--password=}';

    protected $password;

    protected function go()
    {
        if ($this->parseUser() && $this->parsePassword()) {
            $this->updatePassword();
        }
    }

    protected function updatePassword()
    {
        $this->userRepository->skipProtected()->updatePassword($this->password);
        $this->warn(
            sprintf(
                '[%s] was updated as password for User ID [%s] successfully!',
                $this->password,
                $this->userRepository->getId(),
            )
        );
    }

    protected function parsePassword()
    {
        $this->password = $this->option('password');

        if (empty($this->password)) {
            $similarCharactersExcluded = $this->confirm('Do you want to exclude similar characters (e.g. i, I, l, L, 1, o, O, 0) ?', false);
            $lowerCasesIncluded = $this->confirm('Do you want password contain lowercase characters?', true);
            $lowerCasesLength = $lowerCasesIncluded ? intval($this->ask('How many characters do you want?', 3)) : 0;
            $upperCasesIncluded = $this->confirm('Do you want password contain uppercase characters?', true);
            $upperCasesLength = $upperCasesIncluded ? intval($this->ask('How many characters do you want?', 3)) : 0;
            $numbersIncluded = $this->confirm('Do you want password contain numbers?', true);
            $numbersLength = $numbersIncluded ? intval($this->ask('How many number do you want?', 3)) : 0;
            $symbolsIncluded = $this->confirm('Do you want password contain symbols?', true);
            $symbolsLength = $symbolsIncluded ? intval($this->ask('How many characters do you want?', 3)) : 0;

            if (!$lowerCasesIncluded
                && !$upperCasesIncluded
                && !$numbersIncluded
                && !$symbolsIncluded) {
                $this->error('Password must include at least one of following types: lower or upper cases, numbers, symbols.');
                return false;
            }

            if (!$lowerCasesLength
                && !$upperCasesLength
                && !$numbersLength
                && !$symbolsLength) {
                $this->error('Password cannot be zero length');
                return false;
            }

            $this->password = (new PasswordGenerator())
                ->excludeSimilarCharacters($similarCharactersExcluded)
                ->includeUpperCases($upperCasesIncluded)
                ->includeLowerCases($lowerCasesIncluded)
                ->includeNumbers($numbersIncluded)
                ->includeSymbols($symbolsIncluded)
                ->setUpperCasesLength($upperCasesLength)
                ->setLowerCasesLength($lowerCasesLength)
                ->setNumbersLength($numbersLength)
                ->setSymbolsLength($symbolsLength)
                ->generate();
        }
        return true;
    }
}
