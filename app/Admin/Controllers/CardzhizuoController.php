<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Cardzhizuo;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;

class CardzhizuoController extends Controller
{
	public function index(Content $content)
	{
		return $content
			->title('充值卡制作')
			->body(new Cardzhizuo());
	}
}