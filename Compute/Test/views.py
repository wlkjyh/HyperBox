from django.shortcuts import render,HttpResponse
import json
# Create your views here.


def Connect(request):
    return HttpResponse(json.dumps({'code':200,'msg':'connect success'}))