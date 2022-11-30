from django.shortcuts import render,HttpResponse
import json,configparser,os
# Create your views here.
def createInstance(request):
    vcpu = request.GET.get('vcpu')
    ram = request.GET.get('ram')
    min = request.GET.get('min')
    max = request.GET.get('max')
    image_path = request.GET.get('image_path')
    switchname = request.GET.get('switchname')
    name = request.GET.get('name')
    vlanid = request.GET.get('vlanid')
    rtx = request.GET.get('trx')
    if rtx == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))


    if vcpu == None or ram == None or image_path == None or switchname == None or name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    
    # 查询虚拟交换机
    ps_script = 'Get-VMSwitch -Name "{}"'\
        .format(switchname)
    ps_result = os.popen('powershell.exe -command ' + ps_script).read()
    # 判断是否包含InvalidParameter
    if 'InvalidParameter' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'虚拟交换机不存在','error':ps_result}))
    
    # 判断image_path是否存在
    if not os.path.exists(image_path):
        return HttpResponse(json.dumps({'code':400,'msg':'映像文件不存在'}))

    
    # 使用image_path创建父盘
    config = configparser.ConfigParser()
    config.read('Compute.conf',encoding='utf-8')
    instance_path = config.get('storage','instance')
    fileto = instance_path + '/' + name + '.vhdx'
    run_ps = 'New-VHD -ParentPath ' + image_path + ' -Path ' + fileto +' -Differencing'
    run_result = os.popen('powershell.exe -command ' + run_ps).read()
    if not os.path.exists(fileto):
        return HttpResponse(json.dumps({'code':400,'msg':'创建实例硬盘失败','error':run_result}))


    run_ps = 'New-VM -Name "' + name + '" -MemoryStartupBytes ' + ram +  'MB -VHDPath '+fileto+' -Generation 1 -SwitchName ' + switchname
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'MemoryAssigned' not in ps_result:
        os.remove(fileto)
        return HttpResponse(json.dumps({'code':400,'msg':'创建实例失败','error':ps_result}))

    # 设置cpu
    run_ps = 'Set-vm -ProcessorCount ' + vcpu + ' -Name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()


    # 设置动态内存
    if min != None and max != None:
        ps_run = 'Set-VMMemory ' + name + ' -DynamicMemoryEnabled $true -MinimumBytes ' + min + 'MB -StartupBytes ' + ram + 'MB -MaximumBytes ' + max + 'MB -Priority 50 -Buffer 25'
        ps_result = os.popen('powershell.exe -command ' + ps_run).read()
    else:
        ps_run = 'Set-VMMemory ' + name + ' -DynamicMemoryEnabled $false'
        ps_result = os.popen('powershell.exe -command ' + ps_run).read()


    # 判断是否有vlan
    if vlanid != None:
        run_ps = 'Set-VMNetworkAdapterVlan -VMName ' + name + ' -Access -VlanId' + vlanid
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # 设置rtx因子
    if not rtx.isdigit():
        rtx = '0'

    if rtx != '0':
        run_ps = 'Set-VMNetworkAdapter –VMName ' + name + ' -MaximumBandwidth ' + rtx
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    
    return HttpResponse(json.dumps({'code':200,'msg':'创建实例成功'}))


