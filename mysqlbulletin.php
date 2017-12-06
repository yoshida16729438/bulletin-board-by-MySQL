<?php
session_start();//編集のためにデータを受け渡す必要がある
?>
<head>
<meta http-equiv="content-language" content="ja">
<meta charset="UTF-8">
</head>

<title>ここにタイトルが入ります</title>
<h1>ここにタイトルが入ります</h1>

<?php
try{
	$pdo=new PDO("mysql:host=ホスト名;dbname=データベース名;charset=utf8","ユーザー名","パスワード"); //接続
//	$sql="drop table if exists bulletin";
//	$pdo->query($sql);

	$sql="create table if not exists bulletin(id int primary key not null auto_increment,name varchar(20),comment varchar(100),password int(10),time varchar(20),onoff int(1));"; //テーブル作成
	$pdo->query($sql);
	
	if(isset($_SESSION["comnum"]))$val3=$_SESSION["comnum"];//編集時に必要なのか…？投稿番号受け取り
	
	if(isset($_POST["button1"])){//追加または編集のとき
		if($_POST["name"]==="")echo "</br>Input name.</br>";
		else if($_POST["comment"]==="") echo "</br>Input comment.</br>";
		else if($_POST["pass"]==="") echo "</br>Input password.</br>";
		else{
			$name=$_POST["name"];
			$comment=$_POST["comment"];
			$pass=$_POST["pass"];
			$time=date("Y/n/j G:i:s");//年/月/日 時:分:秒
			$com=$_POST["comnum"];//編集の時は番号が入っている
			if($_POST["comnum"]==null){ //追加のとき
				$sql="insert into bulletin(name,comment,password,time,onoff) values(:name,:comment,:pass,:time,1);";
				$insert=$pdo->prepare($sql);
				$insert->bindparam(":name",$name,pdo::PARAM_STR);
				$insert->bindparam(":comment",$comment,pdo::PARAM_STR);
				$insert->bindparam(":pass",$pass,pdo::PARAM_INT);
				$insert->bindparam(":time",$time,pdo::PARAM_STR);
				$insert->execute();

			}else{ //編集のとき
				$sql="select password from bulletin where id=$com";//編集指定されたコメントのパスワード取得
				$result=$pdo->query($sql);
				foreach($result as $row){
					if($row["password"]==$pass){
						$sql="update bulletin set name=:name,comment=:comment,time=:time where id=:id;";//パスワード正しければ編集
						$update=$pdo->prepare($sql);
						$update->bindparam(":name",$name,pdo::PARAM_STR);
						$update->bindparam(":comment",$comment,pdo::PARAM_STR);
						$update->bindparam(":time",$time,pdo::PARAM_STR);
						$update->bindparam(":id",$com,pdo::PARAM_STR);
						$update->execute();
					}else echo "<br/>Incorrect password.<br/>";
				}
				unset($_SESSION["comnum"]);
				unset($_SESSION["prevcom"]);
				unset($_SESSION["prevname"]);
			}
		}
	} else if(isset($_POST["button2"])){//削除のとき
		if($_POST["delete"]==="")echo "</br>Input delete number.</br>";
		else if($_POST["delpass"]==="")echo "</br>Input delete password.</br>";
		else{
			$delete=$_POST["delete"];//削除したい投稿番号
			$delpass=$_POST["delpass"];
			$sql="select id,onoff,password from bulletin";//全投稿のidと表示有無のみ取得
			$result=$pdo->query($sql);
			foreach($result as $row){
				if($row["id"]==$delete&&$row["onoff"]==1){//削除番号と一致する投稿が存在しかつ表示されてるか
					$exist=true;//削除したい投稿は存在するかどうか
					if($delpass==$row["password"]){
						$pdo->query("update bulletin set onoff=0 where id=$delete");
						echo "</br>Deleted No.$delete.<br/>";
					}else echo "</br>Incorrect password.<br/>";
				}
			}
			if($exist!=true) echo "</br>Comment No.$delete doesn't exist.</br>";
		}
	} else if(isset($_POST["button3"])){//編集指定されたコメントをフォームに返す
		if($_POST["enum"]==="")echo "</br>Input edit number.</br>";
		else {
			$enum=$_POST["enum"];//編集する番号を受け取る
			$sql="select id,name,comment,onoff from bulletin";
			$result=$pdo->query($sql);
			foreach($result as $row){
				if ($row["id"]==$enum&&$row["onoff"]==1){
					$_SESSION["prevname"]=$row["name"];
					$_SESSION["prevcom"]=$row["comment"];
					$_SESSION["comnum"]=$row["id"];
				}
			}
			if (!isset($_SESSION["comnum"])) echo "</br>Comment No.$enum doesn't exist.</br>";
		}
	}
	//ここまで本体
	
	$val1=null;//編集する投稿の名前を受け取る箱
	$val2=null;//編集するコメントを受け取る箱
	$val3=null;//編集するコメントの投稿番号を受け取る箱
	if (isset($_SESSION["prevname"]))$val1=$_SESSION["prevname"];//prevには編集するコメントのデータが入る
	if (isset($_SESSION["prevcom"]))$val2=$_SESSION["prevcom"];
	if (isset($_SESSION["comnum"]))$val3=$_SESSION["comnum"];
?>
	
	<form action="mission_2-15.php" method="post" />
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
	</form>
<?php
	$sql="select * from bulletin;";//全データ取得
	$all=$pdo->query($sql);
	foreach($all as $out){
		if($out["onoff"]==1){
			echo $out["id"].":".$out["name"].":".$out["time"]."<br/>".$out["comment"]."<br/><br/>";
		}
	}

}catch(PDOException $e){
	echo "error\n</br>";
}
$pdo=null;

?>