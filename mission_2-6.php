<?php
session_start();//編集のためにデータを受け渡す必要がある
?>

<title>ここにタイトルが入ります</title>
<h1>ここにタイトルが入ります</h1>

<?php 
$filename="out2-6.txt";//出力先
$fp=fopen($filename,"a+");
$array=file($filename);
$numofarray=count($array);//データ行数
if($numofarray>0){
	$divide=explode("<>",$array[$numofarray-1]);
	$last=$divide[0];
$val3=$_SESSION["comnum"];
$val4=$_SESSION["arraynum"];
}
$last++;//ここまででtxtファイル内の最終行の投稿番号取得
if (isset($_POST["button1"])){//追加または編集のとき
	$name=$_POST["name"];
	$comment=$_POST["comment"];
	$pass=$_POST["pass"];
	$time=date("Y/n/j G:i:s");//年/月/日 時:分:秒
	$com=$_POST["comnum"];
	if($_POST["comnum"]==null){ //追加のとき
		fwrite($fp,"$last<>$name<>$comment<>$time<>$pass<>".PHP_EOL);
	}else{ //編集のとき
		$divide=explode("<>",$array[$val4]);
		if($divide[4]==$pass){
			ftruncate($fp,0);
			for($out=0;$out<$val4;$out++) fwrite($fp,$array[$out]);
			fwrite($fp,"$val3<>$name<>$comment<>$time<>$pass<>".PHP_EOL);
			for($out=$val4+1;$out<$numofarray;$out++) fwrite($fp,$array[$out]);
		}else{
			echo "</br>Incorrect password.</br></br>";
		}
		unset($_SESSION["comnum"]);
		unset($_SESSION["prevcom"]);
		unset($_SESSION["prevname"]);
		unset($_SESSION["arraynum"]);
		}

} else if (isset($_POST["button2"])){//削除のとき
	$delete=$_POST["delete"];
	$delpass=$_POST["delpass"];
	$num=0;
	foreach($array as $data){
		$divide=explode("<>",$data);
		if($divide[0]==$delete) $delnum=$num;//削除番号を行番号に変換
		$num++;
	}
	if(!isset($delnum)){
		echo "</br>Comment No.$delete doesn't exist.</br>";
	}else{
		$divide=explode("<>",$array[$delnum]);
		if($delpass==$divide[4]){
			ftruncate($fp,0);//ファイルを白紙に
			for($out=0;$out<$delnum;$out++) fwrite($fp,$array[$out]);
			for($out=$delnum+1;$out<$numofarray;$out++)fwrite($fp,$array[$out]);
			echo "deleted No.$delete.";//報告
		}else {
			echo "</br>Incorrect password.</br></br>";
		}
	}
}else if (isset($_POST["button3"])){//編集指定されたコメントをフォームに返す
	$enum=$_POST["enum"];//編集する番号を受け取る
	$num=0;
	if(isset($enum)){
		foreach($array as $data){
			$divide=explode("<>",$data);
			if ($divide[0]==$enum){
				$_SESSION["prevname"]=$divide[1];//名前を返す
				$_SESSION["prevcom"]=$divide[2];//コメントを返す
				$_SESSION["arraynum"]=$num;//編集したいコメントが何行目かを返す
				$_SESSION["comnum"]=$enum;//編集したいコメントの番号を返す
			}
			$num++;
		}
		if(!isset($_SESSION["arraynum"])) echo "</br>Comment No.$enum doesn't exist.</br></br>";
	}else {
		echo "</br>Input edit number.</br></br>";
	}
}
$val1=null;//編集する投稿の名前を受け取る箱
$val2=null;//編集するコメントを受け取る箱
$val3=null;//編集するコメントの投稿番号を受け取る箱
$val4=null;//編集するコメントのテキストファイル内の行番号を受け取る箱
if (isset($_SESSION["prevname"]))$val1=$_SESSION["prevname"];//prevには編集するコメントのデータが入る
if (isset($_SESSION["prevcom"]))$val2=$_SESSION["prevcom"];
if (isset($_SESSION["comnum"]))$val3=$_SESSION["comnum"];
if (isset($_SESSION["arraynum"]))$val4=$_SESSION["arraynum"];
?>

<form action="mission_2-6.php" method="post" />
<p><label>名前:<input type="text" name="name" size=10 value="<?php echo $val1; ?>" /></label> <!--名前-->
&nbsp;&nbsp;&nbsp;

<label>コメント:<input type="text" name="comment" size=40 value="<?php echo $val2; ?>" /></label><!--コメント-->
&nbsp;&nbsp;&nbsp;

<label>パスワード:<input type="password" name="pass" size=20 /></label> <!--投稿時パスワード-->

&nbsp;<button type="submit" name="button1" style="WIDTH:60px;HEIGHT:20px">add</button></p> <!--投稿ボタン-->

<p><label>削除したい番号を入力:<input type="text" name="delete" size=5 /></label><!--削除-->
&nbsp;&nbsp;&nbsp;<label>パスワード:<input type="password" name="delpass" size=20 /></label><!--削除パスワード-->
&nbsp;<button type="submit" name="button2" style="WIDTH:60px;HEIGHT:20px">delete</button></p>

<p><label>編集したい番号を入力:<input type="text" name="enum" size=5 /></label><!--編集-->
&nbsp;<button type="submit" name="button3" style="WIDTH:60px;HEIGHT:20px">edit</button></p>

<input type="hidden" name="comnum" value="<?php echo $val3 ?>" /> <!--編集する投稿番号-->
<input type="hidden" name="arraynum" value="<?php echo $val4 ?>" /> <!--編集する投稿のメモ帳内での行番号-->
<?php

$array=file($filename);
foreach($array as $data){
	$divide=explode("<>",$data);
	echo "$divide[0] : $divide[1] : $divide[2] : $divide[3]<br/>";//番号名前コメント時間の順に出力
}

fclose($fp);
?>