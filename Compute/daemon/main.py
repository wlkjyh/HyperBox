import os,json,time,configparser,base64

config = configparser.ConfigParser()
config.read('config.ini', encoding='utf-8')
config = config['config']


while True:
    time.sleep(0.5)
    try:
        data = {}
        cmd = "powershell.exe \"Get-VM | Select-Object Name,State | ConvertTo-Json\""
        result = os.popen(cmd).read()
        result = json.loads(result)
        for i in result:
            if i['State'] == 2:
                # print(i['Name'] + ' is running')
                data[i['Name']] = 'running'
            else:
                # print(i['Name'] + ' is not running')
                data[i['Name']] = 'off'

        data = json.dumps(data)
        data = base64.b64encode(data.encode('utf-8'))
        data = data.decode('utf-8')

        # url编码
        print('-----------------')
        print(config['update'] + '?data=' + data)
        print(os.popen('curl ' + config['update'] + '?data=' + data).read())

        print('状态发送成功')
    except:
        print('出错了')