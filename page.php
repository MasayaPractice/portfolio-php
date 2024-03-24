<?php
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);
require_once("./config.php");

class Page{

    public $db_link;
    public $is_require_login = true; //慣例
    public $template_vars = [];
    

    public function invoke(){
        $this->init_db_link();
        $this->check_login();
        $this->execute();
      
    }


    //データベース接続
    function init_db_link() {
        $this->db_link  = new mysqli (DB_HOST,DB_USERNAME, DB_PASSWORD, DB_LINK);
    }

    function get_db_link() {
        return $this->db_link;
    }

    //ログイン
    function check_login() {
        if($this->is_require_login == true) {
            // Cookiecチェック
            if(isset($_COOKIE["login"])){
                echo "ログインできました";
            }elseif(!@$_COOKIE["login"]){
                echo "この機能を利用するにはログインが必要です";
                echo "<p><a href='login.php'>ログインページへ戻る</a></p>";
                exit;
            }
        }
    }


}