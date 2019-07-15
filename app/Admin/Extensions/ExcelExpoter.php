<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter;

class ExcelExpoter extends ExcelExporter
{
	protected $fileName = '卡密列表.xlsx';
	protected $columns  = [
		'card'     => '卡号',
		'password' => '密码',
	];

	//这段代码是自己改的,教程上的代码是无法自定义文件名称的.
	public function SetName($wenjianmingcheng){
		$this->fileName=$wenjianmingcheng;
	}
}