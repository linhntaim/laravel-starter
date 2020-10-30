<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

class PasswordGenerator
{
    protected $symbolsIncluded;
    protected $numbersIncluded;
    protected $lowerCasesIncluded;
    protected $upperCasesIncluded;
    protected $symbolsLength;
    protected $numbersLength;
    protected $lowerCasesLength;
    protected $upperCasesLength;
    protected $lowerCaseCharacters = 'abcdefghijklmnopqrstuvwxyz';
    protected $upperCaseCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected $numberCharacters = '0123456789';
    protected $symbolCharacters = '!@#$%^&*()-=_+';

    public function excludeSimilarCharacters($excluded = true)
    {
        if ($excluded == true) {
            // Exclude: i, I, l, L, 1, o, O, 0
            $this->lowerCaseCharacters = 'abcdefghjkmnpqrstuvwxyz';
            $this->upperCaseCharacters = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            $this->numberCharacters = '23456789';
        }
        return $this;
    }

    public function includeUpperCases($upperCasesIncluded = true)
    {
        $this->upperCasesIncluded = $upperCasesIncluded;
        return $this;
    }

    public function includeLowerCases($lowerCasesIncluded = true)
    {
        $this->lowerCasesIncluded = $lowerCasesIncluded;
        return $this;
    }

    public function includeNumbers($numbersIncluded = true)
    {
        $this->numbersIncluded = $numbersIncluded;
        return $this;
    }

    public function includeSymbols($symbolsIncluded = true)
    {
        $this->symbolsIncluded = $symbolsIncluded;
        return $this;
    }

    public function setLowerCasesLength($lowerCasesLength = 3)
    {
        $this->lowerCasesLength = $lowerCasesLength;
        return $this;
    }

    public function setUpperCasesLength($upperCasesLength = 3)
    {
        $this->upperCasesLength = $upperCasesLength;
        return $this;
    }

    public function setNumbersLength($numbersLength = 3)
    {
        $this->numbersLength = $numbersLength;
        return $this;
    }

    public function setSymbolsLength($symbolsLength = 3)
    {
        $this->symbolsLength = $symbolsLength;
        return $this;
    }

    protected function randomString($included, $length, $characters) {
        $random = '';
        if ($included && $length) {
            while (($d = $length - strlen($random)) > 0) {
                $random .= substr(str_shuffle($characters), 0, $d);
            }
        }
        return $random;
    }

    public function generate()
    {
        return str_shuffle(
            $this->randomString(
                $this->lowerCasesIncluded,
                $this->lowerCasesLength,
                $this->lowerCaseCharacters
            )
            . $this->randomString(
                $this->upperCasesIncluded,
                $this->upperCasesLength,
                $this->upperCaseCharacters
            )
            . $this->randomString(
                $this->numbersIncluded,
                $this->numbersLength,
                $this->numberCharacters
            )
            . $this->randomString(
                $this->symbolsIncluded,
                $this->symbolsLength,
                $this->symbolCharacters
            )
        );
    }

    public static function random()
    {
        return (new PasswordGenerator())->excludeSimilarCharacters(true)
            ->includeUpperCases(true)
            ->setUpperCasesLength(3)
            ->includeLowerCases(true)
            ->setLowerCasesLength(3)
            ->includeNumbers(true)
            ->setNumbersLength(3)
            ->includeSymbols(true)
            ->setSymbolsLength(3)
            ->generate();
    }
}
