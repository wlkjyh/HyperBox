<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\{Users, Compute, network, Instance, Volume, backup};

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            //code...
            $userrow = Users::where('token', $request->session()->get('token'))->first();
            return view('dashboard.index', compact('userrow'));
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            //code...
            $request->session()->forget('token');
            return redirect('/home/dashboard/authentication');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function main(Request $request)
    {
        try {
            $network = network::get();
            $arr = [];
            $uid = userrow('id');
            foreach ($network as $val) {
                if ($val->rule == 'ALL') {
                    $arr[] = $val;
                } else {
                    $k = json_decode($val->rule, true);
                    if (in_array($uid, $k)) {
                        $arr[] = $val;
                    }
                }
            }
            $network_count = count($arr);
            if (userrow('username') == 'admin') {
                $instance_count = Instance::count();
                $volume_count = Volume::count();
                $backup_count = backup::count();
            } else {
                // 计算实例数量
                $instance_count = Instance::where('userid', $uid)->count();

                $volume_count = Volume::where('userid', $uid)->count();

                // 备份数量
                $backup_count = backup::where('userid', $uid)->count();
            }
            //code...
            return view('dashboard.main', ['compute' => Compute::get(), 'network_count' => $network_count, 'instance_count' => $instance_count, 'volume_count' => $volume_count, 'backup_count' => $backup_count]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    // 网络拓扑
    public function networktop(Request $request)
    {
        try {
            if (getconfig('networktop') != 'true') {
                return abort(404);
            }
            //code...
            $network = network::get();
            $mynetwork = [];
            $uid = userrow('id');
            foreach ($network as $val) {
                if ($val->rule == 'ALL') {
                    $mynetwork[] = $val;
                } else {
                    $k = json_decode($val->rule, true);
                    if (in_array($uid, $k)) {
                        $mynetwork[] = $val;
                    }
                }
            }

            $network = Compute::get();
            $mycompute = [];
            $uid = userrow('id');
            foreach ($network as $val) {
                if ($val->rule == 'ALL') {
                    $mycompute[] = $val;
                } else {
                    $k = json_decode($val->rule, true);
                    if (in_array($uid, $k)) {
                        $mycompute[] = $val;
                    }
                }
            }

            foreach ($mycompute as $val) {
                $nodes[] = [
                    'id' => $val->id,
                    'type' => 'compute',
                    'typename' => '计算主机',
                    'namespace' => $val->hostname,
                ];
                $edges[] = [
                    'from' => $val->id,
                    'to' => -1
                ];
            }



            foreach ($mynetwork as $val) {
                $nodes[] = [
                    'id' => $val->id,
                    'type' => 'network',
                    'typename' => '网络',
                    'namespace' => $val->name,
                ];
                $edges[] = [
                    'from' => $val->id,
                    'to' => $val->compute
                ];
            }

            $route = \App\Route::get();
            foreach ($route as $val) {
                $nodes[] = [
                    'id' => $val->id,
                    'type' => 'route',
                    'typename' => '路由器',
                    'namespace' => $val->name,
                ];
                $edges[] = [
                    'from' => $val->id,
                    'to' => $val->network
                ];
            }

            if (userrow('username') == 'admin') {
                $myInstance = Instance::get();
            } else {
                $myInstance = Instance::where('userid', userrow('id'))->get();
            }
            foreach ($myInstance as $val) {
                $route = \App\Route::where('network', $val->network)->first();
                if (!$route) {
                    $nodes[] = [
                        'id' => $val->id,
                        'type' => 'instance',
                        'typename' => '实例',
                        'namespace' => $val->name,
                    ];
                    $edges[] = [
                        'from' => $val->id,
                        'to' => $val->network
                    ];
                } else {
                    $nodes[] = [
                        'id' => $val->id,
                        'type' => 'instance',
                        'typename' => '实例',
                        'namespace' => $val->name,
                    ];
                    $edges[] = [
                        'from' => $val->id,
                        'to' => $route->id
                    ];
                }
            }

            return view('dashboard.networktop', ['nodes' => json_encode($nodes), 'edges' => json_encode($edges)]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }
}
