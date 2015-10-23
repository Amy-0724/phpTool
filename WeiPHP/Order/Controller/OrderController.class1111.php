<?php

namespace Addons\Order\Controller;
use Home\Controller\AddonsController;

class OrderController extends AddonsController{
	Public function _initialize() {
		$controller = strtolower ( _ACTION );
		$res ['title'] = '预约';
		$res ['class'] = $controller == 'lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		$this->assign ( 'err', I('err') );
	}
	public function index_list(){
		$url = U('/addon/Heart/Heart/subscribe');
		$this->assign ( 'url_order', $url );

		$this->display ();
		
	}
	public function index_order(){
		$type = I('type');
		if($type=='0'){
			$this->assign ( 'title', '基础档——赴华东疗养院疗养体检推荐方案' );
			$this->assign ( 'money', '1642' );
		}
		if($type=='1'){
			$this->assign ( 'title', 'A档——赴华东疗养院疗体检推荐方案' );
			$this->assign ( 'money', '男 4173 / 女 4914' );
		}
		if($type=='2'){
			$this->assign ( 'title', 'B档——赴华东疗养院疗体检推荐方案' );
			$this->assign ( 'money', '男 2239 / 女 2935' );
		}
		if($type=='3'){
			$this->assign ( 'title', 'C档——赴华东疗养院疗体检推荐方案' );
			$this->assign ( 'money', '男 1242 / 女 1444元' );
		}
		if($type=='4'){
			$this->assign ( 'title', 'VIP档——赴华东疗养院疗体检推荐方案' );
			$this->assign ( 'money', '男 6288 / 女 7380' );
		}
		$this->assign ( 'type', $type );
		$url = U('/addon/Order/Order/order_form');
		$this->assign ( 'url', $url );
		
		$this->display ();
		
	}
	public function order_form(){
		$type = I('order_type');
		$money = I('money');
		$type_array = explode("_",$type);
		for($i=1;$i<count($type_array)-1;$i++){
			if($ad_type){
				$ad_type = $ad_type.",".$type_array[$i];
			}else{
				$ad_type = $type_array[$i];
			}
			
		}
		$data['openid']=get_openid();

		$data['yytc'] = $type_array['0'];
		$data['yyad'] = $ad_type;

		$Oreder = M("Oreder");
		$res=$Oreder->add($data);
		session('oid',$res);
		$url = U('/addon/Order/Order/index');
		$this->success('套餐预约成功',$url);
		
		$this->display ();
		
	}
	public function index(){
		$url = U('/addon/Order/Order/index');
		$url_add = U('/addon/Order/Order/addmsg');
		$this->assign ( 'url', $url );
		$this->assign ( 'url_add', $url_add );
		$openid = get_openid();
    	$Userinfo = M('Userinfo'); 		
		$where['openid'] = $openid;
    	$datas = $Userinfo->where($where)->find();	
		$this->assign('datas',$datas);
		$this->display ();
		
	}
	//预约
	public function addmsg(){
		$data['openid']=get_openid();
		$data['name'] = I('name');
		$data['sex'] = I('sex');
		$data['telphone'] = I('tel');
		$data['idcard'] = I('adid');
		$data['age'] = I('age');
		$data['yydata'] = I('date')." ".I('time');
		$data['yysum'] = I('num');

		if(!session('inhospno')){
			$order = M('Oreder');
			$openid = $data['openid'];
			$where['openid'] = $openid;
			$data_u =  $order->where($where)->find();
			$data['inhospno'] = $data_u['lyh'];
			if(!$data['inhospno']){
				$data['inhospno'] = date("YmdHis");
				$User = M("Userinfo");
				$openid=get_openid();
				$data_a['openid']=$openid;
				$data_a['lyh'] = $data['inhospno'];
				$data_a['name'] = $data['name'];
				$data_a['cardID'] = '';
				$data_a['sex'] = $data['sex'];
				$data_a['phone'] = $data['telphone'] ;
				$data_a['age'] = $data['age'];
				$imgSrc = $this->gwtWeixinImg();
				session('inhospno',$data_a['lyh']);
				$data_a['imgPath'] = $imgSrc;
				$res = $User->add($data_a);
			}				
		}else{
			$data['inhospno'] = session('inhospno');
		}

		$Oreder = M("Oreder");
		$id = session('oid');
		$Oreder->where('id='.$id)->save($data);

		$url = U('/addon/Heart/Heart/subscribe');
		$this->success('预约成功',$url);
	}
	
