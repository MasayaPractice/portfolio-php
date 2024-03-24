<?php 

//エラー抽出
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

//外部ファイル取り込み
require_once("./config.php");
require_once("./common.php");
require_once("./page.php");
require_once("./person.php");

Class ExaminationResultsPage extends Page{
    //public $template_vars = "";

    public function execute(){
        $this->ExaminationResults(); 
    }

    public function ExaminationResults(){
        //GETパラメータの取得
        $person_id = @$_GET['person_id'];
        $result_point = @$_GET['result_point'];
        $examination_date = @$_GET['examination_date'];
        var_dump($person_id);

        $this->template_vars["person_id"] =  $person_id;
        var_dump($this->template_vars["person_id"] );
        $this->template_vars["result_point"] =  $result_point ;
        $this->template_vars["examination"] = $examination_date;
        var_dump( $this->template_vars["person_id"]);


        if(!isset($person_id)){

            echo "この機能を利用するにはログインが必要です";
            echo "<p><a href='login.php'>ログイン画面へ戻る</a></p>";  
            exit;
        }else{


            //テスト結果を取得 //person_idを指定しない場合はエラー
            $sql = "SELECT * FROM examinations WHERE person_id = " . $this->template_vars["person_id"];
            $query = $this->get_db_link()->query($sql);
            while ($results =  $query ->fetch_assoc()){
                $records[] = $results; 
            }
            $this->template_vars = @$records;
            $records = $this->template_vars;

             //バリデーションチェック
            if(!empty($result_point) || !empty($examination_date)){
                $error_messages = [];
                //数値チェック
                if(!isset($result_point)){
                //この場合はエラーを表示させない
                }elseif(empty($result_point)){
                    $error_messages[] = "点数（数字)の入力は必須です。\n";
                }elseif(!is_numeric($result_point)){
                    $error_messages[] = "数字で入力してください\n";
                }elseif($result_point > 100 ){
                    $error_messages[] = "0~100の数字で入力してください\n";
                }
                
                //テスト実施日チェック
                $exam_date  = date_parse_from_format("Y-n-j", $examination_date);
                if(!isset($examination_date)){
                //この場合はエラーを表示させない
                }elseif(empty($examination_date)){
                    $error_messages[] = "日付の入力は必須です。\n";
                }
                elseif($exam_date["warning_count"] != 0 || $exam_date["error_count"] != 0){
                    $error_messages[] = "日付が間違っています。YYYY-mm-dd形式で入力してください";
                
                }elseif(mb_strlen($examination_date,'UTF-8') > 20){
                    $error_messages[] =  "日付は20字以内で入力ください";
                }else{
                    $exam_date_obj = new DateTime($examination_date);
                    $now = new DateTime(); 
                    if($exam_date_obj > $now){
                        $error_messages[] =  "過去の日付を入力してください";      
                    }
                }
                //エラー判定
                if(count($error_messages) > 0){
                    echo "<p>";
                foreach($error_messages as $error_message){
                    echo $error_message."<br>";
                }
                echo "</p>";
                echo '<a href="./register_form.php"> 登録画面に戻る </a>';
                exit;
                }

                
                //入力データをDBに格納
                $sql = "INSERT INTO examinations ( `person_id`, `result_point`, `examination_date`) VALUES ('"
                .$person_id
                ."', " 
                ."'"
                .$result_point
                ."', " 
                ."'"   
                .$examination_date
                ."')";  
                $result = $this->get_db_link()->query($sql);
            }
        }
      
    }
}

$examresult = new ExaminationResultsPage();

$examresult->invoke();


$records = $examresult->template_vars;
?>

<html>

<body>
<p><?php echo login(); ?>さんでログインしています</p> 
<table border = 1>
    <tr>
        <th>テスト結果</th>
    </tr>
    <?php foreach(@$records as $record):?>
    <tr><td><?php echo $record["result_point"]; ?>点</td></tr>   
    <?php endforeach; ?>
</table>
<form action = 'exam_result.php'  method = 'get'>
    <p>点数を入力してください：
    <input type='text' name='result_point' >
    </p>
    <p>テスト実施日を入力してください:
    <input type='text' name='examination_date'>
    </p>
    <input type="submit" value="登録">
    <input type="hidden" name="person_id" value =<?php echo $examresult->template_vars[0]["person_id"]; ?>>
</form>
    <a href="./register_form.php"> 登録画面に戻る </a>
    </body>

</html>

