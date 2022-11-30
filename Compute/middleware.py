# file:mymiddleware.py
from django.http import HttpResponse
from django.utils.deprecation import MiddlewareMixin
import re,configparser,json
class IpAddr(MiddlewareMixin):
   count_dict = {}
   def process_request(self,request):
        request_ip = request.META['REMOTE_ADDR']
        config = configparser.ConfigParser()
        config.read('Compute.conf',encoding='utf-8')
        ip = config.get('auth','auth_ipaddr')
        if ip == 'any':
            return
        if re.match(ip,request_ip):
            return
        return HttpResponse(json.dumps({'code':403,'msg':'IP未被信任[' + request_ip + ']'}))

