<?php
$filename = 'kadai2-6.txt';
$edit = ($_POST['edit']);

//削除希望の時
if(isset($_POST['del'])) {
    //フォームデータの取得
    $delN = $_POST['delN'];
    
    //ファイルを追記モードで開く
    $fp = fopen($filename, 'a+');

    //テキストファイルを配列化
    $filearr = file($filename);

    //配列をループして区切り文字で区切る
    foreach ($filearr as $tmp) {
        list($num[], $user[], $word[], $date[], $password[]) = explode("<>", $tmp);
    }

    $delnum = array_search($delN, $num);
    $pass = $_POST['passwordD'];
    $passread = rtrim($password[$delnum], "\n");
    if ($passread == $pass) {

        //削除の実行
        unset($filearr[$delnum]);
        unlink($filename);
    
        foreach ($filearr as $tmp) {
            list($nnum[], $nuser[], $nword[], $ndate[], $npassword[]) = explode("<>", $tmp);
        }

        $fp = fopen($filename, 'a+');
        for($i = 0; $i <= count($nnum) - 1; $i++) {
            fwrite($fp, ($i + 1)."<>".$nuser[$i]."<>".$nword[$i]."<>".$ndate[$i]."<>".$npassword[$i]);

        }
        fclose($fp);
    }
}


//編集時の動作
else if (isset($_POST['edi'])) {
    $ediN = $_POST['ediN'];
    //ファイルを追記モードで開く
    $fp = fopen($filename, 'a+');

    //テキストファイルを配列化
    $filearr = file($filename);

    //配列をループして区切り文字で区切る
    foreach ($filearr as $tmp) {
        list($num[], $user[], $word[], $date[], $password[]) = explode("<>", $tmp);
    }

    //目当ての投稿番号の検索
    $edinum = array_search($ediN, $num);
    $pass = $_POST['passwordE'];
    $passread = rtrim($password[$edinum], "\n");
    if ($passread == $pass) {

        //該当部分を変数に格納
        $userE = $user[$edinum];
        $wordE = $word[$edinum];
        $edi = 1;

    }

    fclose($fp);
}

else if ($edit == 1) {
    $fp = fopen($filename, 'a+');
    $edinum = ($_POST['ediN']) - 1;
   
   //テキストファイルを配列化
    $filearr = file($filename);

    //配列をループして区切り文字で区切る
    foreach ($filearr as $tmp) {
        rtrim($tmp, "\n");
        list($num[], $user[], $word[], $date[], $password[]) = explode("<>", $tmp);
    }

    array_splice($user, $edinum, 1, ($_POST['name']));
    array_splice($word, $edinum, 1, ($_POST['comment']));

    unlink($filename);

    $fp = fopen($filename, 'a+');
    for($i = 0; $i <= count($num) - 1; $i++) {
        fwrite($fp, $num[$i]."<>".$user[$i]."<>".$word[$i]."<>".$date[$i]."<>".$password[$i]);

    }
 
    fclose($fp);

}


//名前欄に記述がある場合のみ動作
else if(!empty($_POST['name'])) {
    
	//フォームデータの取得
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $password = (string)$_POST['password'];

	//ファイルを追記モードで開く
    $fp = fopen($filename, 'a+');

    //投稿番号の取得
    $filearr = file($filename);
    list($number, $user, $word, $date, $pass) = explode("<>", end($filearr));
    $num = $number[0] + 1;
        
    //時刻の取得
    $time = date('Y m/d H:i:s');
	
    //フォームデータをファイルに書き込む
    fwrite($fp, $num."<>".$name."<>".$comment."<>".$time."<>".$password."\n");
	
    //ファイルを閉じる
    fclose($fp);

}

?>

<!DOCTYPE html>
<html lang = 'ja'>

<head>
<meta charset = 'UTF-8'>
<title>フォームからデータを受け取る</title>
</head>

<body>
<form action = 'misson_2-6.php' method = 'post'>
名前<br>
<input type = 'text' name ='name' value = <?php echo htmlspecialchars($userE); ?>><br>
コメント<br>
<input type = 'text' name ='comment' value = <?php echo htmlspecialchars($wordE); ?>><br>
パスワード(設定)<br>
<input type = 'password' name = 'password'><br>
<?php
if ($edi == 1) {
    echo "<input type = 'hidden' name = 'edit' value = '1'>";
}
?>
<input type = 'submit' name = 'send' value ='送信'><br>

<br>
編集したい投稿番号
<br>
<input type = 'text' name = 'ediN' value = '<?php echo htmlspecialchars($ediN); ?>'>
<br>
パスワード(認証)<br>
<input type = 'password' name = 'passwordE'><br>
<input type = 'submit' name = 'edi' value = '編集'><br>

<br>
削除したい投稿番号<br>
<input type = 'text' name ='delN'>
<br>
パスワード(認証)<br>
<input type = 'password' name = 'passwordD'><br>
<input type = 'submit' name = 'del' value ='削除'>
</form>

<br><br>
投稿番号 名前 コメント 日時
<br>
</body>

</html>
<?php

//配列を出力
//ファイルが存在する場合のみ動作
if (file_exists($filename)) {

    $fr = fopen($filename, 'r');

    //テキストファイルを配列化
    $filearr = file($filename);

    //配列をループして区切り文字で区切る
    foreach ($filearr as $tmp) {
        list($numP[], $userP[], $wordP[], $dateP[], $passwordP[]) = explode("<>", $tmp);
    }

    for($i = 0; $i <= count($numP); $i++) {
        $line = $numP[$i]." ".$userP[$i]." ".$wordP[$i]." ".$dateP[$i];
        echo htmlspecialchars($line).'<br>';
    }
    
    fclose ($fr);
}
?>