	public function basic_check_back($kes){
		$sqm = 'HDLYY-WXKF00001';
		$ywlx = '1';		//serviced：服务ID，调用的接口名称，定义为GetPatBasicInfo(病人信息)，GetPatCheckResult(结果说明)，GetPatSingleGuide(导引单信息)，GetPatItemFee(费用信息)
		$serviceid = 'GetPatBasicInfo'; 
		$instr;
		// 构造xml               
        $openid = get_openid();
        // get inhospno and name
        $Order = M("Userinfo");
        $where['openid'] = $openid;
        $dataOrder = $Order->where($where)->find();
        if(!empty($dataOrder['lyh'])){
            $inhospno = $dataOrder['lyh'];
            $name = $dataOrder['name'];
        }else{
            // test value 测试数据
            /* $inhospno = '2006053819';
            $name = '王正泉'; */
        }       
        // constitution 构造
        $getxml = $this->buildInstr($inhospno,$name);
        $instr = $getxml; 
		$res=$this->GetHisService($sqm,$ywlx,$serviceid,$instr,$inhospno,$name);
		$checks=$this->xml_to_array($res);
		
		$arr_check = explode('●',$checks['body']['patinfo']['JL']);
		 foreach($arr_check as $val){
			
			if(preg_match("/".$kes."/",$val,$regs)){
				$checks_value = $val;
			}
		} 
		//ben 
		/* foreach($arr_check as $key=>$val){			
			$gval = explode("\n",$val);
			if(!empty($gval[0])){
				$arr_checkben[] = $gval;
			}
		} */
		if(!$checks_value){
			$checks_value = '无';
		}else{
			$arr_check_value = explode(' ',$checks_value);
			$checks_value_a = $arr_check_value['1'];
			$checks_value = nl2br($checks_value_a);
			//$checks_value_a_b = explode('/\r/\n',$checks_value_a);
				//	$checks_value = $checks_value."<br/>".$val;
			//foreach($arr_check_value as $val){
			//	$checks_value = $checks_value."<br/>".$checks_value_a_b;
			//}
		}
		
		//return $arr_checkben;
		return $arr_check;
	}
	public function basic_check_back_ben(){
		$sqm = 'HDLYY-WXKF00001';
		$ywlx = '1';		//serviced：服务ID，调用的接口名称，定义为GetPatBasicInfo(病人信息)，GetPatCheckResult(结果说明)，GetPatSingleGuide(导引单信息)，GetPatItemFee(费用信息)
		$serviceid = 'GetPatBasicInfo'; 
		$instr;
		// 构造xml               
        $openid = get_openid();
        // get inhospno and name
        $Order = M("Userinfo");
        $where['openid'] = $openid;
        $dataOrder = $Order->where($where)->find();
        if(!empty($dataOrder['lyh'])){
            $inhospno = $dataOrder['lyh'];
            $name = $dataOrder['name'];
        }else{
            // test value 测试数据
            /* $inhospno = '2006053819';
            $name = '王正泉'; */
        }       
        // constitution 构造
        $getxml = $this->buildInstr($inhospno,$name);
        $instr = $getxml; 
		$res=$this->GetHisService($sqm,$ywlx,$serviceid,$instr,$inhospno,$name);
		$checks=$this->xml_to_array($res);
		
		$arr_check = explode('●',$checks['body']['patinfo']['JL']);
		/* foreach($arr_check as $val){
			
			if(preg_match("/".$kes."/",$val,$regs)){
				$checks_value = $val;
			}
		} */
		//ben 
		foreach($arr_check as $key=>$val){			
			$gval = explode("\n",$val);
			if(!empty($gval[0])){
				$arr_checkben[] = $gval;
			}
		}
		if(!$checks_value){
			$checks_value = '无';
		}else{
			$arr_check_value = explode(' ',$checks_value);
			$checks_value_a = $arr_check_value['1'];
			$checks_value = nl2br($checks_value_a);
			//$checks_value_a_b = explode('/\r/\n',$checks_value_a);
				//	$checks_value = $checks_value."<br/>".$val;
			//foreach($arr_check_value as $val){
			//	$checks_value = $checks_value."<br/>".$checks_value_a_b;
			//}
		}
		
		return $arr_checkben;
		
	}
	
	
	
