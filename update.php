<?php
ini_set('display_errors', "ON"); 
ini_set('error_reporting', E_ALL);


//外部ファイル取り込み
require_once("./config.php");
require_once("./common.php");
require_once("./page.php");


//データ更新
class UpdatePage extends Page{
    public function execute(){
        $this->update();
    }

    public function update(){
        //Getパラメータの取得
        $kanji = @$_GET['name_kanji'];
        $kana  = @$_GET['name_katakana'];
        $birthday  = @$_GET['birthday'];
        $email = @$_GET['email'];
        $id = @$_GET['id'];


        //エラーメッセージを入れておく空配列を作成
        $error_messages = [];

        //漢字のバリデーションチェック
        if(empty($kanji)){
            $error_messages[] = "氏名の入力は必須項目です。\n";   
        }elseif(mb_strlen($kanji,'UTF-8') > 20){
            $error_messages[] = "漢字は20字以内で入力してください。\n";                       
        }

        //カタカナのバリデーションチェック
        if(empty($kana)){
            $error_messages[] = "カタカナの入力は必須項目です。\n";
        }elseif(! preg_match("/\A[ァ-ヴー]+\z/u",$kana)){
            $error_messages[] = "カタカナで入力してください。\n";  
        }elseif(mb_strlen($kana,'UTF-8') >20){
            $error_messages[] = "カタカナは20字以内で入力してください。\n";  
        }

        //誕生日のバリデーションチェック
        $birthday_type = date_parse_from_format("Y-n-j", $birthday);    
        if(empty($birthday)){
            $error_messages[] = "生年月日の入力は必須項目です。\n";
        }elseif(mb_strlen($birthday,'UTF-8') > 20){
            $error_messages[] = "生年月日は20字以内で入力してください。\n";
        }elseif($birthday_type["warning_count"] != 0 || $birthday_type["error_count"]){
            $error_messages[] = "日付が間違っています。YYYY-mm-dd形式で入力してください"; 
        }else{
            //重複チェック
            // 編集対象のレコードを id から取得
            $sql = "SELECT * FROM persons WHERE id ='$id'";
            
            var_dump($sql);
            
            $db_link = $this->db_link;
            $result = $db_link->query($sql);
            var_dump($result);
            $row_id = $result->fetch_assoc();

            //既存のDBの同idからレコードを取得
            $sql = "SELECT * FROM persons WHERE birthday ='$birthday'";
            $db_link = $this->db_link;
            $result = $db_link->query($sql);

            $row_birthday = $result->fetch_assoc();

            // 編集対象のレコード（'birthday'）と、既存のDBの同idからのレコード（'birthday'）が同じものかをチェック
            if($row_birthday && ($row_id["birthday"] !== $row_birthday["birthday"])){
                $error_messages[] ="この生年月日すでに登録されています";
            }
        }

        //メールアドレスのチェック
        if(empty($email)){
            $error_messages[] = 'メールアドレスの入力は必須項目です。';
        }elseif(mb_strlen($email, 'UTF-8') > 20){
            $error_messages[] =  "メールアドレスは20字以内で入力ください";
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error_messages[] = "正しいメールアドレスを入力ください";
        }elseif(filter_var($email, FILTER_VALIDATE_EMAIL)){
            //重複チェック
            // 編集対象のレコードを id から取得
            $sql = "SELECT * FROM persons WHERE id ='$id'";
            $db_link = $this->db_link;
            $result = $db_link->query($sql);
            $row_id = $result->fetch_assoc();

            // 既存のDBの同idからレコード（'email'）を取得
            $sql = "SELECT * FROM persons WHERE email ='$email'";
            $db_link = $this->db_link;
            $result = $db_link->query($sql);
            $row_email = $result->fetch_assoc();

             // 編集対象のレコード(email)と、既存のdbの同レコード('email)が同じものかをチェック
             if($row_email && $row_id['email']!= $row_email['email']){
                $error_messages[] ="このメールアドレスはすでに登録されています";
             }
        }

        //エラーメッセージの値が一つでもあるか判定
        if(count($error_messages) > 0){
            echo "<p>";
            foreach($error_messages as $errormessage){
                echo $errormessage."<br>";
            }
            echo "</p>";
            exit;    
        }
        
        //バリデーションを経て入力された正しい値をDBに格納
        $escaped_kanji = mysqli_real_escape_string($db_link, $kanji);
        $sql = "UPDATE `persons` SET `name_kanji` = '"
        .$kanji
        ."', `name_katakana` = '"
        .$kana
        ."', `birthday` = ' "
        .$birthday
        ."',  `email` = '"
        .$email
        ." ' WHERE `id` = "
        .$id; 

        $db_link = $this->db_link;
        $result = $db_link->query($sql);

        echo '更新しました';

    }

}

$update = new UpdatePage();
$update->invoke();

?>
<html>
<p><?php echo login(); ?>さんでログインしています</p> 
<a href="./register_form.php">
    <button type="button">登録画面にもどる</button>
</a>

</html>