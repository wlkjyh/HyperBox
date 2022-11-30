<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Users, Compute, Flavor, Instance, network, Image, Volume, Client, backup};
use Illuminate\Foundation\Console\Presets\React;
use App\Jobs\{MakeInstance};

class BasicController extends Controller
{
    //
    public function Flavor(Request $request)
    {
        try {
            // $rows = Flavor::get();
            // return view('dashboard.flavor', ['rows' => $rows]);
            if(userrow('username') == 'admin'){
                $rows = Flavor::get();
                return view('dashboard.flavor', ['rows' => $rows]);
            }else{
                // 查询userid为当前用户的flavor或shared为1的flavor
                $rows = Flavor::where('userid', userrow('id'))->orWhere('share', 1)->get();
                return view('dashboard.flavor', ['rows' => $rows]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function create_api_flavor(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $vcpu = $request->input('vcpu');
            $ram = $request->input('ram');
            $type = $request->input('type');
            $min = $request->input('min');
            $max = $request->input('max');
            $trx = $request->input('trx', 0);
            $share = $request->input('share', 1);
            
            if (!$name || !$vcpu || !$ram) {
                return $this->display(400, '参数不能为空');
            }
            if($share != 1 && $share != 0){
                return $this->display(400, 'share参数错误');
            }
            if ($trx != 0) {
                // 必须是正整数
                if (!preg_match('/^[1-9]\d*$/', $trx)) {
                    return $this->display(400, 'TRX因子不合法');
                }
            }

            // vcpu, ram必须是整数
            if (!has_int($vcpu) || !has_int($ram)) {
                return $this->display(400, '类型不合法');
            }

            if ($type == 'true') {
                if (!$min || !$max) {
                    return $this->display(400, '参数不能为空');
                }
                if (!has_int($min) || !has_int($max)) {
                    return $this->display(400, '类型不合法');
                }
                if ($min > $max) {
                    return $this->display(400, '最小内存不能大于最大内存');
                }
            }
            $row = Flavor::where('name', $name)->first();
            if ($row) {
                return $this->display(400, '名称已存在');
            }
            $insert = [
                'id' => uuid(),
                'userid' => userrow('id'),
                'name' => $name,
                'vcpu' => $vcpu,
                'ram' => $ram,
                'trx' => $trx,
                'type' => ($type == 'true') ? 1 : 0,
                'share' => $share,
            ];
            if ($type == 'true') {
                $insert['min'] = $min;
                $insert['max'] = $max;
            }
            Flavor::create($insert);
            return $this->display(200, '创建成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }


    public function delete_api_flavor(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $row = Flavor::where('id', $id)->first();
            if (!$row) {
                return $this->display(400, '记录不存在');
            }
            // 判断是否有实例
            $has = Instance::where('flavor', $id)->first();
            if ($has) {
                return $this->display(400, '实例规格正在使用，不能删除');
            }
            $userrow = userrow('ALL');
            if ($userrow->username != 'admin') {
                if ($userrow->id != $row->userid) {
                    return $this->display(400, '没有权限');
                }
            }
            $row->delete();
            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function edit_flavor(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            // echo $id;
            $row = Flavor::where('id', $id)->first();
            if (!$row) {
                return $this->display(400, '记录不存在');
            }
            $userrow = userrow('ALL');
            if ($userrow->username != 'admin') {
                if ($userrow->id != $row->userid) {
                    return $this->display(400, '没有权限');
                }
            }
            return view('dashboard.flavor_edit', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function edit_api_flavor(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $vcpu = $request->input('vcpu');
            $ram = $request->input('ram');
            $min = $request->input('min');
            $max = $request->input('max');
            $trx = $request->input('trx', 0);
            $share = $request->input('share', 1);
            if (!$id || !$vcpu || !$ram) {
                return $this->display(400, '参数不能为空');
            }
            if($share != 1 && $share != 0){
                return $this->display(400, 'share参数错误');
            }
            if ($trx != 0) {
                // 必须是正整数
                if (!preg_match('/^[1-9]\d*$/', $trx)) {
                    return $this->display(400, 'TRX因子不合法');
                }
            }
            // vcpu, ram必须是整数
            if (!has_int($vcpu) || !has_int($ram)) {
                return $this->display(400, '类型不合法');
            }

            $row = Flavor::where('id', $id)->first();
            if (!$row) {
                return $this->display(400, '记录不存在');
            }

            if ($row->type == 1) {
                if (!$min || !$max) {
                    return $this->display(400, '参数不能为空');
                }
                if (!has_int($min) || !has_int($max)) {
                    return $this->display(400, '类型不合法');
                }
                if ($min > $max) {
                    return $this->display(400, '最小内存不能大于最大内存');
                }
            }
            $userrow = userrow('ALL');
            if ($userrow->username != 'admin') {
                if ($userrow->id != $row->userid) {
                    return $this->display(400, '没有权限');
                }
            }
            $row->vcpu = $vcpu;
            $row->ram = $ram;
            $row->share = $share;
            if ($row->type == 1) {
                $row->min = $min;
                $row->max = $max;
            }
            $row->trx = $trx;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function network(Request $request)
    {
        try {
            //code...
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
            return view('dashboard.network', ['rows' => $arr]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function Image(Request $request)
    {
        try {
            //code...
            $network = Image::get();
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
            return view('dashboard.Image', ['rows' => $arr]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function Volume(Request $request)
    {
        try {
            //code...
            $myVolume = Volume::where('userid', userrow('id'))->get();
            return view('dashboard.Volume', ['rows' => $myVolume]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function create_volume(Request $request)
    {
        try {
            $name = $request->input('name');
            $size = $request->input('size');
            $compute = $request->input('compute');
            if (!$name || !$size || !$compute) {
                return $this->display(400, '参数不能为空');
            }
            if (!has_int($size)) {
                return $this->display(400, '卷大小必须是有效的整数');
            }
            $compute = Compute::where('id', $compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }
            if ($compute->rule != 'ALL') {
                $k = json_decode($compute->rule, true);
                if (!in_array(userrow('id'), $k)) {
                    return $this->display(400, '没有权限');
                }
            }



            $total = $compute->disk;

            $used_volume = Volume::where('compute', $compute->id)->get();
            $used = 0;
            foreach ($used_volume as $val) {
                $used += $val->size;
            }
            if ($used + $size > $total) {
                return $this->display(400, '计算主机磁盘空间不足');
            }
            $uuid = uuid();
            Volume::create([
                'name' => $name,
                'size' => $size,
                'compute' => $compute->id,
                'id' => $uuid,
                'userid' => userrow('id'),
                'path' => 'null',
                'state' => 0,
            ]);

            // 开始创建卷
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/createvolume', [
                'name' => $uuid,
                'size' => $size,
            ], 'GET')->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) {
                Volume::where('id', $uuid)->delete();
                return $this->display(400, '创建卷失败');
            }
            if ($req['code'] != 200) {
                Volume::where('id', $uuid)->update([
                    'state' => 10,
                    'path' => $req['msg'] . '：' . $req['error'],
                ]);
                return $this->display(200, '创建卷事件已提交');
            } else {
                Volume::where('id', $uuid)->update([
                    'state' => 1,
                    'path' => $req['path'],
                ]);
                return $this->display(200, '创建卷事件已提交');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function VolumeDetail(Request $request, $id)
    {
        try {
            //code...
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            return view('dashboard.VolumeDetail', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function reset_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            if ($row->state != 10) {
                return $this->display(400, '卷未出错');
            }
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }

            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/createvolume', [
                'name' => $id,
                'size' => $row->size,
            ], 'GET')->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) {
                return $this->display(400, '重建卷失败');
            }
            if ($req['code'] != 200) {
                Volume::where('id', $id)->update([
                    'state' => 10,
                    'path' => $req['msg'] . '：' . $req['error'],
                ]);
                return $this->display(200, '重建卷失败');
            } else {
                Volume::where('id', $id)->update([
                    'state' => 1,
                    'path' => $req['path'],
                ]);
                return $this->display(200, '重建卷成功');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }
    public function delete_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            // 是否被连接
            if ($row->instance != '') return $this->display(400, '卷已被连接');
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                $row->delete();
                return $this->display(200, '卷删除成功');
            }
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/deletevolume', [
                'path' => $row->path,
            ], 'GET')->Do()->getResponseBodyJson();
            $row->delete();
            return $this->display(200, '卷删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function edit_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            return view('dashboard.edit_volume', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function resize_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            return view('dashboard.resize_volume', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }
    public function edit_api_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            $name = $request->input('name');
            if (!$name) {
                return $this->display(400, '卷名称不能为空');
            }
            // 查询卷名称是否存在
            $row2 = Volume::where('name', $name)->first();
            if ($row2 && $row2->id != $id) {
                return $this->display(400, '卷名称已存在');
            }
            $row->name = $name;
            $row->save();
            return $this->display(200, '卷修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function resize_api_volume(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Volume::where('id', $id)->where('userid', userrow('id'))->first();
            if (!$row) {
                return $this->display(400, '卷不存在');
            }
            $size = $request->input('size');
            if (!$size) {
                return $this->display(400, '卷大小不能为空');
            }
            if (!has_int($size)) {
                return $this->display(400, '卷大小必须为整数');
            }
            if ($size - $row->size < 0) {
                return $this->display(400, '卷大小必须大于原卷大小');
            }
            // 不能和原大小相同
            if ($size == $row->size) {
                return $this->display(400, '卷大小不能与原卷大小相同');
            }
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }

            $used = $size - $row->size;
            $rows = Volume::where('compute', $compute->id)->get();
            foreach ($rows as $val) {
                $used += $val->size;
            }
            if ($used > $compute->disk) return $this->display(400, '计算主机磁盘空间不足');

            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/resizevolume', [
                'path' => $row->path,
                'size' => $size,
            ], 'GET')->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) {
                return $this->display(400, '卷大小修改失败');
            }
            if ($req['code'] != 200) {
                return $this->display(400, $req['msg'] . '：' . $req['error']);
            } else {
                $row->size = $size;
                $row->save();
                return $this->display(200, '卷大小修改成功');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function instance(Request $request)
    {
        try {
            if (userrow('username') == 'admin') {
                $myInstance = Instance::get();
            } else {
                //code...
                $myInstance = Instance::where('userid', userrow('id'))->get();
            }
            return view('dashboard.instance', ['rows' => $myInstance]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function getResource(Request $request)
    {
        try {
            //code...
            $id = $request->input('compute');
            $compute = Compute::where('id', $id)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }
            if ($compute->rule != 'ALL') {
                $k = json_decode($compute->rule, true);
                if (!in_array(userrow('id'), $k)) {
                    return $this->display(400, '没有权限');
                }
            }
            // 获取镜像
            $myNetwork = network::myNetwork();
            $myImage = Image::myImage();
            $arr1 = [];
            foreach ($myImage as $key => $val) {
                if ($val->compute == $compute->id && strtolower($val->type) != 'iso' && $val->state == 1) {
                    $arr1[] = [
                        'id' => $val->id,
                        'name' => $val->name,
                    ];
                }
            }
            $arr2 = [];
            foreach ($myNetwork as $key => $val) {
                if ($val->compute == $compute->id) {
                    $arr2[] = [
                        'id' => $val->id,
                        'name' => $val->name,
                        'subnet' => $val->subnet,
                        'netmask' => netmasktoprefix($val->netmask),
                    ];
                }
            }
            return response()->json([
                'code' => 200,
                'msg' => '获取成功',
                'network' => $arr2,
                'image' => $arr1,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function create_instance(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $compute = $request->input('compute');
            $image = $request->input('image');
            $network = $request->input('network');
            $flavor = $request->input('flavor');
            $number = $request->input('number');
            $portsafe = $request->input('portsafe', 1);
            $userid = $request->input('userid', userrow('id'));
            if (!$name || !$compute || !$image || !$network || !$flavor || !$number) {
                return $this->display(400, '参数不能为空');
            }
            if (!has_int($number)) {
                return $this->display(400, '请填写正确的启动数量');
            }
            // 判断实例名称是否存在
            $rows = Instance::where('name', $name)->first();
            if ($rows) {
                return $this->display(400, '实例名称已存在');
            }

            $compute = Compute::where('id', $compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }
            if ($compute->rule != 'ALL') {
                $k = json_decode($compute->rule, true);
                if (!in_array(userrow('id'), $k)) {
                    return $this->display(400, '没有权限');
                }
            }
            $image = Image::where('id', $image)->first();
            if (!$image) {
                return $this->display(400, '镜像资源不存在');
            }
            if ($image->compute != $compute->id) {
                return $this->display(400, '镜像资源不属于该计算资源');
            }
            if ($image->rule != 'ALL') {
                $k = json_decode($image->rule, true);
                if (!in_array(userrow('id'), $k)) {
                    return $this->display(400, '没有权限');
                }
            }
            $network = Network::where('id', $network)->first();
            if (!$network) {
                return $this->display(400, '网络资源不存在');
            }
            if ($network->compute != $compute->id) {
                return $this->display(400, '网络资源不属于该计算资源');
            }
            if ($network->rule != 'ALL') {
                $k = json_decode($network->rule, true);
                if (!in_array(userrow('id'), $k)) {
                    return $this->display(400, '没有权限');
                }
            }
            $flavor = Flavor::where('id', $flavor)->first();
            if (!$flavor) {
                return $this->display(400, '实例规格不存在');
            }
            if ($image->vcpu > $flavor->vcpu) {
                return $this->display(400, '镜像资源不支持该规格1');
            }
            if ($image->ram > $flavor->ram) {
                return $this->display(400, '镜像资源不支持该规格2');
            }
            // 计算资源是否足够
            $vcpu_total = $compute->vcpu;
            $ram_total = $compute->ram;
            $allinstance = Instance::where('compute', $compute->id)->get();
            $vcpu_used = 0;
            $ram_used = 0;
            foreach ($allinstance as $key => $val) {
                $vcpu_used += $val->vcpu;
                $ram_used += $val->ram;
            }
            if ($vcpu_used + $flavor->vcpu * $number > $vcpu_total) {
                return $this->display(400, '计算主机VCPU资源不足');
            }
            $r = $flavor->ram;
            if (strstr($r, '-')) $r = explode('-', $r)[1];
            if ($ram_used + $r * $number > $ram_total) {
                return $this->display(400, '计算主机内存资源不足');
            }
            for ($i = 0; $i < $number; $i++) {
                $uuid = uuid();
                $num = $i + 1;
                if ($number == 1) $namespace = $name;
                else $namespace = $name . ' - ' . $num;
                $ipaddr = network::getipanduse($network->id);
                if (!$ipaddr) {
                    return $this->display(400, '网络资源不足');
                }
                Instance::create([
                    'name' => $namespace,
                    'id' => $uuid,
                    'compute' => $compute->id,
                    'image' => $image->id,
                    'network' => $network->id,
                    'ipaddr' => $ipaddr,
                    'flavor' => $flavor->id,
                    'state' => 3,
                    'userid' => $userid,
                    'portsafe' => ($portsafe == 1) ? $portsafe : 0,
                    'vid' => 'unknown',
                ]);
                // $this->dispatch(new MakeInstance($uuid));
                // 提交到队列
                MakeInstance::dispatch($uuid);

                writelog($uuid, date('Y-m-d H:i:s') . ' 创建实例');
            }
            return $this->display(200, '正在调度');
        } catch (\Throwable $th) {
            // 获取错误文件位置和行数
            $file = $th->getFile();
            $line = $th->getLine();

            //throw $th;
            return $this->display(500,'错误发生在'.$file.'第'.$line.'行'.$th->getMessage());
        }
    }

    public function getInstance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $rows = Instance::where('id', $id)->first();
            if (!$rows) {
                return $this->display(400, '实例不存在');
            }
            if ($rows->state == 7) {
                return $this->display(207, '创建实例出错', [
                    'error' => $rows->error,

                ]);
            }
            if ($rows->state == 20) return $this->display(20, '正在开机');
            if ($rows->state == 21) return $this->display(21, '正在重启');
            if ($rows->state == 22) return $this->display(22, '正在关机');
            if ($rows->state != 3) return $this->display(200, 'OK');
            return $this->display(100, 'no ok');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function delete_instance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }

            if (!$row) {
                return $this->display(400, '实例不存在');
            }
            if ($row->state == 3) return $this->display(400, '实例正在构建');
            // 判断有没有备份
            $backup = Backup::where('uuid', $id)->first();
            if ($backup) {
                return $this->display(400, '实例有备份，不能删除');
            }
            Volume::where('instance', $row->id)->update(['instance' => '']);
            if ($row->state == 7) {
                // 直接删除数据
                $row->delete();
                return $this->display(200, '删除成功');
            }
            if ($row->state == 1 || $row->state == 2) {
                $compute = Compute::where('id', $row->compute)->first();
                if (!$compute) {
                    $row->delete();
                    return $this->display(400, '计算资源不存在');
                }

                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/deleteinstance', [
                    'name' => $id
                ])->Do()->getResponseBodyJson();
                $row->delete();
                return $this->display(200, '删除成功');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function edit_api_instance(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) {
                return $this->display(400, '实例不存在');
            }
            $name = $request->input('name');
            if (!$name) return $this->display(400, '实例名称不能为空');
            // 判断名称是否重复
            $rows = Instance::where('name', $row->name)->first();
            if ($rows && $rows->id != $row->id) {
                return $this->display(400, '实例名称已存在');
            }
            $row->name = $name;
            $row->save();

            writelog($id, date('Y-m-d H:i:s') . ' 修改实例名称');
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function start_instance(Request $request)
    {
        try {
            //code...
            $id = $request->get('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }

            if (!$row) {
                return $this->display(400, '实例不存在');
            }
            if ($row->state != 1 && $row->state != 2) {
                return $this->display(400, '实例状态错误');
            }
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }
            $this->dispatch(new \App\Jobs\Instance('start', $id));
            $row->state = 20;
            $row->save();
            return $this->display(200, '事件已提交');
            // $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/startinstance', [
            //     'name' => $id
            // ])->Do()->getResponseBodyJson();
            // if (isset($req['code']) == false) return $this->display(400, '超过最大尝试次数，无法连接到计算主机');
            // if ($req['code'] != 200) {
            //     return $this->display(400, $req['msg']);
            // }
            // // 判断是否有vid
            // if ($row->vid == 'unknown') {
            //     $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/getinstancevid', [
            //         'name' => $id
            //     ])->Do()->getResponseBodyJson();
            //     if (isset($req['code']) && $req['code'] == 200) {
            //         $row->vid = $req['vid'];
            //         // $row->save();
            //     }
            // }
            // $row->state = 1;
            // $row->save();

            // writelog($id, date('Y-m-d H:i:s') . ' 启动实例');
            // return $this->display(200, '启动成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function stop_instance(Request $request)
    {
        try {
            //code...
            $id = $request->get('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) {
                return $this->display(400, '实例不存在');
            }
            if ($row->state != 1 && $row->state != 2) {
                return $this->display(400, '实例状态错误');
            }
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }
            $row->state = 22;
            $row->save();
            $this->dispatch(new \App\Jobs\Instance('poweroff', $id));
            return $this->display(200, '事件已提交');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function restart_instance(Request $request)
    {
        try {
            //code...
            $id = $request->get('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $userrow = userrow();
            if ($userrow->username != 'admin') {
                $row = Instance::where('id', $id)->where('userid', userrow('id'))->first();
            } else {
                $row = Instance::where('id', $id)->first();
            }
            if (!$row) {
                return $this->display(400, '实例不存在');
            }
            if ($row->state != 1 && $row->state != 2) {
                return $this->display(400, '实例状态错误');
            }
            $compute = Compute::where('id', $row->compute)->first();
            if (!$compute) {
                return $this->display(400, '计算资源不存在');
            }

            $row->state = 21;
            $row->save();
            $this->dispatch(new \App\Jobs\Instance('reboot', $id));
            return $this->display(200, '事件已提交');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function backup(Request $request)
    {
        try {
            //code...
            $myBackup = backup::get();
            return view('dashboard.backup', [
                'rows' => $myBackup
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }
}
