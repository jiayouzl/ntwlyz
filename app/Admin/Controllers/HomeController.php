<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('后台首页')
            ->description('简要说明....')
	        ->breadcrumb(
		        ['text' => '用户管理', 'url' => '/users'],
		        ['text' => '充值卡管理', 'url' => '/cards'],
		        ['text' => '']
	        )
	        // 填充页面body部分，这里可以填入任何可被渲染的对象
	        ->body('速捷网络验证')
//            ->row(Dashboard::title())
            ->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::extensions());
//                });

                $row->column(6, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}