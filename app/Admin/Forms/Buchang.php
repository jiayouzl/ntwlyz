<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Buchang extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '批量补偿(所有未到期的用户)';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dd($request -> all());
        $tianshu = $request -> tianshu;
        if ($tianshu > 50) {
            admin_error('补偿天数不能大于50天,请重新提交.');
            return back();
        }
        $ret = DB ::update("UPDATE `user_data` SET `enddate` = date_add(str_to_date(`enddate`,'%Y-%m-%d %H:%i:%s'), interval '{$tianshu}' day) WHERE `enddate` > Now()");
        if ($ret != 0) {
            admin_success('共:' . $ret . '位用户补偿:' . $tianshu . '天成功.');
            return back();
        } else {
            admin_error('补偿失败,请重试!~');
            return back();
        }
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this -> number('tianshu', '补偿天数：') -> help('选择需补偿的天数');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'tianshu' => '1'
        ];
    }
}
