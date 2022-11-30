<?php

use \App\Users;
use \App\logs;

function getrandom($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function userrow($field = 'ALL')
{
    $Userrow = Users::where('token', session()->get('token'))->first();
    if ($field == 'ALL') {
        return $Userrow;
    }
    return $Userrow->$field;
}

function has_int($k)
{
    if (is_numeric($k) == false) return false;
    if (strstr($k, '.')) return false;
    if (strstr($k, '-')) return false;
    if (strstr($k, '+')) return false;
    return true;
}

function has_ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP)) return true;
    return false;
}

function netmasktoprefix($netmask)
{
    $arr = [
        '255.255.255.255' => 32,
        '255.255.255.254' => 31,
        '255.255.255.252' => 30,
        '255.255.255.248' => '29',
        '255.255.255.240' => '28',
        '255.255.255.224' => '27',
        '255.255.255.192' => '26',
        '255.255.255.128' => '25',
        '255.255.255.0' => '24',
        '255.255.254.0' => '23',
        '255.255.252.0' => '22',
        '255.255.248.0' => '21',
        '255.255.240.0' => '20',
        '255.255.224.0' => '19 ',
        '255.255.192.0' => '18',
        '255.255.128.0' => '17',
        '255.255.0.0' => '16',
        '255.254.0.0' => '15',
        '255.252.0.0' => '14',
        '255.248.0.0' => '13',
        '255.240.0.0' => '12',
        '255.224.0.0' => '11',
        '255.192.0.0' => '10',
        '255.128.0.0' => '9',
        '255.0.0.0' => '8',
        '254.0.0.0' => '7',
        '252.0.0.0' => '6',
        '248.0.0.0' => '5',
        '240.0.0.0' => '4',
        '224.0.0.0' => '3',
        '192.0.0.0' => '2',
        '128.0.0.0' => '1',
        '0.0.0.0' => '0',
    ];
    return isset($arr[$netmask]) ? $arr[$netmask] : '0';
}

function getrealhost($hostname)
{
    if (strstr($hostname, ':')) {
        $host = &$hostname;
    } else {
        $host = $hostname . ':3000';
    }
    return $host;
}

function writelog($form, $message)
{
    logs::create([
        'id' => uuid(),
        'form' => $form,
        'data' => $message,
    ]);
}

function getconfig($field)
{
    $file = file_get_contents(__DIR__ . '/config.json');
    $json = json_decode($file, true);
    return $json[$field];
}

function setconfig($field, $result)
{
    $file = file_get_contents(__DIR__ . '/config.json');
    $json = json_decode($file, true);
    $json[$field] = $result;
    $json = json_encode($json);
    file_put_contents(__DIR__ . '/config.json', $json);
    return true;
}

