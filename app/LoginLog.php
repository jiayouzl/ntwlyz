<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
	protected $connection = 'mysql';
	protected $table = 'login_logs';
	/**
	 * 表明模型是否应该被打上时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
}