def deleteInstance(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    run_ps = 'Stop-VM -Name ' + name +  ' -TurnOff'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    run_ps = 'Remove-VM -Name "' +name+'" -Force'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    config = configparser.ConfigParser()
    config.read('Compute.conf',encoding='utf-8')
    instance_path = config.get('storage','instance')
    fileto = instance_path + '/' + name + '.vhdx'
    os.remove(fileto)
    return HttpResponse(json.dumps({'code':200,'msg':'删除实例成功'}))

def startinstance(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误'}))
    
    if 'Running' in ps_result:
        return HttpResponse(json.dumps({'code':200,'msg':'实例已经启动'}))

    if ':00' in ps_result:
        run_ps = 'Start-VM -Name ' + name
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()
        run_ps = 'get-vm -name ' + name
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()
        if 'Running' in ps_result:
            return HttpResponse(json.dumps({'code':200,'msg':'实例启动成功'}))
        else:
            return HttpResponse(json.dumps({'code':400,'msg':'实例启动失败'}))
    return HttpResponse(json.dumps({'code':400,'msg':'启动实例时发生错误'}))

def stopinstance(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误'}))
    
    if ':00' in ps_result:
        return HttpResponse(json.dumps({'code':200,'msg':'实例已经停止'}))

    if 'Running' in ps_result:
        run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()
        run_ps = 'get-vm -name ' + name
        ps_result = os.popen('powershell.exe -command ' + run_ps).read()
        if ':00' in ps_result:
            return HttpResponse(json.dumps({'code':200,'msg':'实例停止成功'}))
        else:
            return HttpResponse(json.dumps({'code':400,'msg':'实例停止失败'}))
    return HttpResponse(json.dumps({'code':400,'msg':'停止实例时发生错误'}))


def getInstancevid(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 获取虚拟机的guid
    run_ps = '(get-vm -Name ' + name + ').ID'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误'}))
    vid = ps_result.split('\n')
    for i in vid:
        # 去掉行空格
        i = i.strip()
        if '-' in i and len(i) > 15:
            return HttpResponse(json.dumps({'code':200,'msg':'获取成功','vid':i}))

    return HttpResponse(json.dumps({'code':400,'msg':'获取失败','error':ps_result}))

def restartinstance(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误'}))

    # 强制断电
    run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # 启动
    run_ps = 'Start-VM -Name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # 获取状态
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'Running' in ps_result:
        return HttpResponse(json.dumps({'code':200,'msg':'重启实例成功'}))
    return HttpResponse(json.dumps({'code':400,'msg':'实例重启失败'}))

def bootinstance(request):
    f1 = request.GET.get('f1')
    f2 = request.GET.get('f2')
    f3 = request.GET.get('f3')
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))
    if f1 == None or f2 == None or f3 == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    # 强制断电
    run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    
    ps_run = 'Set-VMBios -VMName "' + name +  '" -EnableNumLock -StartupOrder (\'' + f1 + '\', \'' + f2 + '\', \'' +f3+ '\', \'Floppy\')'
    ps_result = os.popen('powershell.exe -command ' + ps_run).read()
    if ps_result != '':
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    # 启动
    run_ps = 'Start-VM -Name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    return HttpResponse(json.dumps({'code':200,'msg':'启动实例成功'}))


# 嵌套虚拟化
def virtualinstance(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    # 强制断电
    run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()


    # 设置嵌套虚拟化
    run_ps = 'Set-VMProcessor -VMName ' + name + ' -ExposeVirtualizationExtensions $true'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # 开机
    run_ps = 'Start-VM -Name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    return HttpResponse(json.dumps({'code':200,'msg':'虚拟化成功'}))

def bindip(request):
    name = request.GET.get('name')
    ipaddr = request.GET.get('ipaddr')
    if name == None or ipaddr == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    acl2 = 'Add-VMNetworkAdapterAcl -VMName ' + name + ' -LocalIPAddress ' + ipaddr + '/32 -Direction Both -Action Allow'
    acl1 = 'Add-VMNetworkAdapterAcl -VMName ' + name + ' -LocalIPAddress ANY -Direction Both -Action Deny'
    ps_result = os.popen('powershell.exe -command ' + acl1).read()
    ps_result = os.popen('powershell.exe -command ' + acl2).read()
    return HttpResponse(json.dumps({'code':200,'msg':'绑定IP成功'}))


def getcpu(request):
    name = request.GET.get('name')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    # run_ps = 'get-vm -name ' + name
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # if 'InvalidParamete' in ps_result:
    #     return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    # 获取CPU
    run_ps = '$a = Get-VM -Name ' + name + ' ; $a.CPUUsage'
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))

    # 去掉\n
    ps_result = ps_result.replace('\n','')
    return HttpResponse(json.dumps({'code':200,'msg':'获取成功','cpu':ps_result}))

def backupinstance(request):
    name = request.GET.get('name')
    id = request.GET.get('id')
    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    if id == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例是否存在
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))


    # 这里我们改成检查点
    ps = "Checkpoint-VM -Name " + name + " -SnapshotName " + id
    ps_result = os.popen('powershell.exe -command ' + ps).read()
    # if 'InvalidParamete' in ps_result:
    # 如果没有返回值，说明备份成功
    if ps_result == '':
        return HttpResponse(json.dumps({'code':200,'msg':'备份成功'}))
    
    return HttpResponse(json.dumps({'code':400,'msg':'备份失败','error':ps_result}))


    # # 强制断电
    # run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # # 获取路径
    # config = configparser.ConfigParser()
    # config.read('Compute.conf',encoding='utf-8')
    # instance_path = config.get('storage','instance')
    # backup_path = config.get('storage','backup')

    # # 复制instance_path下的实例到backup_path下
    # run_ps = 'copy ' + instance_path + '/' + name + '.vhdx ' + backup_path + '/' + id + '.vhdx'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # # 启动
    # run_ps = 'Start-VM -Name ' + name
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # return HttpResponse(json.dumps({'code':200,'msg':'备份实例成功'}))

