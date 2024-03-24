<?php

ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

require_once("./config.php");
require_once("./page.php");





class LogoutPage extends Page{
    public function execute(){
        $this->logout();
    }
    public function logout(){
        //var_dump($_COOKIE["login"]);
        setcookie("login",""); 


        //$this->db_link->query("select * from persons");
    }

}

$logout = new LogoutPage();
$logout->invoke();


?>

<html>
<h2>ログアウトが完了しました</h2>
<p><a href='login.php'>ログインページへ戻る</a></p>
<?php echo "削除しました";  ?>
</html>