<?php

namespace App\Entity\Adverts;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $type
 * @property string $default
 * @property boolean $required
 * @property array $variants
 * @property integer $sort
*/
class Attribute extends Model
{
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_FLOAT = 'float';

    protected $table = 'advert_attributes';
    public $timestamps = false;
    protected $fillable = ['name', 'type', 'required', 'default', 'variants', 'sort'];
    protected $casts = ['variants' => 'array'];

    /**
     * @return array
     */
    public static function typesList(): array
    {
        return [
            self::TYPE_STRING => 'String',
            self::TYPE_INTEGER => 'Integer',
            self::TYPE_FLOAT => 'Float',
        ];
    }

    /**
     * @return bool
     */
    public function isString(): bool
    {
        return $this->type === self::TYPE_STRING;
    }

    /**
     * @return bool
     */
    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
    }

    /**
     * @return bool
     */
    public function isFloat(): bool
    {
        return $this->type === self::TYPE_FLOAT;
    }

    /**
     * @return bool
     */
    public function isNumber(): bool
    {
        return $this->isInteger() || $this->isFloat();
    }

    /**
     * @return bool
     */
    public function isSelect(): bool
    {
        return count($this->variants) > 0;
    }
}
