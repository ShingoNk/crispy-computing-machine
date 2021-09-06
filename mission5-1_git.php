<!DOCTYPE html>

<html lang="ja">

<head>

    <meta charset="UTF-8">

    <title>mission5-01</title>

</head>

<body>

<?php

$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)) ;

//テーブルが存在しなければ作成
$sql = "CREATE TABLE IF NOT EXISTS keiji"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "time TIMESTAMP,"
. "passward char(30)"
.");";
$stmt = $pdo->query($sql);

//　テーブルのデータを配列に保存する
$hyoji = 'SELECT * FROM keiji';
$stmt = $pdo->query($hyoji);
$results = $stmt->fetchAll();

//編集機能
if(!empty($_POST["nam"]) && !empty($_POST["comm"]) &&!empty($_POST["enum"])){
    foreach ($results as $row){
        $ata[] = $row['id'] ;
    }
   
    if(in_array($_POST["enum"],$ata)){  //投稿番号の中に編集番号が存在したら編集する
        $id = $_POST["enum"]; //変更する投稿番号
        $name = $_POST["nam"];
        $comment = $_POST["comm"]; //変更する内容
        $passward = $_POST["pass"] ;
        $time = date("Y/m/d H:i:s") ;
        $sql = 'UPDATE keiji SET name=:name,comment=:comment,time=:time,passward=:passward WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> bindParam(':time', $time, PDO::PARAM_STR);
        $stmt -> bindParam(':passward', $passward, PDO::PARAM_STR);
        $stmt->execute();
    }else{   //投稿番号の中に編集番号が存在しない時新規投稿に切り替える
        $new = $pdo -> prepare("INSERT INTO keiji (name, comment,time,passward) VALUES (:name, :comment, :time, :passward)");
        $new -> bindParam(':name', $name, PDO::PARAM_STR);
        $new -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $new -> bindParam(':time', $time, PDO::PARAM_STR);
        $new -> bindParam(':passward', $passward, PDO::PARAM_STR);
        $name = $_POST["nam"] ;
        $comment = $_POST["comm"] ;
        $time = date("Y/m/d H:i:s") ;
        $passward = $_POST["pass"] ;
        $new -> execute();
    }

//新規投稿
}elseif(!empty($_POST["nam"]) && !empty($_POST["comm"]) && !empty($_POST["pass"])){
    $new = $pdo -> prepare("INSERT INTO keiji (name, comment,time,passward) VALUES (:name, :comment, :time, :passward)");
    $new -> bindParam(':name', $name, PDO::PARAM_STR);
    $new -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $new -> bindParam(':time', $time, PDO::PARAM_STR);
    $new -> bindParam(':passward', $passward, PDO::PARAM_STR);
    $name = $_POST["nam"] ;
    $comment = $_POST["comm"] ;
    $time = date("Y/m/d H:i:s") ;
    $passward = $_POST["pass"] ;
    $new -> execute();

//削除機能   
}elseif(!empty($_POST["del"]) && !empty($_POST["delpass"])){
    foreach($results as $row){
        if($_POST["del"] == $row['id']) {   //削除番号とidが一致する配列の抽出
            if($_POST["delpass"] == $row['passward']){  //パスワードの一致確認
                $id = $_POST["del"];
                $delete = 'delete from keiji where id=:id';
                $stmt = $pdo->prepare($delete) ;
                $stmt -> bindParam(':id' , $id, PDO::PARAM_INT ) ;
                $stmt -> execute() ;
            }else{echo "パスワードが違います" ;}
        }
    }
    

}elseif(!empty($_POST["cnum"]) && !empty($_POST["edipass"])){
    foreach ($results as $row){   //編集番号とidが一致する配列の抽出
        if($_POST["cnum"] == $row['id']){
            if($_POST["edipass"] == $row['passward']) {  //パスワードの一致確認
                $chnum = $_POST["cnum"] ;
                $cname = $row['name'] ;
                $ccom = $row['comment'] ;
                $dpass = $row['passward'] ;
            }else{echo "パスワードが違います" ;}
        }
            
        
    }
}






?>

【投稿フォーム】
<form method="POST" action="">
    氏名　　　：<input type="text" required name="nam" 
                value="<?php if (isset($cname)){echo $cname;}?>" >
                <br>
	コメント　：<input type="text" required name="comm"
	            value="<?php if (isset($ccom)){echo $ccom ;}?>" >
                <br>
	パスワード：<input type = "text" required name = "pass"
	            value="<?php if (isset($dpass)){echo $dpass ;}?>"> <br>
	 <input type ="hidden" name="enum"
	  value="<?php if (isset($chnum)){echo $chnum;}?>" > 
	<input type="submit" name="submit" value="送信">
	<br>
</form>

【削除フォーム】
<form method="POST" action="">
	削除番号：<input type="number" required name="del"> 
	パスワード：<input type = "password" required name = "delpass"> <br>
	<input type="submit" name="submit" value="削除">
	<br>
</form>

【編集フォーム】
<form method="POST" action="">
	編集番号：<input type="number" required name="cnum">
	パスワード：<input type = "password" required name = "edipass"> <br>
	<input type="submit" name="submit" value="編集">
	<br>
	
</form>


<?php
 
//コメント表示
echo "<br>" ;
echo "<br>" ;
echo "【投稿一覧】" ;
echo "<hr>" ;

$hyoji = 'SELECT * FROM keiji';
$stmt = $pdo->query($hyoji);
$results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'. ';
        echo $row['name'].'，';
        echo $row['comment'].'  ';
        echo $row['time'].'<br>';
    echo "<hr>";
    }

?>


</body>
</html>