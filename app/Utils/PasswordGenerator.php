<?php

namespace App\Utils;

class PasswordGenerator
{
    protected $includeSymbols;
    protected $includeNumbers;
    protected $includeLowerCases;
    protected $includeUpperCases;
    protected $symbolLength;
    protected $numberLength;
    protected $lowerCaseLength;
    protected $upperCaseLength;
    protected $lowercaseCharacter = 'abcdefghijklmnopqrstuvwxyz';
    protected $uppercaseCharacter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected $numberCharacter = '0123456789';
    protected $symbolCharacter = '!@#$%^&*()-=_+';

    public function excludeSimilarCharacter($isExclude = true)
    {
        if ($isExclude == true) {
            // Exclude: e.g. i, l, 1, L, o, 0, O 
            $this->lowercaseCharacter = 'abcdefghjkmnpqrstuvwxyz';
            $this->uppercaseCharacter = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            $this->numberCharacter = '23456789';
        }
        return $this;
    }

    public function includeUpperCases($includeUpperCases = true)
    {
        $this->includeUpperCases = $includeUpperCases;
        return $this;
    }

    public function includeLowerCases($includeLowerCases = true)
    {
        $this->includeLowerCases = $includeLowerCases;
        return $this;
    }

    public function includeNumbers($includeNumbers = true)
    {
        $this->includeNumbers = $includeNumbers;
        return $this;
    }

    public function includeSymbols($includeSymbols = true)
    {
        $this->includeSymbols = $includeSymbols;
        return $this;
    }

    public function setSymbolLength($symbolLength)
    {
        $this->symbolLength = $symbolLength;
        return $this;
    }

    public function setNumberLength($numberLength)
    {
        $this->numberLength = $numberLength;
        return $this;
    }

    public function setUpperCaseLength($upperCaseLength)
    {
        $this->upperCaseLength = $upperCaseLength;
        return $this;
    }

    public function setLowerCaseLength($lowerCaseLength)
    {
        $this->lowerCaseLength = $lowerCaseLength;
        return $this;
    }

    public function generate()
    {
        $str1 = $this->includeLowerCases ? substr(str_shuffle($this->lowercaseCharacter), 0, $this->lowerCaseLength) : '';
        $str2 = $this->includeUpperCases ? substr(str_shuffle($this->uppercaseCharacter), 0, $this->upperCaseLength) : '';
        $str3 = $this->includeNumbers ? substr(str_shuffle($this->numberCharacter), 0, $this->numberLength) : '';
        $str4 = $this->includeSymbols ? substr(str_shuffle($this->symbolCharacter), 0, $this->symbolLength) : '';


        return str_shuffle($str1 . $str2 . $str3 . $str4);
    }
}
