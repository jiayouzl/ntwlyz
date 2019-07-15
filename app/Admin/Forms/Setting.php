<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Setting extends Form
{
	/**
	 * The form title.
	 *
	 * @var string
	 */
	public $title = '网络验证功能设置';

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
		if ($request -> shiyong == 'on') {
			$result = DB ::table('setting') -> where('id', 1) -> update([
				'shifoushiyong' => 1,
				'time'          => $request -> time
			]);
		} else {
			$result = DB ::table('setting') -> where('id', 1) -> update([
				'shifoushiyong' => 0,
				'time'          => $request -> time
			]);
		}
		if (!empty($result)) {
			admin_success('修改成功....');
		} else {
			admin_error('修改失败....');
		}
		return back();
	}

	/**
	 * Build a form here.
	 */
	public function form()
	{
		$this ->switch('shiyong', '开启试用：');
		$this ->number('time', '试用秒数：')->help('86400=1天');
		//        $this->text('name')->rules('required');
		//        $this->email('email')->rules('email');
		//        $this->datetime('created_at');
		//        $this->textarea('describe', '简介');
	}

	/**
	 * The data of the form.
	 *
	 * @return array $data
	 */
	public function data()
	{
		$result = DB ::table('setting') -> where('id', 1) -> first();
		return [
			'shiyong' => $result -> shifoushiyong,
			'time'    => $result -> time,
		];
	}
}