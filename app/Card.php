<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	protected $connection = 'mysql';
	protected $table = 'cards';

	//可写入字段
	protected $fillable = [
		'card', 'password', 'type', 'consume','key','consumetime'
	];
	/**
	 * 表明模型是否应该被打上时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
}