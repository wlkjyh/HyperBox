<?php

namespace App\Http\Middleware;

use Closure;
use App\Users;

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $cookie = $request->session()->get('token');

        if (!$cookie) {
            if (strpos($request->url(), '/api/') === false) {
                // 判断是不是acloud
                // exit($request->url());
                if (strpos($request->url(), '/acloud') === false) {
                    return redirect('/home/dashboard/authentication');
                } else {
                    return redirect('/acloud.middleware');
                }
            } else {
                return response()->json(['reqid' => uuid(), 'code' => 400, 'msg' => '请先登录']);
            }
        }
        $user = Users::where('token', $cookie)->first();
        if (!$user) {
            if (strpos($request->url(), '/api/') === false) {
                return redirect('/home/dashboard/authentication');
            } else {
                return response()->json(['reqid' => uuid(), 'code' => 400, 'msg' => '请先登录']);
            }
        }
        return $next($request);
    }
}