	public function index_install(){
		$this->display ();
		
	}
	public function binding(){
		$url_add = U('/addon/Order/Order/binding_install');
		
		$this->assign ( 'url_add', $url_add );
		
		$this->display ();
		
	}
	public function consume(){
		$this->display ();
		
	}
	public function order(){
		
		$this->display ();
		
	}
	//综合报告
	public function checks(){
		$sqm = 'HDLYY-WXKF00001';
		$ymlx = '1';		
		/*  serviced：服务ID，调用的接口名称，定义为GetPatBasicInfo(病人信息)，GetPatCheckResult(结果说明)，
		GetPatSingleGuide(导引单信息)，GetPatItemFee(费用信息) */
		$serviceid = 'GetPatItemFee'; 
		// 构造xml               
        $openid = get_openid();
        // get inhospno and name
        $Userinfo = M("Userinfo");
        $where['openid'] = $openid;
        $dataOrder = $Userinfo->where($where)->find();
        if(!empty($dataOrder['lyh'])){
            $inhospno = $dataOrder['lyh'];
            $name = $dataOrder['name'];
        }else{
            // test value 测试数据
            $inhospno = '2006053819';
			$name = '王正泉'; 
        }       
        // constitution 构造
        $getxml = $this->buildInstr($inhospno,$name);
        $instr = $getxml; 		
		$getres = $this->GetHisService($sqm,$ymlx,$serviceid,$instr,$inhospno,$name);
		$checks = $this->xml_to_array($res);
		$arr_check = $checks['body']['checkresult']['Record'];
        $this->assign ('check',$arr_check);		
		$this->display ();
		
	}
	public function basiccheck(){
		$this->display ();
		
	}
	public function basicindex(){
		$kes = '基础检查';
		$checks_value = $this->basic_check_back_ben($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function xx(){
		$getmd = I('md');
		$checks_value = $this->basic_check_back_ben($getmd);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function internal(){
		$kes = '内科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function wlzd(){
		$kes = '物理诊断科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function fs(){
		$kes = '放射科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function yebh(){
		$kes = '眼耳鼻喉科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function ebh(){
		$kes = '耳鼻喉科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function y(){
		$kes = '眼科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function kq(){
		$kes = '口腔科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function fu(){
		$kes = '妇科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	public function wai(){
		$kes = '外科';
		$checks_value = $this->basic_check_back($kes);
		$this->assign ('check',$checks_value);
		$this->display();
	}
	
    public function lists(){
    	$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
    	$row = empty ( $this->model ['list_row'] ) ? 10 : $this->model ['list_row'];
    	$order = M('Oreder');
     if (isset ($_REQUEST['inhospno'])) {
       	   $re=$_REQUEST ['inhospno'];
       	   $sql = "inhospno='$re'"; 
       }
       if (isset ($_REQUEST['inhospno'])&&isset ($_REQUEST['name'])) {
       	   $sd=$_REQUEST ['name'];
       	   $sql .= " and name like '%$sd%'";
       }
       if (!isset($_REQUEST['inhospno'])&&isset ($_REQUEST['name'])) {
         	$sd=$_REQUEST ['name'];
       	    $sql = "name like '%$sd%'";
       }
    	$count = $order->where($sql)->count ();
    	$lists = $order->where($sql)->order ('id DESC')->page ( $page, $row )->select ();
		print_r($lists);
    	if ($count > $row) {
    		$page = new \Think\Page ( $count, $row );
    		$page->setConfig ( 'theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
    		$this->assign ( '_page', $page->show () );
    	}
    	$this->assign('showRes',$lists);
    	$this->display();
    }
	
	
	
    public  function edit(){
    	$id = $_GET['id'];
    	$order = M('Oreder');
    	$sql = "id='$id'";
    	$lists = $order->where($sql)->select ();
    	$this->assign('id',$lists['0']['id']);
    	$this->assign('inhospno',$lists['0']['inhospno']);
    	$this->assign('name',$lists['0']['name']);
    	$this->assign('telphone',$lists['0']['telphone']);
    	$this->assign('idcard',$lists['0']['idcard']);
    	$this->assign('sex',$lists['0']['sex']);
    	$this->assign('age',$lists['0']['age']);
    	$this->assign('yytc',$lists['0']['yytc']);
		$this->assign('yyad',$lists['0']['yyad']);
    	$this->assign('yydata',$lists['0']['yydata']);
    	$this->assign('yysum',$lists['0']['yysum']);
    	$this->display();
    }
	
	
	//绑定
	public function binding_install(){
		$User = M("Userinfo");
		$openid=get_openid();
		$data['openid']=$openid;
		$data['lyh'] = I('order_id');
		$data['name'] = I('name');
		$data['cardID'] = I('adid');
		$check['cardID'] = I('adid');
		$imgSrc = $this->gwtWeixinImg();
		session('inhospno',$data['lyh']);
		$data['imgPath'] = $imgSrc;
		$where['openid'] = $openid;
		$data_u =  $User->where($where)->find();
		$checkSql['lyh'] = $data['lyh'];
		$repeatCheck = $User->where($checkSql)->find();
		if(!empty($repeatCheck['name'])){
			$this->error('用户已经绑定,若有疑问请拨打电话0510-82335000。');
		}
		//$data['inhospno'] = $data_u('lyh');
		if(!$data_u){
			// synchronous user 同步用户,author:ben
				$sqm = 'HDLYY-WXKF00001';
				$ymlx = '1';
				$serviceid = 'GetPatBasicInfo'; 
				// 构造xml				
				//$openid = get_openid();
				// get inhospno and name
				//$Order = M("Userinfo");
				//bind inhospne
				/* $where['openid'] = $openid;
				$dataOrder = $User->where($where)->find(); */
				if(!empty($data['lyh'])){
					$inhospno = $data['lyh'];
					$name = $data['name'];
				}else{
					// test value 测试数据 ben
					//$inhospno = '20060538191';
					/* $inhospno = '2006053819';
					$name = '王正泉'; */
				}		
				// constitution 构造
				$getxml = $this->buildInstr($inhospno,$name);
				/* var_dump($getxml); */
				$instr = $getxml;	
				$getres = $this->GetHisService($sqm,$ymlx,$serviceid,$instr,$inhospno,$name);
				// format xml to array 转换xml成array
				$resArr = $this->xml_to_array($getres);	
				// get reality content 获取主体内容
				$reportArr = $resArr['body'];
				if(!empty($reportArr)){
					$data['INHOSPNO'] = $reportArr['patinfo']['INHOSPNO'];
					$data['sex'] = $reportArr['patinfo']['SEX'];
					$data['phone'] = $reportArr['patinfo']['TELPHONE'];	
					$data['cardID'] = $reportArr['patinfo']['IDCARD'];
					$userDate = $data['cardID'];
					$cardID6 = substr($userDate,-6,6);
					$userDateu = substr($userDate,6,4);
					$age = date(Y) - $userDateu;
					$data['age'] = $age;
				}
				/* var_dump($data['cardID']);var_dump($cardID6);exit(); */
				//add user bind inhospno 用户绑定inhospno author:ben  date:2015.07.29
				if($data['lyh'] == $data['INHOSPNO'] AND $check['cardID'] == $cardID6){
					$res = $User->add($data);
					$url = U('/addon/Heart/Heart/particulars');
					$this->success('绑定成功！', $url);
				}else{				
					$this->error('请输入正确用户信息！');	
				}
			
			exit($res);
		}else{
			if($data_u['name']!=$data['name']||$data_u['cardID']!=$data['cardID']){
				 $url = U('/addon/Order/Order/binding/err/1');

				$this->error('请输入正确用户信息', $url);
				exit();
			}else{
				$url = U('/addon/Heart/Heart/index');

				header("Location:$url") ;
			}
			$url = U('/addon/Heart/Heart/index');

			header("Location:$url") ;
		}
	   
	   
		
	}
	public function test(){
		$sqm = 'HDLYY-WXKF00001';
		$ywlx = '1';		//serviced：服务ID，调用的接口名称，定义为GetPatBasicInfo(病人信息)，GetPatCheckResult(结果说明)，GetPatSingleGuide(导引单信息)，GetPatItemFee(费用信息)
		$serviceid = 'GetPatCheckResult'; 
		$instr;
		// 构造xml               
        $openid = get_openid();
        // get inhospno and name
        $Order = M("Userinfo");
        $where['openid'] = $openid;
        $dataOrder = $Order->where($where)->find();
        if(!empty($dataOrder['lyh'])){
            $inhospno = $dataOrder['lyh'];
            $name = $dataOrder['name'];
        }else{
            // test value 测试数据
             $inhospno = '2006053819';
            $name = '王正泉'; 
        }       
        // constitution 构造
        $getxml = $this->buildInstr($inhospno,$name);
        $instr = $getxml; 
		$res=$this->GetHisService($sqm,$ywlx,$serviceid,$instr,$inhospno,$name);
		$checks=$this->xml_to_array($res);
		var_dump($res);
	   exit;
   	   //$this->success('新增成功','/index.php?s=/addon/Order/Order/lists.html');
	}
	
	
   public function edits(){  
   	   $User = M("Oreder");
   	   $id=$_POST['id'];
   	   $data['inhospno'] = $_POST['inhospno'];
   	   $data['name'] = $_POST['name'];
   	   $data['sex'] = $_POST['sex'];
   	   $data['telphone'] = $_POST['telphone'];
   	   $data['idcard'] = $_POST['idcard'];
   	   $data['age'] = $_POST['age'];
   	   $data['yytc'] = $_POST['yytc'];
	   $data['yyad'] = $_POST['yyad'];
   	   $data['yydata'] = $_POST['yydata'];
   	   $data['yysum'] = $_POST['yysum'];
   	   $sql = "id='$id'";
   	   $res=$User->where($sql)->data($data)->save();
   	   $this->success('新增成功','/index.php?s=/addon/Order/Order/lists.html');
   }
   //转化字符串
   public function getstr($Record){
	   foreach($Record as $key){
		$resPost .= $key;
		}
	return $resPost;
   }
   //fasong 模版
   public function push() {	
	$openid = I('openid');  
	//$openid = 'oRY1nt1vxVOJJjnHd1Qx50rbXK2A';  
		//疗养号 
	$User = M("Oreder"); 	 
	$where['openid'] = $openid;
	$data = $User->where($where)->find();
	$inhospno = $data['inhospno'];

		//获取信息 
	$sqm = 'HDLYY-WXKF00001';
	$ywlx = '1';
	$serviceid = 'GetPatSingleGuide'; 
	$instr;
	$res = $this->GetHisService($sqm,$ywlx,$serviceid,$instr);
	$resArr = $this->xml_to_array($res);
	//处理信息
	$Record = $resArr['body']['CheckItem']['Record'];
		//测试 id
		$template_id = 'DjgQAQDNhPNHm2C4GRTaawCkCHPifSdTdmMRisBMAT8';
		$url = 'http://hdlyy.vboshi.cn/index.php?s=/addon/Heart/Heart/particulars';
		$first = '用户您好';		
		
		$keyword1 = $this->getstr($Record[0]);
		$keyword2 = $this->getstr($Record[1]);
		$keyword3 = $this->getstr($Record[2]);
		$keyword4 = $this->getstr($Record[3]);
		$remark = '来自华东疗养院';				
		$data = '{"first": {
                       "value":"'.$first.'",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$keyword1.'",
                       "color":"#ff0000"
                   },
                   "keyword2": {
                       "value":"'.$keyword2.'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$keyword3.'",
                       "color":"#173177"
                   },
				   "keyword4": {
                       "value":"'.$keyword4.'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"'.$remark.'",
                       "color":"#173177"
                   }
				}';	
		$doctor = $keyword3;
		$id = I('id');		
		$res = $this->sendtempmsg($openid, $template_id, $url, $data,$keyword2,$doctor,$id);
		if($res['errcode'] == 0){
			$dataupd['ostuats'] = '1';
			$User->where($where)->save($dataupd);
			$this->success('发送成功', U('lists'));
		}else{
			$this->error('发送失败');
		}
		
	}
	//发送模板消息
	public function sendtempmsg($openid, $template_id, $url, $data,$reply, $doctor,$id,$topcolor='#FF0000') {		
		$json = $this->get_access_token();		
		if ($json ['errcode'] == 0) {			
			$xml = '{"touser":"'.$openid.'","template_id":"'.$template_id.'","url":"'.$url.'","topcolor":"'.$topcolor.'","data":'.$data.'}';
			$res = $this->curlPost('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$json['access_token'],$xml);
			$res = json_decode($res, true);
			// 记录日志
			if ($res ['errcode'] == 0) {
				addWeixinLog ( $xml, 'post' );
			}
			$senddata = array(
				'openid' => $openid,
				'template_id' => $template_id,
				'MsgID' => $res['msgid'],
				'message' => $data,
				'sendstatus' => $res['errcode']==0 ? 0 : 1,
				'token' => get_token(),
				'ctime' => time(),
			);
			$upddoc['doctor'] = $doctor;
			$upddoc['reply'] = $reply;			
			$updid[id] = $id;			
			M('getmsg')->where($updid)->save($upddoc);
			M ('tmplmsg')->add ( $senddata );			
			return $res;
		}else{			
			return $json;
		}
		
	}
	

	public function curlPost($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		return $tmpInfo;
	}
	
    public function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
 public function get_access_token(){
	
		$map ['token'] = get_token ();
		$info = M ( 'member_public' )->where ( $map )->find ();
		$url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $info ['appid'] . '&secret=' . $info ['secret'];
		$data = json_decode($this->curlGet($url_get), true);
		if ($data ['errcode'] == 0) {

			return $data;
		}else{
		
			return $data;
		}
	}
	
	public function gwtWeixinImg(){	
	$openid = get_openid();
	$token = get_token();
	 $imgSrc = $this->getWeixinUserInfo($openid,$token);	
	 
	 return $imgSrc['headimgurl'];
	}
	
	public  function getWeixinUserInfo($openid, $token) {
    //    $param ['appid'] = $GLOBALS ['user'] ['appid'];
    //    $param ['secret'] = $GLOBALS ['user'] ['secret'];
        $param ['appid'] = "wxdd5ba0af12edb10a";
        $param ['secret'] = "8db2cf74ecf31b9f15f27d668c0b65f8";
        $param ['grant_type'] = 'client_credential';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query ( $param );
        $content = file_get_contents ( $url );
        $content = json_decode ( $content, true );
        
        $param2 ['access_token'] = $content ['access_token'];
        $param2 ['openid'] = $openid;
        $param2 ['lang'] = 'zh_CN';
        
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param2 );
        $content = file_get_contents ( $url );
        $content = json_decode ( $content, true );
        return $content;
    }
}
