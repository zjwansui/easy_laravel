<?php


namespace Zjwansui\EasyLaravel\Model\Base;


use App\Services\Common\Auth;
use Zjwansui\EasyLaravel\Model\Page\Page;
use Zjwansui\EasyLaravel\Model\SearchTools\OrderByFields;
use Illuminate\Support\Arr;

trait BaseModelTrait
{
    /** 使用方法 **/
    //        $user = NgoUserManager::store($a);
    //        $user =  NgoUser::find("61cec0886c017202c7323eb3");
    //        $user = NgoUserManager::edit($a, '61cec1d16c017202c7323eb4');
    //        $userId = NgoUserManager::remove('61cec1d16c017202c7323eb4');

    protected static function beforeStore(&$data): void
    {
        // 处理新增之前的特殊情况 可特殊覆盖
    }

    public static function store($data): self
    {
        self::beforeStore($data);
        return self::create($data)->refresh();
    }

    protected static function beforeUpdate(&$data, $id): void
    {
        // 可特殊覆盖
        $data = Arr::add($data, 'updated_by', Auth::id());
        $data = Arr::add($data, 'updated_at', time());
    }

    public static function edit($data, $id): ?self
    {
        self::beforeUpdate($data, $id);
        $model =  self::whereNull(self::DELETED_AT)->find($id);
        if (!$model) {
            return null;
        }
        $result = $model->update($data);

        if (!$result) {
            return null;
        }
        return $model->refresh();
    }

    public static function show($id): ?self
    {
        return self::whereNull(self::DELETED_AT)->find($id);
    }


    public static function remove($id)
    {
        $model = self::whereNull(self::DELETED_AT)->find($id);
        if (!$model) {
            return null;
        }
        if (self::$softDelete) {
            return $model->update(['deleted_at' => time()]);
        } else {
            return $model->destroy($id);
        }
        return null;
    }

}
