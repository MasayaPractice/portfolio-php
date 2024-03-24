<?php
ini_set('display_errors', "ON"); 

require_once("./config.php");
require("./common.php");
require_once("./page.php");


class RegisterationPage extends Page{
    public function execute(){  
        $this->register();        
    }
    public function register(){

         //URLパラメータを変数に代入
        if(isset($_GET["name_kanji"])){
            $kanji = $_GET['name_kanji'];
        }else{
            $kanji = "";
        }

        if(isset($_GET["name_katakana"])){
            $kana = $_GET['name_katakana'];
        }else{
            $kana = "";
        }

        
        if(isset($_GET['birthday'])){
            $date = $_GET['birthday'];
        }else{
            $date = "";
        }
                if(isset($_GET['email'])){
            $email = $_GET['email'];  
        }else{
            $email = "";
        }

        if(isset($_GET['password'])){
            $password = $_GET['password'];
        }else{
            $password = "";
        }





        //エラーメッセージを入れておく空配列 を作成
        $error_messages = [];

        //漢字のバリデーションチェック
        if(empty($kanji)){   
            $error_messages[] = "氏名の入力は必須項目です";  
        }elseif(mb_strlen($kanji,'UTF-8')< 20){    
            echo '正しい値が入力されました<br>';    
        }else{
            $error_messages[] = "20字以内で入力してください\n"; 
        }

        //カタカナかどうかのバリデーションチェック
        if(empty($kana)){
            $error_messages[] = "カタカナの入力は必須項目です。";
        }else if (! preg_match("/\A[ァ-ヴー]+\z/u", $kana )){  
            $error_messages[] =  "カタカナで入力ください\n"; 
        }else if (mb_strlen($kana,'UTF-8') >20){
            $error_messages[] =  "20字以内で入力ください\n"; 
        }else{
            echo "カタカナが正しく入力されています。<br>";
        }


        //誕生日バリデーションチェック
        $datetype = date_parse_from_format("Y-n-j", $date); 
        if(empty($date)){ 
            $error_messages[] =  '生年月日の入力は必須項目です。'; 
        }elseif(mb_strlen($date,'UTF-8') >20){
            $error_messages[] =  "生年月日は20字以内で入力ください"; 
        }elseif($datetype["warning_count"] != 0 || $datetype["error_count"] != 0){  
            $error_messages[] = "日付が間違っています。YYYY-mm-dd形式で入力してください";    
        }else{
            $sql = "SELECT * FROM  persons WHERE birthday = '$date'"; 
            
            
            $result = mysqli_query($this->db_link, $sql);   
            while ($row = mysqli_fetch_assoc($result)){
                $error_messages[] = "指定された生年月日は既に登録されています。別の生年月日で入力してください";
                break;
            } 
        }

        //メールアドレスのチェック
        if(empty($email)){
            $error_messages[] = 'メールアドレスの入力は必須項目です。'; 
        }else if (mb_strlen($email,'UTF-8') >20){
            $error_messages[] =  "20字以内で入力ください"; 
        }else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){  
            $error_messages[] = "正しいメールアドレスを入力ください";  
        }else if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            $sql = "SELECT * FROM `persons` WHERE email = '$email'";  
            $result = mysqli_query($this->db_link, $sql); 
            while ($row = mysqli_fetch_assoc($result)){           
                $error_messages[] ="指定されたメールアドレスは既に登録されています。別のメールアドレスを指定してください。";
            break; 
            }
        }else{
        echo  "正しい形式のメールアドレスです" ;    
        }

        
        //エラーメッセージの値が一つでもあるか判定
        if(count($error_messages) > 0){
            echo login().'さんでログインしています<br>';
            echo "<p>";
            foreach($error_messages as $error_message){
            echo $error_message."<br>"; 
            }
            echo '<a href="./registeration_inheritance.php"><button>登録画面に戻る</button></a>';
            exit;   
        }

        //validationを経て入力された正しいデータをDBに格納する
        
        $escaped_kanji = mysqli_real_escape_string($this->db_link,$kanji ) ;
        $sql = "INSERT INTO persons (name_kanji, name_katakana, birthday, email, password) VALUES ('"
        . $escaped_kanji                      
        ."', " 
        ."'"   
        . $kana                       
        ."', "  

        ."'"                                         
        . $date
        ."', " 

        ."'" 
        . $email 
        ."',"   
        ."'" 
        .$password                                            
        ."')";                                     
        

        //SQLを実行
        $result = mysqli_query($this->db_link, $sql);
    }


}


$page = new RegisterationPage();
$page->invoke();



?>


<html>
<p><?php login(); ?>さんでログインしています</p>
<a href="./register_form.php">
    <button type="button">登録画面にもどる</button>
</a>

</html>



