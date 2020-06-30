<?php

namespace App\Utils;

abstract class BaseNumberFormatHelper
{
    const DEFAULT_NUMBER_OF_DECIMAL_POINTS = 2;

    #region Static
    /**
     * @var int
     */
    public static $NUMBER_OF_DECIMAL_POINTS;

    /**
     * @var NumberFormatHelper
     */
    protected static $instance;

    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    #endregion

    /**
     * @var string
     */
    private $type;

    public function __construct(LocalizationHelper $localizationHelper = null)
    {
        if ($localizationHelper == null) {
            $localizationHelper = LocalizationHelper::getInstance();
        }
        $this->type = $localizationHelper->getNumberFormat();
        $this->modeNormal();
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function modeInt()
    {
        $this->mode(0);
    }

    public function modeNormal()
    {
        $this->mode(static::DEFAULT_NUMBER_OF_DECIMAL_POINTS);
    }

    /**
     * @param int $numberOfDecimalPoints
     */
    public function mode($numberOfDecimalPoints)
    {
        static::$NUMBER_OF_DECIMAL_POINTS = $numberOfDecimalPoints;
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
        $this->modeInt();
        $number = $this->format($number);
        $this->modeNormal();
        return $number;
    }

    public function formatNumber($number, $mode = BaseNumberFormatHelper::DEFAULT_NUMBER_OF_DECIMAL_POINTS)
    {
        $this->mode($mode);
        $number = $this->format($number);
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
            case 'point_comma':
                return ['.', ','];
            case 'point_space':
                return ['.', ' '];
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
            case 'point_comma':
                return ['\.', '\,'];
            case 'point_space':
                return ['\.', '[ ]'];
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
        return number_format($number, static::$NUMBER_OF_DECIMAL_POINTS, '.', ',');
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatPointSpace($number)
    {
        return number_format($number, static::$NUMBER_OF_DECIMAL_POINTS, '.', ' ');
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
        return number_format($number, static::$NUMBER_OF_DECIMAL_POINTS, ',', '.');
    }

    /**
     * @param float $number
     * @return string
     */
    public function formatCommaSpace($number)
    {
        return number_format($number, static::$NUMBER_OF_DECIMAL_POINTS, ',', ' ');
    }

    /**
     * @param string $formattedNumber
     * @return float
     */
    public function fromFormatComma($formattedNumber)
    {
        return floatval(str_replace(',', '.', preg_replace('/[^\d\,]+/', '', $formattedNumber)));
    }

    public static function doFormat($number, $type)
    {
        switch ($type) {
            case 'point_comma':
                return static::getInstance()->formatPointComma($number);
            case 'point_space':
                return static::getInstance()->formatPointSpace($number);
            case 'comma_point':
                return static::getInstance()->formatCommaPoint($number);
            case 'comma_space':
                return static::getInstance()->formatCommaSpace($number);
            default:
                return static::getInstance()->format($number);
        }
    }

    public static function doFromFormat($formattedNumber, $type)
    {
        switch ($type) {
            case 'point_comma':
            case 'point_space':
                return static::getInstance()->fromFormatPoint($formattedNumber);
            case 'comma_point':
            case 'comma_space':
                return static::getInstance()->fromFormatComma($formattedNumber);
            default:
                return static::getInstance()->fromFormat($formattedNumber);
        }
    }
}
