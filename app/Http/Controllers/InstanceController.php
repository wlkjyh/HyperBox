<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Client, Instance, network, volume, flavor, Compute, logs, backup};

class InstanceController extends Controller
{
    //
    public function Console(Request $request, $id)
    {
        try {
            //code...
            if (userrow('username') == 'admin') {
                $row = Instance::where('id', $id)->first();
            } else {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            }
            if (!$row) return $this->throwable('Instance not found');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->throwable('Compute not found');
            $hostname = $compute->hostname;
            // if (strstr($hostname, ':')) {
            //     $hostname = explode(':', $hostname);
            //     $freerdp = 'http://' . $hostname[0] . ':6500/#vid=' . $row->vid;
            // } else {
            //     $freerdp = 'http://' . $hostname . ':6500/#vid=' . $row->vid;
            // }
            
            $exp = explode("\n", $compute->console);
            $rdp = $exp[mt_rand(0, count($exp) - 1)];

            $freerdp = 'http://'.$rdp.'/#vid=' . $row->vid;
            $log = logs::where('form', $id)->orderBy('created_at', 'desc')->get();

            $myVolume = volume::where('userid', userrow('id'))->where('instance', $id)->get();
            return view('dashboard.console', ['id' => $id, 'row' => $row, 'freerdp' => $freerdp, 'log' => $log, 'myVolume' => $myVolume]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }
    // 启动顺序
    public function boot_instance(Request $request)
    {
        try {
            //code...
            $id = $request->id;
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) return $this->throwable('Instance not found');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->throwable('Compute not found');
            if ($row->bootstrap == '') {
                $list = [
                    'CD', 'IDE', 'LegacyNetworkAdapter'
                ];
            } else {
                $list = json_decode($row->bootstrap, true);
            }
            return view('dashboard.boot_instance', ['id' => $id, 'row' => $row, 'compute' => $compute, 'boot' => $list]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function boot_api_instance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) return $this->display(500, 'Instance not found');
            $list = $request->input('list');
            // 判断是不是数组
            if (!is_array($list)) {
                return $this->display(500, 'Bootstrap must be an array');
            }
            $allow = [
                'CD', 'IDE', 'LegacyNetworkAdapter'
            ];
            foreach ($list as $val) {
                if (!in_array($val, $allow)) {
                    return $this->display(500, 'Bootstrap must be one of the following: CD, IDE, LegacyNetworkAdapter');
                }
            }
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->display(500, 'Compute not found');
            // print_r($list);exit;
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/bootinstance', [
                'f1' => $list[0],
                'f2' => $list[1],
                'f3' => $list[2],
                'name' => $row->id,
            ])->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                $row->bootstrap = json_encode($list);
                $row->save();
                writelog($id, date('Y-m-d H:i:s') . ' 修改启动顺序');
                return $this->display(200, 'Success');
            } else {
                writelog($id, date('Y-m-d H:i:s') . ' 修改启动顺序失败：' . $req['error']);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function connect_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) return $this->throwable('卷不存在');
            if ($row->instance != '') return $this->throwable('卷已被连接');
            $myInstance = Instance::where('vid', '!=', 'unknown')->get();
            return view('dashboard.connect_volume', ['id' => $id, 'row' => $row, 'myInstance' => $myInstance]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function connect_api_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $instance = $request->input('instance');
            $userrow = userrow();
            if($userrow->username != 'admin') return $this->display(500, '改云桌面了,非admin不能连接卷');
            if ($userrow->username != 'admin') {
                $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Volume::where('id', $id)->first();
            }

            if (!$row) return $this->display(500, '卷不存在');
            if ($row->instance != '') return $this->display(500, '卷已被连接');
            $row2 = Instance::where('id', $instance)->first();
            if (!$row2) return $this->display(500, '实例不存在');
            $compute = compute::where('id', $row2->compute)->first();
            if (!$compute) return $this->display(500, '计算主机不存在');

            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/connectvolume', [
                'name' => $instance,
                'volume' => $id,
            ])->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                $row->instance = $instance;


                // 妈的我真是服了这个获取ide了，还要用以前的代码才能获取出来操
                $exp = explode("\n", $req['data']);
                foreach ($exp as $key => $val) {
                    $val = trim($val);
                    if ($val == '' || $val == null) unset($exp[$key]);
                }
                $exp = array_values($exp);
                if (count($exp) == 0 || count($exp) % 2 === 1) return $this->display(500, '连接卷失败');
                $powers = [];
                $ids = [];
                for ($i = 0; $i < floor(count($exp) / 2); $i++) {
                    $powers[] = $exp[$i];
                    $ids[] = $exp[$i + floor(count($exp) / 2)];
                }
                $data = [];
                for ($i = 0; $i < count($powers); $i++) {
                    $idses = $powers[$i];
                    $path = $ids[$i];
                    if (strstr(trim($path), $id)) $ide = $idses;
                }



                $row->ide = $ide;
                $row->save();
                writelog($instance, date('Y-m-d H:i:s') . ' 连接卷到实例');
                return $this->display(200, 'Success');
            } else {
                $error = isset($req['error']) ? $req['error'] : '未知错误';
                writelog($instance, date('Y-m-d H:i:s') . ' 连接卷到实例失败：' . $error);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    // 分离卷
    public function unconnect_api_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) return $this->display(500, '卷不存在');
            if ($row->instance == '') return $this->display(500, '卷未被连接');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->display(500, '计算主机不存在');
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/unconnectvolume', [
                'name' => $row->instance,
                'ide' => $row->ide,
            ])->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                $row->instance = '';
                $row->ide = 0;
                $row->save();
                writelog($row->instance, date('Y-m-d H:i:s') . ' 分离卷');
                return $this->display(200, 'Success');
            } else {
                $error = isset($req['error']) ? $req['error'] : '未知错误';
                writelog($row->instance, date('Y-m-d H:i:s') . ' 分离卷失败：' . $error);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function volume_instance_connect(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) return $this->display(500, '实例不存在');
            $myVolume = Volume::where('instance', '')->get();
            return view('dashboard.volume_instance_connect', [
                'myVolume' => $myVolume,
                'row' => $row,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function virtual_instance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) return $this->display(500, '实例不存在');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->display(500, '计算主机不存在');
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/virtualinstance', [
                'name' => $row->id,
            ])->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                writelog($id, date('Y-m-d H:i:s') . ' 启动嵌套虚拟化');
                return $this->display(200, 'Success');
            } else {
                $error = isset($req['error']) ? $req['error'] : '未知错误';
                writelog($id, date('Y-m-d H:i:s') . ' 启动嵌套虚拟化失败：' . $error);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function getcpu(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if ($id == '') return $this->display(500, '实例不存在');
            if (userrow('username') == 'admin') {
                $row = Instance::where('id', $id)->first();
            } else {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            }
            if (!$row) return $this->display(500, '实例不存在');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->display(500, '计算主机不存在');
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/getcpu', [
                'name' => $id,
            ])->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                // writelog($id, date('Y-m-d H:i:s') . ' 获取CPU使用率');
                // return $this->display(200, 'Success', $req['data']);
                $flavor = flavor::where('id', $row->flavor)->first();
                if (!$flavor) return $this->display(500, '实例规格不存在');
                $max = $flavor->vcpu / $compute->vcpu * 100;
                $use = $req['cpu'];
                // 计算出use占用mac的百分比
                $use = $use / $max * 100;
                if ($use > 100) $percent = 100;
                else $percent = $use;
                return response()->json([
                    'code' => 200,
                    'cpu' => $percent,
                ]);
            } else {
                $error = isset($req['error']) ? $req['error'] : '未知错误';
                writelog($id, date('Y-m-d H:i:s') . ' 获取CPU使用率失败：' . $error);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function backup_instance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            // $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            if (userrow('username') == 'admin') {
                $row = Instance::where('id', $id)->first();
            } else {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            }

            if (!$row) return $this->display(500, '实例不存在');
            return view('dashboard.backup_instance', [
                'row' => $row,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function backinstance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $name = $request->input('name');
            // $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            if (userrow('username') == 'admin') {
                $row = Instance::where('id', $id)->first();
            } else {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            }

            if (!$row) return $this->display(500, '实例不存在');
            $row2 = backup::where('name', $name)->first();
            if ($row2) return $this->display(500, '备份名称已存在');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->display(500, '计算主机不存在');
            $uuid = uuid();
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/backupinstance', [
                'name' => $id,
                'id' => $uuid,
            ])->Do(120)->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
            if ($req['code'] == 200) {
                backup::create([
                    'name' => $name,
                    'id' => $uuid,
                    'userid' => userrow('id'),
                    'uuid' => $id,
                    'type' => 'instance',
                ]);
                writelog($id, date('Y-m-d H:i:s') . ' 备份实例');
                return $this->display(200, 'Success');
            } else {
                writelog($id, date('Y-m-d H:i:s') . ' 备份失败：' . $req['error']);
                return $this->display(500, $req['msg']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function delete_backup(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            // $row = backup::where('id', $id)->where('userid', userrow('id'))->first();
            if (userrow('username') == 'admin') {
                $row = backup::where('id', $id)->first();
            } else {
                $row = backup::where('id', $id)->where('userid', userrow('id'))->first();
            }
            if (!$row) return $this->display(500, '备份不存在');
            if ($row->type == 'instance') {
                $instance = Instance::where('id', $row->uuid)->first();
                if (!$instance) return $this->display(500, '实例不存在');
                $compute = compute::where('id', $instance->compute)->first();
                if (!$compute) return $this->display(500, '计算主机不存在');

                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/deletebackup', [
                    // 'name' => $row->uuid,
                    'id' => $id,
                    'name' => $instance->name,
                ])->Do()->getResponseBodyJson();
                if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
                if ($req['code'] == 200) {
                    $row->delete();
                    writelog($row->uuid, date('Y-m-d H:i:s') . ' 删除备份');
                    return $this->display(200, 'Success');
                } else {
                    $error = isset($req['error']) ? $req['error'] : '未知错误';
                    writelog($row->uuid, date('Y-m-d H:i:s') . ' 删除备份失败：' . $error);
                    return $this->display(500, $req['msg']);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function reback_backup(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            // $row = backup::where('id', $id)->where('userid', userrow('id'))->first();
            if (userrow('username') == 'admin') {
                $row = backup::where('id', $id)->first();
            } else {
                $row = backup::where('id', $id)->where('userid', userrow('id'))->first();
            }

            if (!$row) return $this->display(500, '备份不存在');
            if ($row->type == 'instance') {
                $instance = Instance::where('id', $row->uuid)->first();
                if (!$instance) return $this->display(500, '实例不存在');
                $compute = compute::where('id', $instance->compute)->first();
                if (!$compute) return $this->display(500, '计算主机不存在');

                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/restoreinstance', [
                    // 'name' => $row->uuid,
                    'id' => $id,
                    'name' => $instance->id,
                ])->Do(120)->getResponseBodyJson();
                if (isset($req['code']) == false) return $this->display(500, '超过最大尝试次数，无法连接到计算主机');
                if ($req['code'] == 200) {
                    writelog($row->uuid, date('Y-m-d H:i:s') . ' 还原备份');
                    return $this->display(200, 'Success');
                } else {
                    $error = isset($req['error']) ? $req['error'] : '未知错误';
                    writelog($row->uuid, date('Y-m-d H:i:s') . ' 还原备份失败：' . $error);
                    return $this->display(500, $req['msg']);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function restore_instance(Request $request){
        try {
            //code...
            $id = $request->input('id');
            if($id == '') return $this->display(500, '参数错误');

            $Backup = backup::where('uuid', $id)->where('type', 'instance')->where('userid', userrow('id'))->get();
            
            return view('acloud.backuplist',compact('Backup'));
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }


    // 更新虚拟机状态
    public function update_state(Request $request){
        try {
            // 查询是否有正在进行的jobs
            $jobs = \Illuminate\Support\Facades\DB::table('jobs')->count();
            if($jobs > 0){
                while(true){
                    sleep(1);
                    $jobs = \Illuminate\Support\Facades\DB::table('jobs')->count();
                    if($jobs == 0) break;
                    
                }
            }
            //code...
            $data = $request->get('data');
            $data = base64_decode($data);
            if($data == '') return $this->display(500, '参数错误');
            $data = json_decode($data, true);
            if(is_array($data) == false) return $this->display(500, '参数错误');
            foreach($data as $key => $value){
                $instance = Instance::where('id', $key)->first();
                if($instance){
                    if($value == 'running'){
                        $instance->state = 1;
                        $instance->save();
                    }else if($value == 'off'){
                        $instance->state = 2;
                        $instance->save();
                    }

                }
            }
            return $this->display(200, 'Success');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }
}
