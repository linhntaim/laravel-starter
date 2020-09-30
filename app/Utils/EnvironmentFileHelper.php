<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

class EnvironmentFileHelper
{
    protected $content;
    protected $modified;
    protected $filePath;

    public function __construct()
    {
        $this->filePath = app()->environmentFilePath();
        $this->modified = false;
        $this->capture();
    }

    public function capture()
    {
        $this->content = file_get_contents($this->filePath);
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function save()
    {
        if ($this->modified) {
            file_put_contents($this->filePath, $this->content);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function fill($key, $value)
    {
        $key = strtoupper($key);
        $replacing = $this->replacing($key);
        $replaced = $this->replaced($key, $value);
        if (preg_match_all($replacing, $this->content) === 1) {
            $this->content = preg_replace(
                $replacing,
                $replaced,
                $this->content
            );
        } else {
            $this->content .= PHP_EOL . $replaced;
        }
        $this->modified = true;
        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function replacing($key)
    {
        return sprintf('/^%s=.*/m', $key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return string
     */
    protected function replaced($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if (mb_strpos($value, ' ') !== false || preg_match('/(^\\"|\\"$)/', $value) === 1) {
            $value = sprintf('"%s"', preg_replace('/(^\\\"(.+)\\\"$)/', '\\"$1\\"', str_replace('"', '\\"', $value)));
        }
        return sprintf('%s=%s', $key, $value);
    }
}