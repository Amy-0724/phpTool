<?php
include 'Lib/phpQuery/phpQuery.php';
include 'Lib/Smarty/Smarty.class.php';

$keywords = trim($_POST['keywords']);
$urls = trim($_POST['urls']);
if(empty($keywords)&empty($urls)){
	echo '请输入争取参数';
	exit;
}
$keyword_arr = explode("\n",$keywords);
$url_arr = explode("\n",$urls);

foreach ($keyword_arr as $key) {
    $randa = rand(100,999);
    $url = 'http://wap.baidu.com/s?word='.$key.'&ts=8173515&t_kt=46&rsv_iqid=13192952292074186'.$randa.'&sa=ib&rsv_sug4=3785&inputT=1951&ss=100';
    phpQuery::newDocumentFile($url);
   $get_arr = pq(".ec_site");
    foreach($get_arr as $gKey=>$gVal){
        $gValHtml = pq($gVal)->html();
        if(empty($gValHtml)){
            continue;
        }elseif(!is_array($gValHtml)){
            $gValHtmls[] = $gValHtml;
        }else{
            $gValHtmls = $gValHtml;
        }


        foreach($url_arr as $uKey=>$urlone) {
            if (in_array($urlone,$gValHtmls)) {
//                $sql = "INSERT INTO `mobile_baidu` (keyword,url,rank,`timestamp`) VALUES ($key,$gValHtml,$gKey+1,now())";
                $rankVal['keyword'] =  $key;
                $rankVal['url'] =  $gValHtml;
                $rankVal['rank'] =  $gKey+1;
                $rankVal_arr[] = $rankVal;
            } else {
                if($uKey!=0) {
                    $rankVal['keyword'] = $key;
                    $rankVal['url'] = $gValHtml;
                    $rankVal['rank'] = 'no';
                    $rankVal_arr[] = $rankVal;
                }
            }
        }
        unset($gValHtmls);
    }
}
include 'show.php';
