<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BatchJieFengka extends BatchAction
{
    public $name = '批量解封卡';

    public function handle(Collection $collection)
    {
        $arr = [];
        // 对每一个行的数据模型封卡
        foreach ($collection as $model) {
            $arr[] = $model -> getkey();
        }
        $ret = DB ::table('cards') -> whereIn('id', $arr) -> update(['beifeng' => 0]);
        // 返回一个`解封卡成功`的成功信息，并且刷新页面
        return $this -> response() -> success('选定的:' . $ret . '张卡被解封成功') -> refresh();
    }
}
