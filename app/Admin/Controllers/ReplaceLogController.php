<?php

namespace App\Admin\Controllers;

use App\ReplaceLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReplaceLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户授权转移日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReplaceLog);
        //禁用查询过滤器
        $grid->disableFilter();
        //禁用分页条
        $grid->disableCreateButton();
        //禁用行选择器
        $grid->disableActions();
        //禁用导出数据按钮
        $grid->disableExport();
        $grid->model()->orderBy('id', 'desc');
        $grid->column('id', __('ID'));
        $grid->column('key1', __('被转机器码'))->display(function ($val){
            return "<a href='users?&key={$val}'>{$val}</a>";
        });
        $grid->column('key2', __('转入机器码'))->display(function ($val){
            return "<a href='users?&key={$val}'>{$val}</a>";
        });
        $grid->column('day', __('转入天数'));
        $grid->column('replacetime', __('操作时间'));

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
        $show = new Show(ReplaceLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('key1', __('Key1'));
        $show->field('key2', __('Key2'));
        $show->field('day', __('Day'));
        $show->field('replacetime', __('Replacetime'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReplaceLog);

        $form->text('key1', __('Key1'));
        $form->text('key2', __('Key2'));
        $form->text('day', __('Day'));
        $form->datetime('replacetime', __('Replacetime'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
