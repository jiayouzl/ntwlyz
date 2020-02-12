<?php

namespace App\Http\Middleware;

use Closure;

class CheckDebug
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (config('DEBUG') == 'true') {
			$arr = [
				'code' => 4000,
				'msg'  => '维护中请稍后在重试。'
			];
			echo json_encode($arr);
			exit;
	    }
		return $next($request);
	}
}
