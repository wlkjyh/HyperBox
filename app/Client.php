<?php
namespace App;

class Client
{
    public function __construct()
    {
        $this->header = [];
        $this->url = '';
        $this->data = [];
        $this->method = 'GET';
        return $this;
    }

    public function AddHeader($key, $value)
    {
        $this->header[$key] = $value;
        return $this;
    }

    public function SetProxy($proxy = '127.0.0.1:8080', $username = '', $password = '')
    {
        $this->proxy_host = $proxy;
        $this->proxy_username = $username;
        $this->proxy_password = $password;
        return $this;
    }


    public function SetIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function SetRandomIp()
    {
        $ip_long = [
            ['607649792', '608174079'],
            ['1038614528', '1039007743'],
            ['1783627776', '1784676351'],
            ['2035023872', '2035154943'],
            ['2078801920', '2079064063'],
            ['-1950089216', '-1948778497'],
            ['-1425539072', '-1425014785'],
            ['-1236271104', '-1235419137'],
            ['-770113536', '-768606209'],
            ['-569376768', '-564133889']
        ];
        $rand_key = mt_rand(0, 9);
        $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        $this->ip = $ip;
        return $this;
    }

    public function ParseHeader($param)
    {
        $lines = explode("\n", $param);
        $header = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $pos = strpos($line, ':');
            if ($pos === false) {
                continue;
            }
            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            // $header[$key] = $value;
            $this->header[$key] = $value;
        }
        return $this;
    }

    public function DefaultHeader()
    {
        $this->AddHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36');
        $this->AddHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');
        $this->AddHeader('Accept-Language', 'zh-CN,zh;q=0.9,en;q=0.8');
        $this->AddHeader('Connection', 'keep-alive');
        $this->AddHeader('Upgrade-Insecure-Requests', '1');
        $this->AddHeader('Cache-Control', 'max-age=0');
        $this->AddHeader('TE', 'Trailers');
        $this->AddHeader('Content-Type', 'application/x-www-form-urlencoded');
        return $this;
    }

    public function Make($url, $data = [], $method = 'GET')
    {
        $this->url = $url;
        // $this->method = $method;
        // $this->data = $data;
        if (is_array($data)) {
            // $this->data = $data;
            $this->data = http_build_query($data);
        }else{
            $this->data = $data;
        }
        if ($method == "GET") {
            // $this->url = $url.'?'.$this->data;
            if (strpos($url, '?')) {
                $this->url = $url.'&'.$this->data;
            } else {
                $this->url = $url.'?'.$this->data;
            }
        } else {
            $method = "POST";
        }
        $this->method = $method;
        return $this;
    }

    public function Do($timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $header = [];
        foreach ($this->header as $key => $value) {
            $header[] = $key.':'.$value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($this->method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }
        if (isset($this->proxy_host)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy_host);
            if (isset($this->proxy_username) && isset($this->password)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_username.':'.$this->proxy_password);
            }
        }
        if (isset($this->ip)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$this->ip, 'CLIENT-IP:'.$this->ip));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $output = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($output, 0, $headerSize);
        $body = substr($output, $headerSize);
        curl_close($ch);
        $this->response_body = $body;
        $this->response_header = $header;
        return $this;
    }

    public function getResponseBody()
    {
        return $this->response_body;
    }

    public function getResponseHeader()
    {
        return $this->response_header;
    }

    public function getResponseCookies()
    {
        $header = $this->response_header;
        $cookies = [];
        $header = explode("\r\n", $header);
        foreach ($header as $key => $value) {
            if (strpos($value, 'Set-Cookie:') !== false) {
                $value = str_replace('Set-Cookie: ', '', $value);
                $value = explode(';', $value);
                $cookies[$key] = $value[0];
            }
        }
        $cookies = implode(';', $cookies);
        return $cookies;
    }

    public function getResponseBodyJson()
    {
        return json_decode($this->response_body, true);
    }

    public function getResponseBodyXml()
    {
        $object = simplexml_load_string($this->response_body);
        $object = json_decode(json_encode($object), true);
        return $object;
    }
  
    public function getInstance()
    {
        return $this;
    }
}