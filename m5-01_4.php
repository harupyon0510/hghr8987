<?php
// DB接続設定
$dsn = 'mysql:dbname=tb230910db;host=localhost';
$user = 'tb-230910';
$password = 'yNQ9M32naa';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    
    //フラグの初期化（エラー文の表示用）
    $flag_null = 0;
    $flag_fulse = 0;
    $flag_pass = 0;
    $flag_num = 0;
    
    //編集フォーム
    if(isset($_POST["EDIT"])){ //編集ボタン押した
        if(!empty($_POST["edit_num"])){ //編集番号ある
            if(!empty($_POST["edit_pass"])){ //パスワードある
            
                //データの受け取り
                $edit_num = $_POST["edit_num"];
                $edit_pass = $_POST["edit_pass"];
                
                //パスワードチェック
                $sql = 'SELECT * FROM exam where id=:id'; //編集番号の行だけ選択
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT); //編集番号をセット
                $stmt->execute();
                $results = $stmt->fetchAll();
                
                if(!isset($results[0])){ //編集対象が存在しないとき
                    $flag_null = 1;
                }
                
                foreach ($results as $row){
                    if($row["pass"] == $edit_pass){ //パスワードチェック
                        $num_e = $row["id"];
                        $name_e = $row["name"];
                        $comment_e = $row["comment"];
                    }else $flag_fulse = 1;
                }
                
            }else $flag_pass = 1;
        }else $flag_num = 1;
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>編集フォーム</title>
</head>
<body>
    <form action = "" method = "post">
        【投稿フォーム】<br>
        <input type = "text" name = "name" value = "<?php if(isset($name_e))echo $name_e; ?>" placeholder = "名前"> <br>
        <input type = "text" name = "comment" value = "<?php if(isset($comment_e))echo $comment_e; ?>" placeholder = "コメント"> <br>
        <input type = "hidden" name = "num_e" value = "<?php if(isset($num_e))echo $num_e; ?>">
        <input type = "text" name = "input_pass" placeholder = "パスワード"> <br>
        <input type = "submit" name = "SEND" value = "投稿"> <br>
        
        【削除フォーム】<br>
        <input type = "text" name = "del_num" placeholder = "削除対象番号"> <br>
        <input type="text" name="del_pass" placeholder = "パスワード"> <br>
        <input type = "submit" name = "DEL" value = "削除"> <br>
        
        【編集フォーム】<br>
        <input type = "text" name = "edit_num" placeholder = "編集対象番号"> <br>
        <input type="text" name="edit_pass" placeholder = "パスワード"> <br>
        <input type = "submit" name = "EDIT" value = "編集"> <br>
    </form>
    <br>
   <?php
   // DB接続設定
    $dsn = 'mysql:dbname=tb230910db;host=localhost';
$user = 'tb-230910';
$password = 'yNQ9M32naa';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //投稿フォーム
    if(isset($_POST["SEND"])){ //投稿ボタンを押した
       if(!empty($_POST["name"])){ //名前　有
           if(!empty($_POST["comment"])){ //コメント　有
                if(!empty($_POST["input_pass"])){ //パスワード　有
                    if(empty($_POST["num_e"])){ //編集番号　無（投稿）
                        //投稿機能
                        //データの受け取り
                        $name = $_POST["name"]; //名前
                        $comment = $_POST["comment"]; //コメント
                        $date = date("Y/m/d H:i:s"); //投稿日時
                        $pass = $_POST["input_pass"]; //パスワード
                        
                        //テーブルが無いとき、テーブルを作成
                        $sql = "CREATE TABLE IF NOT  EXISTS exam"
                        ." ("
                        . "id INT AUTO_INCREMENT PRIMARY KEY," //投稿番号
                        . "name char(128)," //名前
                        . "comment TEXT," //コメント
                        . "date char(32)," //日付
                        . "pass char(32)" //パスワード
                        .");";
                        $stmt = $pdo -> query($sql); 
                         
                        //INSERT データを入力
                        $sql = $pdo -> prepare("INSERT INTO exam (name, comment,date,pass) VALUES (:name, :comment, :date, :pass)"); 
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        $sql -> execute();
                    }else{
                        //編集機能
                        //データの受け取り
                        $name = $_POST["name"]; //名前
                        $comment = $_POST["comment"]; //コメント
                        $date = date("Y/m/d H:i:s"); //投稿日時
                        $pass = $_POST["input_pass"]; //パスワード
                        $num_e = $_POST["num_e"]; //編集番号
                        
                        //UPDATE テーブルのデータを編集
                        $sql = 'UPDATE exam SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $num_e, PDO::PARAM_INT);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                 
                }else echo "パスワードを入力してください<br><hr>";
            }else echo "コメントを入力してください<br><hr>";
        }else echo "名前を入力してください<br><hr>";
    }
    
    //削除フォーム   
    if(isset($_POST["DEL"])){ //削除ボタン押した
        if(!empty($_POST["del_num"])){ //削除番号がある
            if(!empty($_POST["del_pass"])){ //パスワードがある
                
                //データの受け取り
                $del_num = $_POST["del_num"];
                $del_pass = $_POST["del_pass"];
                
                //パスワードチェック
                $sql = 'SELECT * FROM exam where id=:id'; //削除番号の行だけ選択
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $del_num, PDO::PARAM_INT); //削除番号をセット
                $stmt->execute();
                $results = $stmt->fetchAll();
                
                if(!isset($results[0])){ //削除対象が存在しないとき
                    echo "削除対象が存在しません<br><hr>";    
                }
                
                foreach ($results as $row){
                    if($row["pass"] == $del_pass){ //パスワードチェック
                        //DELETE　削除
                        $sql = 'delete from exam where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $del_num, PDO::PARAM_INT);
                        $stmt->execute();
                    }else echo "パスワードが間違っています<br><hr>";
                }
                
            }else echo "パスワードを入力してください<br><hr>";
        }else echo "削除対象番号を入力してください<br><hr>";
    }
    
    //編集フォームのエラー文表示（ここに書かないとフォームの上に文が表示されてしまうため）
    if($flag_null == 1){
        echo "編集対象が存在しません<br><hr>";
    }else if($flag_fulse == 1){
        echo "パスワードが間違っています<br><hr>";
    }else if($flag_pass == 1){
        echo "パスワードを入力してください<br><hr>";
    }else if($flag_num == 1){
        echo "編集番号を入力してください<br><hr>";
    }
    
    //掲示板をブラウザに表示
    //SHOW　テーブルの存在を確認
    $sql = "SHOW TABLES";
    $result = $pdo -> query($sql);
    foreach($result as $row){
        //SELECT データの表示
        if($row[0] == "exam"){
            $sql = 'SELECT * FROM exam';
            $stmt = $pdo->query($sql);
            $results2 = $stmt->fetchAll();
            foreach ($results2 as $row){
                echo "【". $row['id'].'】';
                echo $row['name'].' :  ';
                echo $row['comment'].'  ';
                echo $row['date'].',  ';
                echo $row['pass'].'<br>';
                echo "<hr>";
            }
        }
    }
    ?>
</body>
</html>