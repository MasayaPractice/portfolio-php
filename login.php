<?php 
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

require_once("./config.php");

$person_id = @$_COOKIE['login'];

function get_db_link(){
    $db_link  = new mysqli (DB_HOST,DB_USERNAME, DB_PASSWORD, DB_LINK);
    return $db_link;
}

require_once("./page.php");





class LoginPage extends Page {

    public function execute(){
        $this->login();
    }
    public function login(){ 
        $db_link = $this->db_link;
        $db_link->set_charset('utf8mb4');
    
        if(!$db_link){
            exit('接続できませんでした: ' . mysql_error()); 
        }
        

        $username = @$_GET['username'];
        $password  = @$_GET['password'];



        //エラーメッセージを入れておく空配列を作成
        $error_messages = [];
        if(!isset($_GET['username']) && !isset($_GET['password'])) {
            //ファイルを開いたときの処理なのでSQLを実行させない
        }elseif(empty($username) || empty($password)) {
            $error_messages[] = "メールアドレス（ユーザ名）の入力は必須項目です。\n"; 
        }elseif(!empty($username) && !empty($password)){
            $sql = "SELECT * FROM persons where email = '" . $username . "'and password = '" . $password . "'";
            var_dump($sql);
            $result = $db_link->query($sql);
            while($row = $result->fetch_assoc()){
                $record = $row;
            }
            if(@$record){
                setcookie('login',$record["id"]);
                header('Location: /Webapp_Portfolio/register_form.php'); 
            }else{
                $error_messages[] = "メールアドレスまたはパスワードが登録されていません。再度入力してください。\n";
            }
            $person = new Person();
        }

      

        //エラー判定

        if(count($error_messages) > 0){
            echo "<p>";
            foreach($error_messages as $error_message){
                echo $error_message."<br>";
            }
            echo "</p>";
            exit; 
        }
            
    } 



}

$page = new LoginPage();
$page->invoke();

?>

<html>
<div class="signin">
    <form action="login.php" method="GET">
        <label for="login-id">アカウント</label>
        <input id="login-id" name="username" type="text" placeholder="メールアドレスを入力">
        <label for="login-pass">パスワード</label>
        <input id="login-pass" name="password" type="text" placeholder="パスワードを入力">
        <input type="submit" value = "ログインする"> 
    </form>
</div>
</html>
