<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Client;

class MakeInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // id
    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        //3
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $id = $this->id;
        $instance = \App\Instance::where('id', $id)->first();
        if (!$instance) {
            $this->delete();
            return;
        }
        if ($instance->state != 3) {
            $this->delete();
            return;
        }
        $flavor = \App\Flavor::where('id', $instance->flavor)->first();
        $image = \App\Image::where('id', $instance->image)->first();
        $network = \App\network::where('id', $instance->network)->first();
        $compute = \App\Compute::where('id', $instance->compute)->first();
        $arr = [
            'name' => $id,
            'vcpu' => $flavor->vcpu,
            'ram' => $flavor->ram,
            'image_path' => $image->path,
            'switchname' => $network->switchname,
            'trx' => $flavor->trx,
        ];

        // 动态内存
        if($flavor->type == 1){
            $arr['min'] = $flavor->min;
            $arr['max'] = $flavor->max;
        }
        if ($network->type == 'vlan') {
            $arr['vlanid'] = $network->vlan;
        }
        $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/createinstance', $arr)->Do(60)->getResponseBodyJson();
        if (isset($req['code']) == false) {
            $instance->state = 7;
            $instance->error = '超过最大尝试次数，无法连接到计算主机';
            $instance->save();
            $this->delete();
            return;
        }
        if ($req['code'] != 200) {
            $instance->state = 7;
            $instance->error = $req['msg'] . '：' . isset($req['error']) ? $req['error'] : '未知错误';
            $instance->save();
            $this->delete();
            return;
        }

        // 绑定端口安全
        if ($instance->portsafe == 1) {
            $ipaddr = $instance->ipaddr;
            // bind
            $req = (new Client)->Make('http://' . getrealhost($compute->hostname) . '/bindip', ['ipaddr' => $ipaddr, 'name' => $id])->Do(60)->getResponseBodyJson();
        }
        $instance->state = 2;
        $instance->save();
        $this->delete();
        return;
    }
}
