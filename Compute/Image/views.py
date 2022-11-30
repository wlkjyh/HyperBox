from django.shortcuts import render,HttpResponse
import json,configparser
# os
import os
# Create your views here.

def downimage(request):
    downurl = request.GET.get('downurl')
    id = request.GET.get('id')
    ext = request.GET.get('ext')
    callback = request.GET.get('callback')
    if downurl == None or id == None or ext == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))
    
    config = configparser.ConfigParser()
    config.read('Compute.conf',encoding='utf-8')
    image_path = config.get('storage','image')
    fileto = image_path + '/' + id + '.' + ext
    root_path = os.path.dirname(os.path.abspath(__file__))
    root_path = os.path.dirname(root_path)
    os.system('start cmd /C '+root_path+'/app/downurl.bat ' + fileto + ' ' + downurl + ' ' + callback)
    return HttpResponse(json.dumps({'code':200,'msg':'已提交','path':fileto}))

def deleteimage(request):
    path = request.GET.get('path')
    if path == None:
        return HttpResponse(json.dumps({'code':400,'msg':'参数错误'}))
    if os.path.exists(path):
        os.remove(path)
        return HttpResponse(json.dumps({'code':200,'msg':'已删除'}))
    else:
        return HttpResponse(json.dumps({'code':400,'msg':'文件不存在'}))