<?php
include 'Lib/phpQuery/phpQuery.php';
include 'Lib/Smarty/Smarty.class.php';

$keywords = trim($_POST['keywords']);
$urls = trim($_POST['urls']);
$keyword_arr = explode("\n",$keywords);
$url_arr = explode("\n",$urls);

foreach ($keyword_arr as $key) {
    $randa = rand(100,999);
    $url = 'http://wap.baidu.com/s?word='.$key.'&ts=8173515&t_kt=46&rsv_iqid=13192952292074186'.$randa.'&sa=ib&rsv_sug4=3785&inputT=1951&ss=100';
    phpQuery::newDocumentFile($url);
   $get_arr = pq(".ec_site");
    foreach($get_arr as $gKey=>$gVal){
        $gValHtml = pq($gVal)->html();
        if(in_array($gValHtml,$url_arr)){
            $sql = "INSERT INTO `mobile_baidu` (keyword,url,rank,`timestamp`) VALUES ($key,$gValHtml,$gKey+1,now())";
            echo $sql;
            echo $gValHtml;
            echo '<br>',$gKey;
            echo 'a';
        }else{
            echo '<br>',$gValHtml;
            echo '<br>',$gKey;
        }
    }
}



