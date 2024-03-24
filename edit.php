<?php
ini_set('display_errors', "ON"); 
ini_set('error_reporting', E_ALL);


//外部ファイル取り込み
require_once("./config.php");
require("./common.php");
require_once("./page.php");


//登録データの編集
class EditPage extends Page{
    public function execute(){
        $this->edit(); 
    }

    public function edit(){
        //URLパラメータを取得                              
        $id = @$_GET['id'];

        //SQLの実行
        $sql = "SELECT * FROM persons WHERE id = " . @$id;
        $mysqli_fetch_result = $this->get_db_link()->query($sql);
        $result =  $mysqli_fetch_result ->fetch_assoc();
        $this->template_vars['record'] = $result;
    }
}

$edit = new EditPage();
$edit->invoke();


?>
<html>
    <body>
        <p><?php echo login()?>さんでログインしています</p>
        <form action = "./update.php" id = "id" method = "get" >
        <h3>再度入力してください<br></h3>
        <p>氏名<br>
            <input type="text" name="name_kanji" value = "<?php echo $edit->template_vars['record']['name_kanji']; ?>">
        </p>
        <p>氏名（カタカナ）<br>
            <input type="text" name="name_katakana" value = <?php echo $edit->template_vars['record']['name_katakana']; ?>>
        </p>
        <p>生年月日<br>
            <input type="text" name="birthday" value = <?php echo  $edit->template_vars['record']['birthday']; ?>>
        </p>
        <p>メールアドレス：<br>
            <input type="text" name="email" value = <?php echo $edit->template_vars['record']['email']; ?>>
        </p>

        <p>
        <input type="submit"  value = "更新する">   
        <input type="hidden" name="id" value =<?php echo $edit->template_vars['record']['id']; ?>>
        </p>
        </form>
    </body>
</html>