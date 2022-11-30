<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Instance as InstanceModel;
use App\{Compute, Client};


class Instance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type, $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $id)
    {
        //
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $row = InstanceModel::where('id', $this->id)->first();
        if (!$row) $this->delete();
        $compute = Compute::where('id', $row->compute)->first();
        if (!$compute) $this->delete();

        //
        switch ($this->type) {
                // 开机
            case 'start':
                if ($row->state != 20) {
                    $this->delete();
                    return;
                }
                // 发送开机命令
                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/startinstance', [
                    'name' => $this->id,
                ])->Do(120)->getResponseBodyJson();
                if (isset($req['code']) == false) {
                    writelog($this->id, date('Y-m-d H:i:s').' 目标计算机积极拒绝了连接。');
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                if ($req['code'] != 200) {
                    writelog($this->id, date('Y-m-d H:i:s').' 启动实例时发送错误：' . $req['msg']);
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                // 判断是否有vid
                if ($row->vid == 'unknown') {
                    $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/getinstancevid', [
                        'name' => $this->id,
                    ])->Do(120)->getResponseBodyJson();
                    if (isset($req['code']) && $req['code'] == 200) {
                        $row->vid = $req['vid'];
                    }
                }
                writelog($this->id, date('Y-m-d H:i:s').' 启动实例成功。');
                $row->state = 1;
                $row->save();
                $this->delete();
                return;
                break;
                // 关机
            case 'poweroff':
                if ($row->state != 22) {
                    $this->delete();
                    return;
                }
                // 发送开机命令
                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/stopinstance', [
                    'name' => $this->id,
                ])->Do(120)->getResponseBodyJson();
                if (isset($req['code']) == false) {
                    writelog($this->id, date('Y-m-d H:i:s').' 目标计算机积极拒绝了连接。');
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                if ($req['code'] != 200) {
                    writelog($this->id, date('Y-m-d H:i:s').' 关闭实例时发送错误：' . $req['msg']);
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                writelog($this->id, date('Y-m-d H:i:s').' 关闭实例成功。');
                $row->state = 2;
                $row->save();
                $this->delete();
                return;
                break;
                // 重启
            case 'reboot':
                if ($row->state != 21) {
                    $this->delete();
                    return;
                }
                // 发送开机命令
                $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/restartinstance', [
                    'name' => $this->id,
                ])->Do(120)->getResponseBodyJson();
                if (isset($req['code']) == false) {
                    writelog($this->id, date('Y-m-d H:i:s').' 目标计算机积极拒绝了连接。');
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                if ($req['code'] != 200) {
                    writelog($this->id, date('Y-m-d H:i:s').' 重启实例时发送错误：' . $req['msg']);
                    $row->state = 2;
                    $row->save();
                    $this->delete();
                    return;
                }
                // 判断是否有vid
                if ($row->vid == 'unknown') {
                    $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/getinstancevid', [
                        'name' => $this->id,
                    ])->Do(120)->getResponseBodyJson();
                    if (isset($req['code']) && $req['code'] == 200) {
                        $row->vid = $req['vid'];
                    }
                }
                writelog($this->id, date('Y-m-d H:i:s').' 重启实例成功.');
                $row->state = 1;
                $row->save();
                $this->delete();
                return;
                break;
                // 删除
            case 'delete':

                break;

            default:
                $this->delete();
                break;
        }
    }
}
