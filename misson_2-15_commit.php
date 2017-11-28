<?php
//データベース接続
try {

    $pdo = new PDO(
        'mysql:dbname = "dbname"; host = localhost; charset = utf8',
        '"username"',
        '"password"'
    );

    $sql = "CREATE TABLE IF NOT EXISTS "dbname".board_db
        (id int, name varchar(50), comment varchar(140), date datetime, password varchar(50));";
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();

}catch (PDOException $e) {
    var_dump($e);
}

$name = $_POST['name'];
$comment = $_POST['comment'];
$password = $_POST['password'];
$ediN = $_POST['ediN'];
$edit = $_POST['edit'];
$passwordE = $_POST['passwordE'];
$delN = $_POST['delN'];
$passwordD = $_POST['passwordD'];

//削除機能
if(isset($_POST['del'])) {
    try {
        $stmt = $pdo -> query ("SELECT password FROM "dbname".board_db");
        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $pass[] = $row['password'];
            $count = $stmt -> rowCount();
        }
        if ($passwordD == $pass[$delN - 1]) {
            $sql = "DELETE FROM "dbname".board_db where id = :delete_id;";
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':delete_id', $delN, PDO::PARAM_INT);
            $stmt -> execute();
        
            for ($i = $delN + 1; $i <= $count; $i++) {
                $sql = "UPDATE "dbname".board_db set id = :re_id where id = :id;";
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindValue(':id', $i, PDO::PARAM_INT);
                $stmt -> bindValue(':re_id', $i - 1, PDO::PARAM_INT);
                $stmt -> execute();
            }
        }

    } catch  (PDOException $e) {
        var_dump ($e);
    }

}

//編集機能
else if (isset($_POST['edi'])) {
    $edi = 1;
    try {
        $stmt = $pdo -> query ("SELECT password FROM "dbname".board_db");

        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $pass[] = $row['password'];
        }

        if ($passwordE == $pass[$ediN - 1]) {

            $stmt = $pdo -> query ("SELECT name, comment FROM "dbname".board_db");

            while ($row = $stmt -> fetch (PDO::FETCH_ASSOC)) {
                $ename[] = $row['name'];
                $ecomment[] = $row['comment'];
            }
            $userE = $ename[$ediN - 1];
            $wordE = $ecomment[$ediN - 1];

        }
    } catch  (PDOException $e) {
        var_dump ($e);
    }
}

else if ($edit == 1) {
    try {
        $ediN = $_POST['num'];
        $sql = "UPDATE "dbname".board_db set name = :name,
            comment = :comment WHERE id = :id;";
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam (':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam (':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindValue (':id', $ediN, PDO::PARAM_INT);
        $stmt -> execute();
    } catch (PDOException $e) {
        var_dump ($e);
    }

}    

//書き込み機能
else if(!empty($_POST['name'])) {
    try {

        //投稿番号の取得
        $stmt = $pdo -> query ("SELECT id FROM "dbname".board_db");
        while ($row = $stmt -> fetch (PDO::FETCH_ASSOC)) {
            $id = $row["id"];
        }
        $num  = $id + 1;
  
        //時刻の取得
        $time = date ('Y-m-d H:i:s');
	
        //フォームデータをデータベースに書き込む
        $stmt = $pdo -> prepare ("INSERT INTO "dbname".board_db
            (id, name, comment, date, password)
            values (:id, :name, :comment, :date, :password);");
        $stmt -> bindValue (':id', $num, PDO::PARAM_INT);
        $stmt -> bindParam (':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam (':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam (':date', $time, PDO::PARAM_STR);
        $stmt -> bindParam (':password', $password, PDO::PARAM_STR);

        $stmt -> execute();
    } catch  (PDOException $e) {
    var_dump($e);
}

    
}


?>

<!DOCTYPE html>
<html lang = 'ja'>

<head>
<meta charset = 'UTF-8'>
<title>フォームからデータを受け取る</title>
</head>

<body>
<form action = 'misson_2-15.php' method = 'post'>
名前<br>
<input type = 'text' name ='name' value = <?php echo htmlspecialchars($userE); ?>><br>
コメント<br>
<input type = 'text' name ='comment' value = <?php echo htmlspecialchars($wordE); ?>><br>
パスワード(設定)<br>
<input type = 'password' name = 'password'><br>
<?php
if ($edi == 1) {
    echo <<< EOM
<input type = 'hidden' name = 'edit' value = '1'>
<input type = 'hidden' name = 'num' value = $ediN>
EOM;

}
?>
<input type = 'submit' name = 'send' value ='送信'><br>
</form>
<br>
<form action = 'misson_2-15.php' method = 'post'>
編集したい投稿番号
<br>
<input type = 'text' name = 'ediN'>
<br>
パスワード(認証)<br>
<input type = 'password' name = 'passwordE'><br>
<input type = 'submit' name = 'edi' value = '編集'><br>
</form>
<br>
<form action = 'misson_2-15.php' method = 'post'>
削除したい投稿番号<br>
<input type = 'text' name ='delN'>
<br>
パスワード(認証)<br>
<input type = 'password' name = 'passwordD'><br>
<input type = 'submit' name = 'del' value ='削除'>
</form>

<br><br>
--<br>
投稿番号 | 名前<br>
コメント<br>
日時<br>
--
<br>
</body>

<?php
try {
    $stmt =$pdo -> query ("SELECT * FROM "dbname".board_db ORDER BY id");
    while ($visual = $stmt -> fetch (PDO::FETCH_ASSOC)) {
        $tid = htmlspecialchars ($visual["id"]);
        $tname = htmlspecialchars ($visual["name"]);
        $tcomment = htmlspecialchars ($visual["comment"]);
        $tdate = htmlspecialchars ($visual["date"]);

        echo <<< EOM
--<br>
$tid | $tname<br>
$tcomment<br>
$tdate<br>
--<br>
EOM;
    }
}catch (PDOException $e) {
    var_dump($e);
}

?>
</html>
