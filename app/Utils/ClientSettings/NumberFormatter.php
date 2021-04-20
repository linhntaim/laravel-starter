<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

class NumberFormatter
{
    public const DEFAULT_NUMBER_OF_DECIMAL_POINTS = 2;

    #region Static
    /**
     * @var int
     */
    public $numberOfDecimalPoints;

    /**
     * @var string
     */
    private $type;

    public function __construct(Settings $settings = null)
    {
        $this->type = $settings->getNumberFormat();
        $this->modeNormal();
    }

    public function setType($value)
    {
        $this->type = $value;
        return $this;
    }

    public function modeInt()
    {
        return $this->mode(0);
    }

    public function modeNormal()
    {
        return $this->mode(static::DEFAULT_NUMBER_OF_DECIMAL_POINTS);
    }

    /**
     * @param int $numberOfDecimalPoints
     * @return NumberFormatter
     */
    public function mode($numberOfDecimalPoints)
    {
        $this->numberOfDecimalPoints = $numberOfDecimalPoints;
        return $this;
    }

    /**
     * @param float $number
     * @return string
     */
    public function format($number)
    {
        $number = floatval($number);
        switch ($this->type) {
            case 'point_comma':
                return $this->formatPointComma($number);
            case 'point_space':
                return $this->formatPointSpace($number);
            case 'comma_point':
                return $this->formatCommaPoint($number);
            case 'comma_space':
                return $this->formatCommaSpace($number);
            default:
                return $number;
        }
    }

    public function formatInt($number)
    {
        $number = $this->modeInt()->format($number);
        $this->modeNormal();
        return $number;
    }

    public function formatNumber($number, $mode = NumberFormatter::DEFAULT_NUMBER_OF_DECIMAL_POINTS)
    {
        $number = $this->mode($mode)->format($number);
        $this->modeNormal();
        return $number;
    }

    /**
     * @param string $formattedNumber
     * @return float
     */
    public function fromFormat($formattedNumber)
    {
        switch ($this->type) {
            case 'point_comma':
            case 'point_space':
                return $this->fromFormatPoint($formattedNumber);
            case 'comma_point':
            case 'comma_space':
                return $this->fromFormatComma($formattedNumber);
            default:
                return floatval($formattedNumber);
        }
    }

    public function getRegEx($totalLength, $pointLength)
    {
        $restLength = $totalLength - $pointLength;
        $groupMax = $restLength % 3 == 0 ? intval($restLength / 3 - 1) : intval($restLength / 3);
        $chars = $this->getCharsForRegEx();
        return "/^(\d{0,3}|\d{1,3}($chars[1]\d{3}){1,$groupMax})($chars[0]\d{0,$pointLength}){0,1}$/";
    }

    public function getChars()
    {
        switch ($this->type) {
            case 'comma_point':
                return [',', '.'];
            case 'comma_space':
                return [',', ' '];
            case 'point_space':
                return ['.', ' '];
            case 'point_comma':
            default:
                return ['.', ','];
        }
    }

    public function getCharsForRegEx()
    {
        switch ($this->type) {
            case 'comma_point':
                return ['\,', '\.'];
            case 'comma_space':
                return ['\,', '[ ]'];
            case 'point_space':
                return ['\.', '[ ]'];
            case 'point_comma':
            default:
                return ['\.', '\,'];
        }
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatPointComma($number)
    {
        return number_format($number, $this->numberOfDecimalPoints, '.', ',');
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatPointSpace($number)
    {
        return number_format($number, $this->numberOfDecimalPoints, '.', ' ');
    }

    /**
     * @param string $formattedNumber
     * @return float
     */
    public function fromFormatPoint($formattedNumber)
    {
        return floatval(preg_replace('/[^\d\.]+/', '', $formattedNumber));
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatCommaPoint($number)
    {
        return number_format($number, $this->numberOfDecimalPoints, ',', '.');
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatCommaSpace($number)
    {
        return number_format($number, $this->numberOfDecimalPoints, ',', ' ');
    }

    /**
     * @param string $formattedNumber
     * @return float
     */
    public function fromFormatComma($formattedNumber)
    {
        return floatval(str_replace(',', '.', preg_replace('/[^\d\,]+/', '', $formattedNumber)));
    }
}
