<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Cardzhizuo extends Form
{
	/**
	 * The form title.
	 *
	 * @var string
	 */
	public $title = '充值卡批量生成';

	/**
	 * Handle the form request.
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request)
	{
		//dd($request->all());
		$shuliang = $request -> shuliang;
		if ($shuliang > 100) {
			admin_error('批量生成不能大于100张,请重新提交.');
			return back();
		}
		if (empty($request -> type)) {
			admin_error('参数错误');
			return back();
		}
		for ($i = 1; $i <= $shuliang; $i++) {
			$kahao  = "XJ" . substr(str_shuffle(implode("", range('0', '9'))), 0, 8);
			$mima   = substr(str_shuffle(implode("", range('A', 'Z'))), 0, 10);
			$result = DB ::table('cards') -> insert([
				'card'     => $kahao,
				'password' => $mima,
				'type'     => $request -> type,
				'consume'  => 0
			]);
			if (empty($result)) {
				admin_error('生成到第：' . $i . '张失败，循环停止！');
				break;
				return back();
			}
		}
		if (!empty($result)) {
			admin_success('充值卡制作' . $request -> shuliang . '张成功');
			return redirect('admin/cards');
		}
	}

	/**
	 * Build a form here.
	 */
	public function form()
	{
		$this -> select('type', __('充值卡时效：')) -> options([
			1 => '天卡',
			2 => '周卡',
			3 => '月卡',
			4 => '季卡',
			5 => '半年卡',
			6 => '年卡'
		]);
		$this -> number('shuliang', '生成数量：');
	}

	/**
	 * The data of the form.
	 *
	 * @return array $data
	 */
	public function data()
	{
		return [
			'type'     => 1,
			'shuliang' => '10',
		];
	}
}