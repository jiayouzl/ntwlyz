<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Setting;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;

class SettingController extends Controller
{
	public function setting(Content $content)
	{
		return $content
			->title('系统设置')
			->body(new Setting());
	}
}