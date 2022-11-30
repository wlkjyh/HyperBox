<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Users, Compute, Flavor, network, Instance, Client, Image, GooleAuth, QRcode, Config, Route};
use Faker\Provider\Base;
use Hamcrest\Core\Set;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class AdminController extends Controller
{
    //计算资源
    public function compute(Request $request)
    {
        try {
            //code...
            return view('dashboard.admin.compute', ['rows' => Compute::get()]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 添加一个计算主机
     */
    public function create_compute(Request $request)
    {
        try {
            //code...
            $hostname = $request->input('hostname');
            $vcpu = $request->input('vcpu');
            $ram = $request->input('ram');
            $disk = $request->input('disk');
            $console = $request->input('console');
            if ($console == '') {
                return $this->display(500,'FreeRDP远程地址不能为空！');
            }
            if (!$hostname || !$vcpu || !$ram || !$disk) {
                return $this->display(400, '参数不能为空');
            }
            // vcpu, ram, disk必须是整数
            if (!has_int($vcpu) || !has_int($ram) || !has_int($disk)) {
                return $this->display(400, '类型不合法');
            }
            // 判断主机名是否存在
            $compute = Compute::where('hostname', $hostname)->first();
            if ($compute) {
                return $this->display(400, '主机名已存在');
            }
            // 判断compute是否有端口
            if (strstr($hostname, ':')) {
                $host = &$hostname;
            } else {
                $host = $hostname . ':3000';
            }
            $req = (new Client)->Make('http://' . $host . '/connect', [], 'GET')->Do()->getResponseBodyJson();
            if (isset($req['code']) == false) return $this->display(400, '无法连接到主机');
            if ($req['code'] != 200) return $this->display(400, $req['msg']);

            Compute::create([
                'id' => uuid(),
                'hostname' => $hostname,
                'vcpu' => $vcpu,
                'ram' => $ram,
                'disk' => $disk,
                'rule' => 'ALL',
                'console' => $console,
            ]);
            return $this->display(200, '创建成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 编辑计算主机
     */
    public function edit_api_compute(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $vcpu = $request->input('vcpu');
            $ram = $request->input('ram');
            $disk = $request->input('disk');
            $console = $request->input('console');
            if (!$console) return $this->display(400, 'FreeRDP远程地址不能为空！');
            if (!$id || !$vcpu || !$ram || !$disk) {
                return $this->display(400, '参数不能为空');
            }
            $row = Compute::where('id', $id)->first();
            if (!$row) {
                return $this->display(400, '记录不存在');
            }
            // vcpu, ram, disk必须是整数
            if (!has_int($vcpu) || !has_int($ram) || !has_int($disk)) {
                return $this->display(400, '类型不合法');
            }
            // 修改
            $row->vcpu = $vcpu;
            $row->ram = $ram;
            $row->console = $console;
            $row->disk = $disk;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }


    // 用户组
    public function group(Request $request)
    {
        try {
            //code...
            $group = \App\Group::get();
            return view('dashboard.admin.group', ['rows' => $group]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }


    public function create_group(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $description = $request->input('description');
            if (!$name || !$description) {
                return $this->display(400, '参数不能为空');
            }
            $group = \App\Group::where('name', $name)->first();
            if ($group) {
                return $this->display(400, '用户组已存在');
            }
            \App\Group::create([
                'id' => uuid(),
                'name' => $name,
                'description' => $description
            ]);
            return $this->display(200, '创建成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function delete_group(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $group = \App\Group::where('id', $id)->first();
            if (!$group) {
                return $this->display(400, '用户组不存在');
            }
            $user = Users::where('group', $id)->first();
            if ($user) {
                return $this->display(400, '用户组下有用户，不能删除');
            }
            if ($group->name == 'default') {
                return $this->display(400, '默认用户组不能删除');
            }
            $group->delete();
            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }


    public function edit_api_group(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $name = $request->input('name');
            $description = $request->input('description', '-');
            if (!$id || !$name) {
                return $this->display(400, '参数不能为空');
            }
            $group = \App\Group::where('id', $id)->first();
            if (!$group) {
                return $this->display(400, '用户组不存在');
            }
            $namerow = \App\Group::where('name', $name)->first();
            if ($namerow && $namerow->id != $id) {
                return $this->display(400, '用户组名已存在');
            }
            if ($group->name == 'default') {
                return $this->display(400, '默认用户组不能修改');
            }
            $group->name = $name;
            $group->description = $description;
            $group->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function group_member(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) {
                return $this->display(400, '参数不能为空');
            }
            $group = \App\Group::where('id', $id)->first();
            if (!$group) {
                return $this->display(400, '用户组不存在');
            }
            $myUser = Users::where('group', $id)->get();
            // noGroup
            $noGroup = Users::where('group', null)->get();
            return view('dashboard.admin.group_member', ['rows' => $myUser, 'rows2' => $noGroup, 'group' => $group]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function remove_group(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $user = Users::where('id', $id)->first();
            if (!$user) {
                return $this->display(400, '用户不存在');
            }
            $user->group = null;
            $user->save();
            return $this->display(200, '移除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function add_group(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $group = $request->input('group');
            if (!$id || !$group) {
                return $this->display(400, '参数不能为空');
            }
            $user = Users::where('id', $id)->first();
            if (!$user) {
                return $this->display(400, '用户不存在');
            }
            $group = \App\Group::where('id', $group)->first();
            if (!$group) {
                return $this->display(400, '用户组不存在');
            }
            $user->group = $group->id;
            $user->save();
            return $this->display(200, '添加成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function edit_compute(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Compute::where('id', $id)->first();
            if (!$row) return redirect('/home/dashboard/admin/compute');
            return view('dashboard.admin.edit_compute', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th);
        }
    }


    public function delete_api_compute(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = Compute::where('id', $id)->first();
            if (!$row) return $this->display(400, '记录不存在');
            $row->delete();
            // 删除相关实例
            Instance::where('compute', $id)->delete();
            network::where('compute', $id)->delete();
            Image::where('compute', $id)->delete();


            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function Flavor(Request $request)
    {
        try {
            $userrow = userrow();
            //code...
            $rows = Flavor::get();
            return view('dashboard.flavor', ['rows' => $rows]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function network(Request $request)
    {
        try {
            //code...
            return view('dashboard.admin.network', ['rows' => network::get()]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function import_user(Request $request)
    {
        try {
            $csv = $request->file('csv');
            if (!$csv) {
                return $this->display(400, '请选择文件');
            }
            $ext = $csv->getClientOriginalExtension();
            if ($ext != 'csv') {
                return $this->display(400, '文件格式错误');
            }
            // 获取内容
            $content = $csv->get();
            // 转码
            // $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            // 转换为utf-8
            // $content = iconv('GBK', 'UTF-8', $content);
            // 分割
            $content = explode("\n", $content);
            // exit(print_r($content));
            foreach ($content as $key => $value) {
                // 如果最后一个字符是,则去掉
                if (substr($value, -1) == ',') {
                    $value = substr($value, 0, -1);
                }
                $arr = explode(',', $value);
                // print_r($arr);
                if (count($arr) < 4) {
                    continue;
                }
                $username = $arr[0];
                $password = $arr[1];
                $email = $arr[2];
                $group = $arr[3];
                $userrow = Users::where('username', $username)->first();
                if ($userrow) {
                    continue;
                }
                $emailrow = Users::where('email', $email)->first();
                if ($emailrow) {
                    continue;
                }

                if ($group != '-') {
                    $grouprow = \App\Group::where('name', $group)->first();
                    if (!$grouprow) {
                        $group = null;
                    } else {
                        $group = $grouprow->id;
                    }
                } else {
                    $group = null;
                }
                Users::create([
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'email' => $email,
                    'group' => $group,
                ]);
                // echo $username . ',' . $password . ',' . $email . ',' . $group . "\n";
            }
            return $this->display(200, '导入成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function create_network(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $compute = $request->input('compute');
            $type = $request->input('type');
            $switchname = $request->input('switchname');
            $vlanid = $request->input('vlanid');
            $subnet = $request->input('subnet');
            $netmask = $request->input('netmask');
            $gateway = $request->input('gateway');
            $dns = $request->input('dns');
            $dhcp = $request->input('dhcp');
            $list = $request->input('list');
            if (!$name ||  !$compute || !$type || !$switchname || !$subnet || !$netmask || !$gateway || !$dns || !$dhcp || !$list) {
                return $this->display(400, '参数不能为空');
            }
            if ($dhcp == 'true') {
                $dhcp = 1;
            } else {
                $dhcp = 0;
            }
            if ($type != 'physics' && $type != 'vlan') {
                return $this->display(400, '类型不合法');
            }

            if ($type == 'vlan') {
                if ($vlanid == '') {
                    return $this->display(400, 'vlanid不能为空');
                }
                if (!has_int($vlanid)) {
                    return $this->display(400, 'vlanid不合法');
                }
            } else {
                $vlanid = 0;
            }
            //subnet、netmask、gateway、dns必须是ip地址
            if (!has_ip($subnet) || !has_ip($netmask) || !has_ip($gateway) || !has_ip($dns)) {
                return $this->display(400, 'SUBNET、NETMASK、GATEWAY、DNS不是ip地址');
            }
            $ipa = $list;
            //list必须是有效的范围池
            $list = explode('-', $list);
            if (count($list) != 2) {
                return $this->display(400, '范围池不合法');
            }
            if (!has_ip($list[0]) || !has_ip($list[1])) {
                return $this->display(400, '范围池不合法');
            }
            $list[0] = ip2long($list[0]);
            $list[1] = ip2long($list[1]);
            if ($list[0] > $list[1]) {
                return $this->display(400, '范围池不合法');
            }
            $row = network::where('name', $name)->first();
            if ($row) {
                return $this->display(400, '名称已存在');
            }
            $row = Compute::where('id', $compute)->first();
            if (!$row) {
                return $this->display(400, '计算节点不存在');
            }

            $iptable = [];
            $arr = explode("\n", $ipa);
            $ip = [];
            foreach ($arr as $val) {
                if (strstr($val, '-')) {
                    $k = explode('-', $val);
                    // echo $k[0];
                    if (filter_var($k[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($k[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        //get start ip
                        $k1 = explode('.', $k[0]);
                        $start = end($k1);
                        $k2 = explode('.', $k[1]);
                        $end = end($k2) + 1;
                        for ($i = $start; $i < $end; $i++) {
                            $ipa = $k1[0] . '.' . $k1[1] . '.' . $k1[2] . '.' . $i;
                            if (isset($iptable[$ipa]) == false || $iptable[$ipa] == 'true') $ip[$ipa] = 'true';
                            else  $ip[$ipa] = 'false';
                        }
                        // print_r($ip);
                    }
                }
            }
            // exit(json_encode($ip));


            network::create([
                'id' => uuid(),
                'name' => $name,
                'compute' => $compute,
                'type' => $type,
                'switchname' => $switchname,
                'vlan' => $vlanid,
                'subnet' => $subnet,
                'netmask' => $netmask,
                'gateway' => $gateway,
                'dns' => $dns,
                'dhcp' => $dhcp,
                'list' => $ipa,
                'ippool' => json_encode($ip),
                'rule' => 'ALL',
            ]);
            return $this->display(200, '创建成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function delete_network(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $row = network::where('id', $id)->first();
            if (!$row) return $this->display(400, '记录不存在');
            // 判断是否有实例在使用
            $rows = Instance::where('network', $id)->first();
            if ($rows) {
                return $this->throwable('无法删除网络，因为网络中有实例在使用');
            }
            $row->delete();
            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th);
        }
    }

    public function edit_network(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = network::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            return view('dashboard.admin.edit_network', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function edit_api_network(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = network::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            $name = $request->input('name');
            $dhcp = $request->input('dhcp');
            $dns = $request->input('dns');
            if (!$name || !$dhcp || !$dns) {
                return $this->throwable('参数不能为空');
            }
            if ($dhcp == 'true') {
                $dhcp = 1;
            } else {
                $dhcp = 0;
            }
            if (!has_ip($dns)) {
                return $this->throwable('DNS不合法');
            }
            // 判断名称是否重复
            $row = network::where('name', $name)->first();
            if ($row && $row->id != $id) {
                return $this->throwable('网络名称已存在');
            }
            $row->name = $name;
            $row->dhcp = $dhcp;
            $row->dns = $dns;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function rule_network(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = network::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            return view('dashboard.admin.rule_network', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function rule_api_network(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->display(500, '参数不能为空');
            $row = network::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $rule = $request->input('rule');
            if (!$rule) {
                return $this->display(500, '参数不能为空');
            }
            if ($rule != 'ALL' && json_decode($rule, true) === null) {
                return $this->display(500, '访问权错误');
            }
            $row->rule = $rule;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function rule_compute(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = compute::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            return view('dashboard.admin.rule_compute', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function rule_api_compute(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->display(500, '参数不能为空');
            $row = compute::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $rule = $request->input('rule');
            if (!$rule) {
                return $this->display(500, '参数不能为空');
            }
            if ($rule != 'ALL' && json_decode($rule, true) === null) {
                return $this->display(500, '访问权错误');
            }
            $row->rule = $rule;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function image(Request $request)
    {
        try {
            //code...
            $row = image::get();
            return view('dashboard.admin.image', ['rows' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function create_image(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $compute = $request->input('compute');
            $path = $request->input('path');
            $vcpu = $request->input('vcpu');
            $ram = $request->input('ram');
            $local = $request->input('local');
            $type = $request->input('type');
            if ($name == '' || $compute == '' || $path == '' || $vcpu == '' || $ram == '' || $local == '') {
                return $this->throwable('参数不能为空');
            }
            $allow = ['vhdx', 'iso', 'vhd'];
            if (!in_array($type, $allow)) {
                return $this->throwable('文件类型错误');
            }

            if ($local == '2' && strstr($path, 'http') == false) {
                return $this->display(500, '请输入有效的网络位置');
            }
            // vcpu和ram必须是整数
            if (!has_int($vcpu) || !has_int($ram)) {
                return $this->display(500, 'RAM和VCPU不合法');
            }
            $row = image::where('name', $name)->first();
            if ($row) {
                return $this->display(500, '映像名称已存在');
            }
            // 判断compute是否存在
            $row = compute::where('id', $compute)->first();
            if (!$row) {
                return $this->display(500, '计算节点不存在');
            }
            if ($local == '1') {
                Image::create([
                    'name' => $name,
                    'compute' => $compute,
                    'path' => $path,
                    'vcpu' => $vcpu,
                    'ram' => $ram,
                    'type' => $type,
                    'state' => 1,
                    'rule' => 'ALL',
                ]);
                return $this->display(200, '创建成功');
            } else {
                $uuid = uuid();
                $req = (new Client)->Make('http://' . getrealhost($row->hostname) . '/downimage', [
                    'downurl' => $path,
                    'id' => $uuid,
                    'ext' => strtolower($type),
                    'callback' => 'http://' . $_SERVER['HTTP_HOST'] . '/callback/downok/' . $uuid,
                ], 'GET')->Do()->getResponseBodyJson();
                if (isset($req['code']) == false) return $this->display(500, '无法连接计算节点');
                if ($req['code'] != 200) return $this->display(500, $req['msg']);
                Image::create([
                    'name' => $name,
                    'compute' => $compute,
                    'path' => $req['path'],
                    'vcpu' => $vcpu,
                    'ram' => $ram,
                    'type' => $type,
                    'state' => 2,
                    'rule' => 'ALL',
                    'id' => $uuid,
                ]);
                return $this->display(200, '已提交至计算主机，请等待计算主机下载完成');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function downok(Request $request, $id)
    {
        try {
            //code...
            $row = image::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $row->state = 1;
            $row->save();
            return $this->display(200, '下载完成');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function delete_image(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = image::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            $compute = compute::where('id', $row->compute)->first();
            if (!$compute) return $this->throwable('计算节点不存在');
            if ($row->state == 2) return $this->display(500, '请等待计算主机下载完成');
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/deleteimage', [
                'path' => $row->path,
            ], 'GET')->Do()->getResponseBodyJson();

            // 判断是否有实例
            $row = instance::where('image', $id)->first();
            if ($row) {
                return $this->display(500, '该映像已被实例使用，无法删除');
            }
            $row->delete();
            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function getimagestatus(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = image::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            if ($row->state == 1) return $this->display(200, 'OK');
            return $this->display(500, '下载中');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function edit_image(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = image::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            return view('dashboard.admin.edit_image', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function edit_api_image(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $name = $request->input('name');
            $ram = $request->input('ram');
            $vcpu = $request->input('vcpu');
            if ($id == '' || $name == '' || $ram == '' || $vcpu == '') {
                return $this->display(500, '参数不能为空');
            }
            $row = image::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            // 判断name是否存在
            $row2 = image::where('name', $name)->first();
            if ($row2 && $row2->id != $id) return $this->display(500, '映像名称已存在');
            if (!has_int($ram)) return $this->display(500, '内存错误');
            if (!has_int($vcpu)) return $this->display(500, 'VCPU错误');
            $row->name = $name;
            $row->ram = $ram;
            $row->vcpu = $vcpu;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function rule_image(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = image::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            return view('dashboard.admin.rule_image', ['row' => $row]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function rule_api_image(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->display(500, '参数不能为空');
            $row = image::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $rule = $request->input('rule');
            if (!$rule) {
                return $this->display(500, '参数不能为空');
            }
            if ($rule != 'ALL' && json_decode($rule, true) === null) {
                return $this->display(500, '访问权错误');
            }
            $row->rule = $rule;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function user(Request $request)
    {
        try {
            //code...
            $rows = Users::get();
            return view('dashboard.admin.user', ['rows' => $rows]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function delete_user(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->throwable('参数不能为空');
            $row = Users::where('id', $id)->first();
            if (!$row) return $this->throwable('记录不存在');
            $protected = [
                'admin'
            ];
            if (in_array($row->name, $protected)) return $this->display(500, '该用户收到保护，不能删除');
            $row->delete();
            return $this->display(200, '删除成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function repwd_user(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->display(500, '参数不能为空');
            $row = Users::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $newpwd = $request->input('newpwd');
            $confirmpwd = $request->input('confirmpwd');
            if (!$newpwd || !$confirmpwd) return $this->display(500, '参数不能为空');
            if ($newpwd != $confirmpwd) return $this->display(500, '两次密码不一致');
            $row->password = password_hash($newpwd, PASSWORD_DEFAULT);
            $row->token = '';
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function create_user(Request $request)
    {
        try {
            //code...
            $username = $request->input('username');
            $password = $request->input('password');
            $email = $request->input('email');
            $group = $request->input('group');
            if (!$username || !$password || !$email) return $this->display(500, '参数不能为空');
            $row = Users::where('username', $username)->first();
            if ($row) return $this->display(500, '用户名已存在');
            $row = Users::where('email', $email)->first();
            if ($row) return $this->display(500, '邮箱已存在');
            // 判断是不是有效的邮箱
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->display(500, '邮箱格式错误');
            // 密码必须大于6位
            if (strlen($password) < 6) return $this->display(500, '密码必须大于6位');
            Users::create([
                'id' => uuid(),
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'email' => $email,
                'group' => $group,
            ]);
            return $this->display(200, '创建成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function remail_user(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $email = $request->input('email');
            if (!$id || !$email) return $this->display(500, '参数不能为空');
            $row = Users::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            // 判断email是不是有效的邮箱
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->display(500, '邮箱格式错误');
            // 判断是否存在
            $row2 = Users::where('email', $email)->first();
            if ($row2 && $row2->id != $id) return $this->display(500, '邮箱已存在');
            $row->email = $email;
            $row->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }

    public function System(Request $request)
    {
        try {
            //code...
            $timezone = getconfig('timezone');
            $network = getconfig('networktop');
            $freeoauth = getconfig('freeoauth');
            $localauto = getconfig('localauto');
            // $method = getconfig('method');

            return view('dashboard.admin.system', ['timezone' => $timezone, 'networktop' => $network, 'freeoauth' => $freeoauth, 'localauto' => $localauto]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function systemconfig(Request $request)
    {
        try {
            //code...
            $timezone = $request->input('timezone');
            $networktop = $request->input('networktop');
            $freeoauth = $request->input('freeoauth', 'disable');
            $localauto = $request->input('localauto', 'disable');
            if ($freeoauth != 'enable') $freeoauth = 'disable';
            if ($localauto != 'enable') $localauto = 'disable';
            // $method = $request->input('method');
            if (!$timezone) return $this->display(500, '参数不能为空');
            if (!$networktop) return $this->display(500, '参数不能为空');
            // if (!$method) return $this->display(500, '参数不能为空');
            setconfig('timezone', $timezone);
            setconfig('networktop', $networktop);
            setconfig('freeoauth', $freeoauth);
            setconfig('localauto', $localauto);
            // setconfig('method', $method);
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->display(500, $th->getMessage());
        }
    }


    // 许可证
    public function license(Request $request)
    {
        try {
        } catch (\Throwable $th) {
            return $this->throwable($th->getMessage());
        }
    }

    // 访问安全
    public function security(Request $request)
    {
        try {

            return view('dashboard.admin.security');
        } catch (\Throwable $th) {
            return $this->throwable($th->getMessage());
        }
    }

    public function security_api(Request $request)
    {
        try {
            //code...
            $basicauth = $request->input('basicauth', '0');
            $authuser = $request->input('authuser');
            $authpass = $request->input('authpass');
            $googleauth = $request->input('googleauth', '0');
            if ($googleauth == '1') {
                if (getconfig('googleauthsecret') == '') return $this->display(500, '你需要先绑定Secret');
                setconfig('googleauth', '1');
            } else {
                setconfig('googleauth', '0');
            }
            if ($basicauth == '1') {
                if (getconfig('authpass') == '' && $authpass == '') return $this->display(500, '你必须要设置一个认证密码');
                if (!$authuser) return $this->display(500, '参数不能为空');
                setconfig('basicauth', '1');
                setconfig('authuser', $authuser);
                if ($authpass != '') setconfig('authpass', $authpass);
            } else {
                setconfig('basicauth', '0');
            }

            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function bindGooleAuth(Request $request)
    {
        try {
            //code...
            $GooleAuth = new GooleAuth();
            $secret = $GooleAuth->createSecret();
            $content = 'otpauth://totp/dreamstack?secret=' . $secret . '';
            setconfig('googleauthsecret', $secret);
            return view('dashboard.admin.bindgoogleauth', ['qrcode' => '/getQrcode?value=' . $content, 'secret' => $secret]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }
    public function configoauth(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if (!$id) return $this->display(500, '参数不能为空');
            $row = Users::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            $freeoauth = Config::where('k', 'freeoauth')->first();
            $freeoauth = $freeoauth->v;
            $freeoauth = json_decode($freeoauth, true);
            $auth = [];
            foreach ($freeoauth as $key => $val) {
                if ($val == $row->id) $auth[] = $key;
            }
            // 使用,拼接
            $auth = implode(',', $auth);
            // 去掉最后一个逗号
            $auth = rtrim($auth, ',');
            return view('dashboard.admin.configoauth', ['row' => $row, 'auth' => $auth]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function configauth(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $auth = $request->input('auth');
            if (!$id) return $this->display(500, '参数不能为空');
            $user = Users::where('id', $id)->first();
            if (!$user) return $this->display(500, '记录不存在');
            $freeoauth = Config::where('k', 'freeoauth')->first();
            $list = json_decode($freeoauth->v, true);
            foreach ($list as $key => $val) {
                if ($val == $id) unset($list[$key]);
            }
            $arr = explode(',', $auth);
            foreach ($arr as $val) {
                if (!$val) continue;
                $val = strtolower($val);
                // 判断是不是有效的mac
                if (!preg_match('/^[a-fA-F0-9]{2}-[a-fA-F0-9]{2}-[a-fA-F0-9]{2}-[a-fA-F0-9]{2}-[a-fA-F0-9]{2}-[a-fA-F0-9]{2}$/', $val)) return $this->display(500, $val . '不是有效的MAC');
                if (isset($list[$val])) return $this->display(500, $val . '发生重复');

                $list[$val] = $id;
            }
            // 去重和空白
            $list = array_unique($list);
            $list = array_filter($list);
            $freeoauth->v = json_encode($list);
            $freeoauth->save();
            return $this->display(200, '修改成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function tasks(Request $request)
    {
        try {
            //code...
            return view('dashboard.admin.tasks');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function Route(Request $Request)
    {
        try {
            //code...
            $rows = Route::get();
            return view('dashboard.admin.route', ['rows' => $rows]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }

    public function create_route(Request $request)
    {
        try {
            //code...
            $name = $request->input('name');
            $network = $request->input('network');
            if (!$name) return $this->display(500, '参数不能为空');
            $row = Route::where('name', $name)->first();
            if ($row) return $this->display(500, '路由器名称已存在');
            $network = network::where('id', $network)->first();
            if (!$network) return $this->display(500, '网络不存在');

            // 一个网络只能有一个路由器
            $R = Route::where('network', $network->id)->first();
            if ($R) return $this->display(500, '网络已存在路由器');
            
            Route::create([
                'name' => $name,
                'network' => $network->id,
                'route' => '[]',
            ]);
            return $this->display(200, '添加成功');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }


    public function route_manage(Request $request,$id){
        try {
            //code...
            $row = Route::where('id', $id)->first();
            if (!$row) return $this->display(500, '记录不存在');
            return view('dashboard.admin.route_manage', ['row' => json_decode($row->route, true),'id'=>$id]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->throwable($th->getMessage());
        }
    }
}
