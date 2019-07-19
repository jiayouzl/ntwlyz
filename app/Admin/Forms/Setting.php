<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

		$fileCharater = $request -> file('file_upload');
		if (!empty($fileCharater)) {
			if ($fileCharater -> isValid()) {
				//客户端文件名称..
				$clientName = $fileCharater -> getClientOriginalName();
				//获取文件的扩展名
				//$ext = $fileCharater->getClientOriginalExtension();
				//也就是该资源的媒体类型
				//$mimeTye = $fileCharater -> getMimeType();
				//获取待上传文件的绝对路径
				$path = $fileCharater -> getRealPath();
				//指定文件名
				//$filename = date('Y-m-d-h-i-s').'.'.$ext;
				//以下代码可用.
				//$ret=Storage::disk('admin')->put('files/'.$clientName, file_get_contents($path));
				//获取目录文件，两个返回值有差别，第一个带public
				//$files = Storage::allFiles('public');
				//$files = Storage::disk('public')->allFiles();
				Storage ::disk(config('admin.upload.disk')) -> put(config('admin.upload.directory.file') . '/' . $clientName, file_get_contents($path));
				$getupname = Storage ::disk(config('admin.upload.disk')) ->url(config('admin.upload.directory.file') . '/' . $clientName);
			}
		}

		$result = DB ::table('setting') -> where('id', 1) -> update([
			'shifoushiyong' => $request -> shiyong == 'on' ? 1 : 0,
			'time'          => $request -> time,
			'banben'        => $request -> banben,
			'dlldown'       => isset($getupname) ? $getupname : $request -> dlldown
		]);

		if (!empty($result) || !empty($request -> shiyong) && !empty($request -> time)) {
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
		$this ->switch('shiyong', '开启试用');
		$this -> number('time', '试用秒数') -> help('86400=1天');
		$this -> text('banben', '版本号') -> help('如不需要可留空') -> setWidth(2);
		$this -> text('dlldown', 'DLL下载地址') -> help('如不需要可留空') -> setWidth(5);
		$this -> file('file_upload', 'DLL上传') -> help('如需远程调用代码上传DLL,如不需要可留空.')-> setWidth(5);
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
			'banben'  => $result -> banben,
			'dlldown' => $result -> dlldown
		];
	}
}