<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AppOption
 * @package App\Models
 * @property string $type
 */
class AppOption extends Model
{
    const TYPE_NULL = 'null';
    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';

    const YES = 1;
    const NO = 2;

    protected $primaryKey = 'key';

    protected $table = 'app_options';

    public $incrementing = false;

    protected $fillable = [
        'key',
        'type',
        'value',
    ];

    public function getValueAttribute()
    {
        switch ($this->type) {
            case static::TYPE_NULL:
                return null;
            case static::TYPE_NUMBER:
                return floatval($this->attributes['value']);
            case static::TYPE_ARRAY:
                return json_decode($this->attributes['value'], true);
            case static::TYPE_OBJECT:
                return unserialize($this->attributes['object']);
            default:
                return $this->attributes['value'];
        }
    }

    public function setValueAttribute($value)
    {
        if (is_object($value)) {
            $this->attributes['type'] = static::TYPE_OBJECT;
            $this->attributes['value'] = serialize($value);
        } elseif (is_array($value)) {
            $this->attributes['type'] = static::TYPE_ARRAY;
            $this->attributes['value'] = json_encode($value);
        } elseif (is_numeric($value)) {
            $this->attributes['type'] = static::TYPE_NUMBER;
            $this->attributes['value'] = $value;
        } elseif (empty($value)) {
            $this->attributes['type'] = static::TYPE_NULL;
            $this->attributes['value'] = null;
        } else {
            $this->attributes['type'] = static::TYPE_STRING;
            $this->attributes['value'] = $value;
        }
    }
}
