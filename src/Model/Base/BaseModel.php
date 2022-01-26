<?php


namespace Zjwansui\EasyLaravel\Model\Base;


use Zjwansui\EasyLaravel\Model\Events\ModelSaving;
use Zjwansui\EasyLaravel\Model\Helper\Builder;
use Zjwansui\EasyLaravel\Model\Helper\Collection;
use Zjwansui\EasyLaravel\Model\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BaseModel extends Model
{
    protected $connection = 'mysql';

    protected $table;

    public static $softDelete = true;


    public const FIELD_ID ='id';

    public const DELETED_AT = 'deleted_at';


//    protected static bool $serializeDateAsInteger = false;
    // 是否使用自带时间
    public $timestamps = false;
//    protected $dateFormat = 'U';
//    protected $dates = [
//        'created_at',
//        'deleted_at',
//        'updated_at',
//    ];
    protected $errors;

    public $validator;

    protected array $messages = [];

    public static array $attrs;

    /**
     * @var int|mixed
     */

    public function __construct(array $attributes = [])
    {
        $this->fillable(array_keys($this->_getRules()));
        self::$attrs = $this->fillable;
        parent::__construct($attributes);
    }

    private $_rules;

    private function _getRules(): array
    {
        if (is_array($this->_rules)) {
            return $this->_rules;
        }
        $this->_rules = $this->rules();
        if (!is_array($this->_rules)) $this->_rules = [];
        return $this->_rules;
    }

    protected $dispatchesEvents = [
        'saving' => ModelSaving::class
    ];

    protected function rules(): array
    {
        return [];
    }

    public function validate(array $attributes = null): bool
    {
        if ($this->exists) {
            if (!is_array($attributes)) $attributes = $this->getDirty();
            $rules = array_intersect_key($this->_rules, $attributes);
        } else {
            $attributes = $this->attributes;
            $rules = $this->_rules;
        }

        $this->validator = Validator::make($attributes, $rules, $this->messages);
        if ($this->validator->passes()) {
            return true;
        }

        $this->errors = $this->validator->errors();
        return false;
    }

    public function getAttributes(array $accept = null, array $except = null): array
    {
        $attributes = $this->attributes;

        if ($accept && is_array($accept)) {
            $attributes = array_intersect_key($attributes, array_flip($accept));
        }

        if ($except && is_array($except)) {
            $attributes = array_diff_key($attributes, array_flip($except));
        }

        return $this->addDateAttributesToArray($attributes);
    }

    public function hasAttribute($name): bool
    {
        return in_array($name, $this->fillable);
    }


    public function increment($column, $amount = 1, array $extra = []): int
    {
        return parent::increment($column, $amount, $extra);
    }


    public function decrement($column, $amount = 1, array $extra = []): int
    {
        return parent::decrement($column, $amount, $extra);
    }

    public function hasErrors(): bool
    {
        return isset($this->errors);
    }


    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }


    protected function newBaseQueryBuilder(): QueryBuilder
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }


//    protected function serializeDate(DateTimeInterface $date)
//    {
//        if (static::$serializeDateAsInteger) {
//            return $date->getTimestamp(); // 设置是否返回时间戳
//        }
//
//        return $date->format('Y-m-d H:i:s'); // 数据库格式
//    }

    /**
     * 将当前模型更改为数组集合
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }


    public static bool $displayAttributesOnly = false;


    public function displayAttributes()
    {
        return $this->dateAsInteger()->getAttributes(null, ['deleted', 'password_hash']);
    }

    public function attributesToArray()
    {
        return static::$displayAttributesOnly ? $this->displayAttributes() : parent::attributesToArray();
    }

}
