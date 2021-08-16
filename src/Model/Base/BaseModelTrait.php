<?php


namespace Zjwansui\EasyLaravel\Model\Base;


use Zjwansui\EasyLaravel\Model\Page\Page;
use Zjwansui\EasyLaravel\Model\SearchTools\OrderByFields;

trait BaseModelTrait
{
//    private $model;
//
//    public function __construct()
//    {
//        $this->model = new self();
//        // 是否提出删除的
//    }

    public function findById($id, $select = '*'): self
    {
        return self::select($select)->find($id);
    }

    public function createNew(self $model): self
    {
        return self::create($model);
    }

    public function edit($id, $update): self
    {
        $model = self::find($id);
        $model->update($update);
        $model->refresh();
        return $model;
    }

    public function del($id, $soft = true): ?bool
    {
        $model = $this->model->find($id);
        if ($soft) {
            $res = $model->destory();
        } else {
            $res = $model->update(['deleted_at' => time()]);
        }
        return $res;
    }

    public function findCountAndList($where = [], OrderByFields $orders = null, $select = '*', $pageSize = 10): Page
    {
        $query = self::select($select);
        if ($where) {
            $query = $query->where($where);
        }
        if ($orders) {
            foreach ($orders->orderByFields as $order) {
                $query = $query->orderBy($order->field, $order->sort);
            }
        }
//        return $query->paginate($pageSize, '*', 'page', $page)->toPage();
        return $query->paginate($pageSize)->toPage();
    }

    public function beforeUpdate()
    {

    }
}
