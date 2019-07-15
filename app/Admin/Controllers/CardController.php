<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Card;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CardController extends AdminController
{
	/**
	 * Title for current resource.
	 *
	 * @var string
	 */
	protected $title = '充值卡';

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid()
	{
		$grid = new Grid(new Card);
		$grid -> filter(function ($filter) {
			// 禁用id查询框
			$filter -> disableIdFilter();
			$filter -> like('card', '卡号：');
			//$filter->expand();//默认显示搜索框
		});
		//快捷搜索
		$grid->quickSearch('card');
		//快捷键功能
		$grid->enableHotKeys();
		//禁用创建按钮
		$grid -> disableCreateButton();
		//禁用行的查看按钮
		$grid -> actions(function ($actions) {
			$actions -> disableView();
		});
		$grid -> model() -> orderBy('id', 'desc');
		//$grid->model()->where('id', '<', 100);
		$grid -> column('id', __('ID')) -> orderBy('id', 'desc');
		$grid -> column('card', __('卡号')) -> width(290) -> copyable();
		$grid -> column('password', __('密码')) -> width(290) -> copyable();
		$grid -> column('type', __('类型')) -> display(function ($type) {
			//1=天卡,2=周卡,3=月卡,4=季卡,5=半年卡,6=年卡
			if ($type == 1) {
				return '天卡';
			} elseif ($type == 2) {
				return '周卡';
			} elseif ($type == 3) {
				return '月卡';
			} elseif ($type == 4) {
				return '季卡';
			} elseif ($type == 5) {
				return '半年卡';
			} elseif ($type == 6) {
				return '年卡';
			}
		});
		$grid -> column('consume', __('是否已充值')) -> display(function ($consume) {
			return $consume == 0 ? '未充值' : "<span style='color:red'>已充值</span>";
		}) -> sortable() -> help('点击可排序');
		$grid -> column('key', __('被充值Key'));
		$grid -> column('consumetime', __('充值时间')) -> editable('datetime');
		//默认为每页20条
		$grid -> paginate(20);
		//以EXCEL导出
		$ExcelExpoter=new ExcelExpoter();
		$ExcelExpoter->SetName('卡密列表'.Carbon ::now() -> toDateTimeString().'.xlsx');
		$grid->exporter($ExcelExpoter);
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
		$show = new Show(Card ::findOrFail($id));

		$show -> field('id', __('ID'));
		$show -> field('card', __('卡号'));
		$show -> field('password', __('密码'));
		$show -> field('type', __('类型'));
		$show -> field('consume', __('是否已充值'));
		$show -> field('key', __('被充值Key'));
		$show -> field('consumetime', __('充值时间'));

		return $show;
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form()
	{
		$form = new Form(new Card);

		$form->footer(function ($footer) {
			// 去掉重置按钮
			$footer->disableReset();
			// 去掉查看
			$footer->disableViewCheck();
			// 去掉继续编辑按钮
			$footer->disableEditingCheck();
			// 去掉继续创建按钮
			$footer->disableCreatingCheck();
		});

		$form -> text('card', __('卡号'));
		$form -> text('password', __('密码'));
		//$form ->switch('type', __('类型'));
		$form->select('type', __('类型'))->options([1 => '天卡', 2 => '周卡', 3 => '月卡', 4 => '季卡', 5 => '半年卡', 6 => '年卡']);
		$states = [
			'on'  => ['value' => 1, 'text' => '已充值', 'color' => 'danger'],
			'off' => ['value' => 0, 'text' => '未充值', 'color' => 'success'],
		];
		$form ->switch('consume', __('是否已充值'))->states($states);
		$form -> text('key', __('被充值Key'));
		$form -> datetime('consumetime', __('充值时间'))->default(date('Y-m-d H:i:s'));

		return $form;
	}
}