function timezones()
{
    $timezones =

        array(

            '(GMT-12:00) International Date Line West' => 'Pacific/Wake',

            '(GMT-11:00) Midway Island' => 'Pacific/Apia',

            '(GMT-11:00) Samoa' => 'Pacific/Apia',

            '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',

            '(GMT-09:00) Alaska' => 'America/Anchorage',

            '(GMT-08:00) Pacific Time (US & Canada); Tijuana' => 'America/Los_Angeles',

            '(GMT-07:00) Arizona' => 'America/Phoenix',

            '(GMT-07:00) Chihuahua' => 'America/Chihuahua',

            '(GMT-07:00) La Paz' => 'America/Chihuahua',

            '(GMT-07:00) Mazatlan' => 'America/Chihuahua',

            '(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',

            '(GMT-06:00) Central America' => 'America/Managua',

            '(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',

            '(GMT-06:00) Guadalajara' => 'America/Mexico_City',

            '(GMT-06:00) Mexico City' => 'America/Mexico_City',

            '(GMT-06:00) Monterrey' => 'America/Mexico_City',

            '(GMT-06:00) Saskatchewan' => 'America/Regina',

            '(GMT-05:00) Bogota' => 'America/Bogota',

            '(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',

            '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',

            '(GMT-05:00) Lima' => 'America/Bogota',

            '(GMT-05:00) Quito' => 'America/Bogota',

            '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',

            '(GMT-04:00) Caracas' => 'America/Caracas',

            '(GMT-04:00) La Paz' => 'America/Caracas',

            '(GMT-04:00) Santiago' => 'America/Santiago',

            '(GMT-03:30) Newfoundland' => 'America/St_Johns',

            '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',

            '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',

            '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',

            '(GMT-03:00) Greenland' => 'America/Godthab',

            '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',

            '(GMT-01:00) Azores' => 'Atlantic/Azores',

            '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',

            '(GMT) Casablanca' => 'Africa/Casablanca',

            '(GMT) Edinburgh' => 'Europe/London',

            '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',

            '(GMT) Lisbon' => 'Europe/London',

            '(GMT) London' => 'Europe/London',

            '(GMT) Monrovia' => 'Africa/Casablanca',

            '(GMT+01:00) Amsterdam' => 'Europe/Berlin',

            '(GMT+01:00) Belgrade' => 'Europe/Belgrade',

            '(GMT+01:00) Berlin' => 'Europe/Berlin',

            '(GMT+01:00) Bern' => 'Europe/Berlin',

            '(GMT+01:00) Bratislava' => 'Europe/Belgrade',

            '(GMT+01:00) Brussels' => 'Europe/Paris',

            '(GMT+01:00) Budapest' => 'Europe/Belgrade',

            '(GMT+01:00) Copenhagen' => 'Europe/Paris',

            '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',

            '(GMT+01:00) Madrid' => 'Europe/Paris',

            '(GMT+01:00) Paris' => 'Europe/Paris',

            '(GMT+01:00) Prague' => 'Europe/Belgrade',

            '(GMT+01:00) Rome' => 'Europe/Berlin',

            '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',

            '(GMT+01:00) Skopje' => 'Europe/Sarajevo',

            '(GMT+01:00) Stockholm' => 'Europe/Berlin',

            '(GMT+01:00) Vienna' => 'Europe/Berlin',

            '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',

            '(GMT+01:00) West Central Africa' => 'Africa/Lagos',

            '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',

            '(GMT+02:00) Athens' => 'Europe/Istanbul',

            '(GMT+02:00) Bucharest' => 'Europe/Bucharest',

            '(GMT+02:00) Cairo' => 'Africa/Cairo',

            '(GMT+02:00) Harare' => 'Africa/Johannesburg',

            '(GMT+02:00) Helsinki' => 'Europe/Helsinki',

            '(GMT+02:00) Istanbul' => 'Europe/Istanbul',

            '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',

            '(GMT+02:00) Kyiv' => 'Europe/Helsinki',

            '(GMT+02:00) Minsk' => 'Europe/Istanbul',

            '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',

            '(GMT+02:00) Riga' => 'Europe/Helsinki',

            '(GMT+02:00) Sofia' => 'Europe/Helsinki',

            '(GMT+02:00) Tallinn' => 'Europe/Helsinki',

            '(GMT+02:00) Vilnius' => 'Europe/Helsinki',

            '(GMT+03:00) Baghdad' => 'Asia/Baghdad',

            '(GMT+03:00) Kuwait' => 'Asia/Riyadh',

            '(GMT+03:00) Moscow' => 'Europe/Moscow',

            '(GMT+03:00) Nairobi' => 'Africa/Nairobi',

            '(GMT+03:00) Riyadh' => 'Asia/Riyadh',

            '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',

            '(GMT+03:00) Volgograd' => 'Europe/Moscow',

            '(GMT+03:30) Tehran' => 'Asia/Tehran',

            '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',

            '(GMT+04:00) Baku' => 'Asia/Tbilisi',

            '(GMT+04:00) Muscat' => 'Asia/Muscat',

            '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',

            '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',

            '(GMT+04:30) Kabul' => 'Asia/Kabul',

            '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',

            '(GMT+05:00) Islamabad' => 'Asia/Karachi',

            '(GMT+05:00) Karachi' => 'Asia/Karachi',

            '(GMT+05:00) Tashkent' => 'Asia/Karachi',

            '(GMT+05:30) Chennai' => 'Asia/Calcutta',

            '(GMT+05:30) Kolkata' => 'Asia/Calcutta',

            '(GMT+05:30) Mumbai' => 'Asia/Calcutta',

            '(GMT+05:30) New Delhi' => 'Asia/Calcutta',

            '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',

            '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',

            '(GMT+06:00) Astana' => 'Asia/Dhaka',

            '(GMT+06:00) Dhaka' => 'Asia/Dhaka',

            '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',

            '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',

            '(GMT+06:30) Rangoon' => 'Asia/Rangoon',

            '(GMT+07:00) Bangkok' => 'Asia/Bangkok',

            '(GMT+07:00) Hanoi' => 'Asia/Bangkok',

            '(GMT+07:00) Jakarta' => 'Asia/Bangkok',

            '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',

            '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',

            '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',

            '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',

            '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',

            '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',

            '(GMT+08:00) Perth' => 'Australia/Perth',

            '(GMT+08:00) Singapore' => 'Asia/Singapore',

            '(GMT+08:00) Taipei' => 'Asia/Taipei',

            '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',

            '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',

            '(GMT+09:00) Osaka' => 'Asia/Tokyo',

            '(GMT+09:00) Sapporo' => 'Asia/Tokyo',

            '(GMT+09:00) Seoul' => 'Asia/Seoul',

            '(GMT+09:00) Tokyo' => 'Asia/Tokyo',

            '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',

            '(GMT+09:30) Adelaide' => 'Australia/Adelaide',

            '(GMT+09:30) Darwin' => 'Australia/Darwin',

            '(GMT+10:00) Brisbane' => 'Australia/Brisbane',

            '(GMT+10:00) Canberra' => 'Australia/Sydney',

            '(GMT+10:00) Guam' => 'Pacific/Guam',

            '(GMT+10:00) Hobart' => 'Australia/Hobart',

            '(GMT+10:00) Melbourne' => 'Australia/Sydney',

            '(GMT+10:00) Port Moresby' => 'Pacific/Guam',

            '(GMT+10:00) Sydney' => 'Australia/Sydney',

            '(GMT+10:00) Vlapostok' => 'Asia/Vlapostok',

            '(GMT+11:00) Magadan' => 'Asia/Magadan',

            '(GMT+11:00) New Caledonia' => 'Asia/Magadan',

            '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',

            '(GMT+12:00) Auckland' => 'Pacific/Auckland',

            '(GMT+12:00) Fiji' => 'Pacific/Fiji',

            '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',

            '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',

            '(GMT+12:00) Wellington' => 'Pacific/Auckland',

            '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',

        );
    return $timezones;
}


