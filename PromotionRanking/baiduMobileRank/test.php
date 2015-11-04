<?php
function FormatHeader($url, $myIp = null,$xml = null)
{
// 解悉url 
    $temp = parse_url($url);
    $query = isset($temp['query']) ? $temp['query'] : '';
    $path = isset($temp['path']) ? $temp['path'] : '/';

    $header = array (
        'Host: wap.baidu.com',
        'Connection:keep-alive',
        'Content-type: application/x-www-form-urlencoded;charset=UTF-8',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
    );

    return $header;
}
$user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4';


$interface = 'http://wap.baidu.com/s?word=%E6%97%A0%E9%94%A1%E8%85%8B%E8%87%AD&ts=8173515&t_kt=46&rsv_iqid=13192952292074186622&sa=ib&rsv_sug4=3785&inputT=1951&ss=100';
$header = FormatHeader($interface,'58.219.75.172');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $interface);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置头信息的地方
//curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//请求百度失败
curl_setopt($ch, CURLOPT_TIMEOUT, 50);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

print_r($result);
echo '<br>';
print_r(getallheaders());
?> 