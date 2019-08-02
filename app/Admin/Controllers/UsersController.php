<?php

namespace App\Admin\Controllers;

use App\UserData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserData);
        $grid -> filter(function ($filter) {
            // 禁用id查询框
            $filter -> disableIdFilter();
            //精确搜索
            //            $filter -> where(function ($query) {
            //                $query -> where('key', $this -> input);
            //            }, '机器码');
            //模糊搜索
            $filter -> like('key', '机器码');
            //默认显示搜索框
            //$filter->expand();
        });
        $grid -> model() -> orderBy('id', 'desc');
        //$grid->column('id', __('ID'));
        $grid -> column('id', __('ID')) -> expand(function ($model) {
            $comments = $model -> LoginLog() -> take(10) -> orderBy('id', 'desc') -> get() -> map(function ($comment) {
                return $comment -> only([
                    'id',
                    'user_id',
                    'ip',
                    'logindate'
                ]);
            });
            return new Table([
                'ID',
                '用户ID',
                '登录IP',
                '登录时间'
            ], $comments -> toArray());
        });
        $grid -> column('key', __('机器码'));
        $grid -> column('ip', __('注册IP'));
        $grid -> column('regdate', __('注册日期'));
        $grid -> column('enddate', __('到期时间')) -> sortable();

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
        $show = new Show(UserData ::findOrFail($id));

        $show -> field('id', __('ID：'));
        $show -> field('key', __('机器码：'));
        $show -> field('ip', __('注册IP：'));
        $show -> field('regdate', __('注册日期：'));
        $show -> field('enddate', __('到期时间：'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserData);

        $form -> text('key', __('机器码：')) -> rules('required');
        $form -> text('ip', __('注册IP：')) -> rules('required');
        $form -> date('regdate', __('注册日期：')) ->default(date('Y-m-d')) -> rules('required');
        $form -> datetime('enddate', __('到期时间：')) ->default(date('Y-m-d H:i:s')) -> rules('required');

        return $form;
    }
}
