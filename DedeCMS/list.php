<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>无锡市公立医院</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="/ben/css/index.css" />
	<style>
	.fy { overflow:hidden; zoom:1; border:#dddddd 1px solid; background:#fff; padding:0px 7px 4px; }
.fy h3 { height:34px; border-bottom:#e7e7e7 1px solid; }
.fy h3 span { border-bottom:#0254a8 2px solid; font-size:14px; line-height:33px; color:#524e4e; display:inline-block; }
.fy h3 span a { color:#524e4e; }
.fy ul { overflow:hidden; zoom:1; padding:0 0px 17px; }
.fy ul li { overflow:hidden; zoom:1; margin-top:5px; }
.fy ul li i img{ width:105px; height:135px;}
.fy ul li dl { float:left; width:90px; height:87px; background:#0254a8; text-align:center; margin-right:2px; display:inline; }
.fy ul li dl a { color:#fff; }
.fy ul li dl dt { text-align:center; padding-top:15px; }
.fy ul li dl dd { line-height:30px; font-size:18px; color:#ffffff; }
.fy_c { overflow:hidden; zoom:1; background:#f5f5f5; height:87px; }
.fy_c a { background:#f5f5f5; display:block; height:87px; }
.fy_c a:hover { background:#fff1f0; display:block; }
.fy_c a h2 { line-height:30px; font-size:14px; color:#0254a8; font-weight:bold; display:block; margin:0px 5px; padding-top:5px; border-bottom:#c5c5c5 1px dashed; overflow:hidden; zoom:1; }
.fy_c a p { padding:0px 7px; color:#000000; line-height:24px; }
.fy_c a span { display:block; clear:right; float:right; }
	</style>
	
    <base target="_blank" />
</head>

<body>
<?php
$tid = $_GET['tid'];
if(empty($tid)){
    echo '404';
    exit;
}
include 'temp/head.htm';
include 'lib/mysql.class.php';
$db = new MySQL();
?>
<div class="fy yahei">
    <h3><span><a href="/">主页</a>><?php $sql = "SELECT typedir,typename FROM `m120gw_arctype` WHERE id = $tid";$res = $db->query($sql);echo $res[0]['typename']; ?></a></span></h3>
    <ul>
		<?php $sql = "SELECT id,typename FROM `m120gw_arctype` WHERE reid = $tid ORDER BY `id`";$res = $db->query($sql);foreach($res as $key=>$val){ ?>
        <li>
            <dl>
                <dt><img src="/ben/img/<?php echo $val['typename'];?>.png"></dt>
                <dd id="listname"><?php echo $val['typename']; ?></dd>
            </dl>
            <div class="fy_c">
				<?php $sqlarc = 'SELECT id,title,description FROM `m120gw_archives` WHERE typeid = '.$val['id'].' limit 0,1';$resac = $db->query($sqlarc);$resac = $resac[0]?>
             	   <a href="article.php?id=<?php echo $resac['id'] ?>&tid=<?php echo $tid; ?>" title="<?php echo $resac['title'] ?>" target="_blank">
                    <h2>·<?php echo $resac['title'] ?></h2>
                    <p><?php echo $resac['description'] ?><font class="red">【详细】</font>&nbsp;&nbsp;</p>
                </a>

            </div>
        </li>
       <?php }?>
    </ul>
</div>
<?php include 'temp/foot.htm'; ?>
</body>

</html>
