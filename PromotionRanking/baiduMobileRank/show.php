<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
	</head>	
	<body>
		<h4>竞价排名查询</h4>
		<table class="table table-hover">
		<tr>
			<th>关键词</th>
			<th>出现链接</th>			
			<th>排名</th>			
		</tr>
		<?php
		if(!empty($rankVal_arr)){
			foreach($rankVal_arr as $rKey=>$rVal){
				if($rVal['rank'] != 'no'){
					echo '<tr class="success">';
				}else{
					echo '<tr>';	
				}				
				echo '<td>'.$rVal['keyword'].'</td>';
				echo '<td>'.$rVal['url'].'</td>';
				echo '<td>'.$rVal['rank'].'</td>';
				echo '</tr>';
			}
		}	
		?>
		</table>
	</body>
</html>