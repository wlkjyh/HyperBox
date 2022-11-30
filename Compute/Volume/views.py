from django.shortcuts import render,HttpResponse
import json,configparser,os

# Create your views here.
def createvolume(request):
    name = request.GET.get('name')
    size = request.GET.get('size')
    if name == None or size == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    config = configparser.ConfigParser()
    config.read('Compute.conf',encoding='utf-8')
    volume_path = config.get('storage','volume')
    fileto = volume_path + '/' + name + '.vhdx'

    os.system('powershell.exe New-VHD -Path ' + fileto + ' -SizeBytes ' + size + 'GB -BlockSizeBytes 128MB -LogicalSectorSize 4KB > ' + fileto + '.log')
    # 读取错误日志
    f = open(fileto + '.log', 'r')
    log = f.read()
    f.close()
    # 判断是否创建成功
    if os.path.exists(fileto):
        return HttpResponse(json.dumps({'code':200,'msg':'已创建','path':fileto}))
    else:
        return HttpResponse(json.dumps({'code':400,'msg':'创建失败','error':log}))


def deleteVolume(request):
    path = request.GET.get('path')
    if path == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))
    if os.path.exists(path):
        os.remove(path)
        if os.path.exists(path):
            return HttpResponse(json.dumps({'code':400,'msg':'删除失败'}))
        return HttpResponse(json.dumps({'code':200,'msg':'已删除'}))
    else:
        return HttpResponse(json.dumps({'code':400,'msg':'文件不存在'}))

def resizevolume(request):
    path = request.GET.get('path')
    size = request.GET.get('size')
    if path == None or size == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))
    if os.path.exists(path):
        run_ps = 'Resize-VHD -Path ' + path + ' -SizeBytes ' + size + 'GB'
        run_result = os.popen('powershell.exe ' + run_ps).read()
        # 如果包含InvalidParameter
        if 'InvalidParameter' in run_result:
            return HttpResponse(json.dumps({'code':400,'msg':'扩展卷大小失败','error':run_result}))
        
        if os.path.exists(path):
            return HttpResponse(json.dumps({'code':200,'msg':'已重置','path':path}))

        return HttpResponse(json.dumps({'code':400,'msg':'未知错误','error':run_result}))
    else:
        return HttpResponse(json.dumps({'code':400,'msg':'文件不存在'}))

def connectvolume(request):
    name = request.GET.get('name')
    volume = request.GET.get('volume')
    if name == None or volume == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    config = configparser.ConfigParser()
    config.read('Compute.conf',encoding='utf-8')
    volume_path = config.get('storage','volume')
    fileto = volume_path + '/' + volume + '.vhdx'
    # 如果不存在
    if not os.path.exists(fileto):
        return HttpResponse(json.dumps({'code':400,'msg':'卷不存在'}))

    # 判断虚拟机是否存在
    run_ps = 'Get-VM -Name' + name
    run_result = os.popen('powershell.exe ' + run_ps).read()
    if 'InvalidParamete' in run_result:
        return HttpResponse(json.dumps({'code':400,'msg':'虚拟机不存在'}))

    # 连接卷到虚拟机
    run_ps = 'Add-VMHardDiskDrive -VMName ' + name + ' -Path ' + fileto + ' -ControllerType SCSI'
    run_result = os.popen('powershell.exe ' + run_ps).read()
    if run_result != '':
        return HttpResponse(json.dumps({'code':400,'msg':'连接卷失败','error':run_result}))

    # 获取在那个ide
    run_ps = '$disk = Get-VMHardDiskDrive -VMName '+name+' -ControllerType SCSI ; $disk.ControllerLocation ; $disk.Path'
    run_result = os.popen('powershell.exe ' + run_ps).read()
    if run_result == '':
        return HttpResponse(json.dumps({'code':400,'msg':'获取ide失败'}))
   
    return HttpResponse(json.dumps({'code':200,'msg':'已连接','data':run_result}))

# 分离卷
def unconnectvolume(request):
    name = request.GET.get('name')
    ide = request.GET.get('ide')
    if name == None or ide == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))

    # 判断虚拟机是否存在
    run_ps = 'Get-VM -Name' + name
    run_result = os.popen('powershell.exe ' + run_ps).read()
    if 'InvalidParamete' in run_result:
        return HttpResponse(json.dumps({'code':400,'msg':'虚拟机不存在'}))

    run_ps ='Remove-VMHardDiskDrive -VMName ' + name + ' -ControllerType SCSI -ControllerLocation ' + ide + ' -ControllerNumber 0'
    run_result = os.popen('powershell.exe ' + run_ps).read()
    if run_result == '':
        return HttpResponse(json.dumps({'code':200,'msg':'分离卷成功'}))

    return HttpResponse(json.dumps({'code':400,'msg':'分离卷失败','error':run_result}))