function createAt($end_time)
{
    $begin_time = date('Y-m-d H:i:s');
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    //获取相差
    $timediff = strtotime($endtime) - strtotime($starttime);
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    if ($mins == 0 && $hours == 0) {
        return $secs . '秒';
    }
    if ($hours == 0) {
        return $mins . '分钟';
    }
    if ($days == 0) {
        return $hours . '小时' . $mins . '分钟';
    }
    return $days . '日' . $hours . '小时';
}

function UserToAcloud()
{
    $userrow = userrow('username');
    if ($userrow != 'admin') return exit(<<<EOF
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="refresh" content="0;url='http://{$_SERVER['HTTP_HOST']}/acloud'" />
    
            <title>Redirecting to http://{$_SERVER['HTTP_HOST']}/acloud</title>
        </head>
        <body>
            Redirecting to <a href="http://127.0.0.1/acloud">http://127.0.0.1/acloud</a>.
        </body>
    </html>
EOF);
}


function getmacbyip($clientip)
{
    shell_exec("ping $clientip -n 1 -l 1");

    $str =  shell_exec('powershell -command "arp -a"');
    // echo $str;
    if (strstr($str, $clientip) == false) {
        return false;
    }
    if(strstr($str,$clientip.' ---')) return 'localhost';
    $arr = explode("\n", $str);
    $realmac = false;
    foreach ($arr as $val) {
        $macaddr = false;
        $ipaddr = false;
        preg_match('/([0-9a-fA-F]{2}(:|-)){5}[0-9a-fA-F]{2}/', $val, $matches);
        if (isset($matches[0])) {
            $macaddr = $matches[0];
        }
        preg_match('/([0-9]{1,3}.){3}[0-9]{1,3}/', $val, $matches);
        if (isset($matches[0])) {
            $ipaddr = $matches[0];
        }
        if ($ipaddr && $macaddr) {
            if ($ipaddr == $clientip) {
                $realmac = $macaddr;
                break;
            }
        }
    }
    return $realmac;
}
