"""Compute URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/3.2/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import path
import Test.views as Test
import Image.views as Image
import Volume.views as Volume
import Instance.views as Instance
# import route.views as route
urlpatterns = [
    path('connect', Test.Connect),
    path('downimage', Image.downimage),
    path('deleteimage', Image.deleteimage),
    path('createvolume',Volume.createvolume),
    path('deletevolume',Volume.deleteVolume),
    path('resizevolume',Volume.resizevolume),
    path('createinstance',Instance.createInstance),
    path('deleteinstance',Instance.deleteInstance),
    path('startinstance',Instance.startinstance),
    path('stopinstance',Instance.stopinstance),
    path('getinstancevid', Instance.getInstancevid),
    path('restartinstance', Instance.restartinstance),
    path('bootinstance', Instance.bootinstance),
    # 连接卷
    path('connectvolume', Volume.connectvolume),
    path('unconnectvolume', Volume.unconnectvolume),
    path('virtualinstance',Instance.virtualinstance),
    # bindip
    path('bindip', Instance.bindip),
    path('getcpu',Instance.getcpu),

    path('backupinstance',Instance.backupinstance),
    # 恢复备份
    path('restoreinstance',Instance.restoreinstance),
    # 删除备份
    path('deletebackup',Instance.deletebackup),


]
