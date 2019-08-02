<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\LoginLog;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LoginLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户登录日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LoginLog);
        //禁用查询过滤器
        $grid->disableFilter();
        //禁用分页条
        $grid->disableCreateButton();
        //禁用行选择器
        $grid->disableActions();
        //禁用导出数据按钮
        $grid->disableExport();
        $grid->model()->orderBy('id', 'desc');
        //$grid->column('id', __('ID'));
        $grid->column('user_id', __('用户ID'));
        $grid->column('User_Loginlog.key',__('用户机器码'))->display(function ($val) {
            return "<a href='users?&key={$val}'>{$val}</a>";
        });
        $grid->column('ip', __('登录IP所在地'));
        $grid->column('logindate', __('登录时间'))->sortable();
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(LoginLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('ip', __('Ip'));
        $show->field('logindate', __('Logindate'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LoginLog);

        $form->number('user_id', __('User id'));
        $form->ip('ip', __('Ip'));
        $form->datetime('logindate', __('Logindate'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
