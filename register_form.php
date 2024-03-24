<?php 

ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

require_once("./config.php");
require("./common.php");
require_once("./page.php");




class RegisterationPage extends Page{
    public function execute(){
        //ロジックなし
    }

}
$page = new RegisterationPage();

$page->invoke();



?>


<html>
<body>
<p><?php echo login(); ?>さんで
しています</p> 
 <form action="register.php" method="Get">

 <p>氏名（Name):<br>
	<input type="text" name="name_kanji" value= "氏名"></p>
 <p>氏名（カタカナ）:<br>
	<input type="text" name="name_katakana" value= "カタカナ"></p>
<p>生年月日:<br>
	<input type="text" name="birthday" value= "生年月日"></p>
<p>メールアドレス：<br>
	<input type="text" name="email" value ="メールアドレス"></p> 
<p>パスワード: <br>
	<input type="text" name="password" value ="パスワード"></p> 





	
<input type="submit" value = "登録する">  
<a href="./datalist.php" > 一覧へ</a> 
<a href="./logout.php">
    <button type="button">ログアウトする</button>
</a>


</form>
    </body>
</html>
