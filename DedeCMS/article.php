<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>无锡市公立医院</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="/ben/css/index.css" />
    <style>
        .cha_txt {
    padding: 20px 10px;
    line-height: 24px;
    color: #333;
    font-size: 15px;
}
    </style>

    <base target="_blank" />
</head>

<body>
<?php
$tid = $_GET['tid'];
$id = $_GET['id'];
if(empty($tid)||empty($id)){
    echo '404';
    exit;
}
include 'temp/head.htm';
include 'lib/mysql.class.php';
$db = new MySQL();
?>
<div class="fy yahei">
    <h3><span><a href="/">主页</a>><?php $sql = "SELECT id,typename FROM `m120gw_arctype` WHERE id = $tid";$res = $db->query($sql);echo $res[0]['typename']; ?></a></span></h3>
    <div class="cha_txt">
		<?php $sql = "SELECT body FROM `m120gw_addonarticle` WHERE aid = $id";$res = $db->query($sql);echo $res[0]['body']; ?>
	</div>
</div>
<?php include 'temp/foot.htm'; ?>
</body>

</html>