def restoreinstance(request):
    id = request.GET.get('id')
    name = request.GET.get('name')
    if id == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    if name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断实例
    run_ps = 'get-vm -name ' + name
    ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    if 'InvalidParamete' in ps_result:
        return HttpResponse(json.dumps({'code':400,'msg':'错误','error':ps_result}))


    # 恢复检查点
    ps = "Restore-VMSnapshot -VMName  " + name + " -Name " + id + " -Confirm:$false"
    ps_result = os.popen('powershell.exe -command ' + ps).read()
    if ps_result == '':
        return HttpResponse(json.dumps({'code':200,'msg':'恢复成功'}))

    return HttpResponse(json.dumps({'code':400,'msg':'恢复失败','error':ps_result}))
    # if 'InvalidParamete' in ps_result:

    # # 断电
    # run_ps = 'Stop-VM -Name ' + name + ' -TurnOff'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()

    # 获取路径
    # config = configparser.ConfigParser()
    # config.read('Compute.conf',encoding='utf-8')
    # instance_path = config.get('storage','instance')
    # backup_path = config.get('storage','backup')
    # # 删除原来的
    # run_ps = 'del ' + instance_path + '/' + name + '.vhdx'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # # 复制备份到instance_path下
    # run_ps = 'copy ' + backup_path + '/' + id + '.vhdx ' + instance_path + '/' + name + '.vhdx'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # # 删除avhdx文件
    # run_ps = 'del ' + instance_path + '/' + id + '.avhdx'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # # 启动
    # run_ps = 'Start-VM -Name ' + name
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # return HttpResponse(json.dumps({'code':200,'msg':'恢复实例成功'}))

def deletebackup(request):
    id = request.GET.get('id')
    name = request.GET.get('name')
    if id == None or name == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    
    ps = 'Remove-VMSnapshot -Name ' + id + ' -VMName ' + name
    ps_result = os.popen('powershell.exe -command ' + ps).read()
    return HttpResponse(json.dumps({'code':200,'msg':'删除备份成功'}))
    # # 获取配置
    # config = configparser.ConfigParser()
    # config.read('Compute.conf',encoding='utf-8')
    # backup_path = config.get('storage','backup')
    # # 删除备份
    # run_ps = 'del ' + backup_path + '/' + id + '.vhdx'
    # ps_result = os.popen('powershell.exe -command ' + run_ps).read()
    # return HttpResponse(json.dumps({'code':200,'msg':'删除备份成功'}))