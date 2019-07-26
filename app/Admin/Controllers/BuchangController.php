<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Buchang;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;

class BuchangController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('用户补偿')
            ->body(new Buchang());
    }
}
