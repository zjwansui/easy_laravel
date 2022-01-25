<?php

namespace Zjwansui\EasyLaravel\Model\Base;


use App\Helpers\Tools\Time;
use App\Models\MongoDb\Document\BaseModel;
use Illuminate\Support\Arr;
use MongoDB\BSON\ObjectId;

class MongoBaseModel
{

    /** 使用方法 **/
    //        $user = NgoUserManager::store($a);
    //        $user =  NgoUser::find("61cec0886c017202c7323eb3");
    //        $user = NgoUserManager::edit($a, '61cec1d16c017202c7323eb4');
    //        $userId = NgoUserManager::remove('61cec1d16c017202c7323eb4');


    protected static function beforeStore(&$data): void
    {
        // 处理新增之前的特殊情况 可特殊覆盖
        if (!array_key_exists('_id', $data)) {
            $data['_id'] = (string)new ObjectId();
        }
    }

    public static  function store($data): self
    {
        self::beforeStore($data);
        return self::create($data);
    }

    protected static function beforeUpdate(&$data, $id): void
    {
        // 可特殊覆盖
        $data = Arr::except($data, ['_id', 'created_at']);
    }

    public static  function edit($data, $id): ?self
    {
        self::beforeUpdate($data, $id);
        $model = self::find($id);
        if (!$model) {
            return null;
        }
        $result = $model->update($data);

        if (!$result) {
            return null;
        }
        return $model->refresh();
    }

    public static function show($id):?self
    {
        return self::find($id);
    }

    public static  function remove($id)
    {
        if (self::softDelete) {
            $deleted = 1 === self::find($id)->update([self::softDelete => (microtime(true) * 1000)]);
        } else {
            $deleted = 1 === self::destroy($id);
        }
        if ($deleted) {
            return $id;
        }
        return null;
    }

}