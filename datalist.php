<?php

//エラー抽出
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

//外部ファイル取り込み
require_once("./config.php");
require_once("./common.php");
require_once("./page.php");
require_once("./person.php");




//データ表示
class DataListPage extends Page{
    public function execute(){
        $this->datalist();      
    }
    public function datalist(){
         //Getパラメータの取得
        $kanji = @$_GET['kanji'];
        $sort     = @$_GET['sort'];
        $page_no  = @$_GET['page_no'];
        $email    = @$_GET['email']; 
        $birthday = isset($_GET['birthday']) ?  $_GET['birthday'] : null;
        
        $this->template_vars["kanji"] =  $kanji;
        $this->template_vars["sort"] =  $sort ;
        $this->template_vars["page_no"] = $page_no;
        $this->template_vars["email"] =  $email;
        $this->template_vars["birthday"] =  $birthday;
       

        //検索結果
        $person_info = getPersons($kanji, $birthday, $email, $sort, $page_no); 
                                    
        //レコード取得
        $records = @$person_info['records'];
        $this->template_vars["records"] = $records;
        
        
        //レコード数
        $record_count = @$person_info['record_count'];

        $this->template_vars["record_count"] =  $record_count;
    }
}

$datalist = new DatalistPage();
$datalist->invoke();

//現在の時間
$now = new  DateTime("now");



//ページ部分を作成
//現在のページ番号
$current_page_number = '';
$current_page_number = $datalist->template_vars["page_no"];

//総ページ番号
$total_page_number = ceil($datalist->template_vars["record_count"]/RECORDS_PER_PAGE);

//「前へ」と「後ろへ」のページ番号
$prev_page_number =  $current_page_number - 1;
$next_page_number = $current_page_number + 1;






$records = $datalist->template_vars;






$records = $datalist->template_vars["records"];

$record_count = $datalist->template_vars["record_count"];
?>


<html>
<body>

        <form action = 'datalist.php' method = 'get'>
        <!--フォーム画面作成 -->
        <p>名前を入力してください：
        <!--値（名前）を入力後に入力値を残す -->
        <input type='text' name='kanji' value="<?php echo $datalist->template_vars["kanji"];?>">
        </p>
        <p>メールアドレスを入力してください:
        <!--値（メールアドレス）を入力後に入力値を残す -->
        <input type='text' name='email' value="<?php echo $datalist->template_vars["email"];?>">
        </p>
        <p>生年月日を入力してください:
        <!--生年月日を入力後に入力値を残す -->
        <input type='text' name='birthday' value="<?php echo $datalist->template_vars["birthday"];?>">
        </p>
        <p><input type="submit" value="検索"></p>
        </form>



        <?php if($record_count == 0): ?>
        <p>records not found.</p>
        <?php else: ?>

        <table border=1>
        <tr>
            <th>氏名</th>
            <th>カナ</th>
            <th>生年月日</th>
            <th>曜日</th>
            <th>年齢</th>
            <th>メールアドレス
            <a href='datalist.php?birthday=<?php echo $datalist->template_vars["birthday"]; ?>&email=<?php echo $datalist->template_vars["email"]; ?>&sort=email_asc&kanji=<?php echo $datalist->template_vars["kanji"]; ?>'>▲</a>
            <a href='datalist.php?birthday=<?php echo $datalist->template_vars["birthday"]; ?>&email=<?php echo $datalist->template_vars["email"]; ?>&sort=email_desc&kanji=<?php echo $datalist->template_vars["kanji"]; ?>'>▽</a>
            </th>
            <th>編集</th>
            <th>テスト結果</th>
            <th>テスト結果平均</th>
        </tr>
        <?php foreach($records as  $record): ?>
        <?php $person = new Person($record); ?>
            <tr>
                <td><?php echo $person->getName(); ?></td>
                <td><?php echo $person->getKana(); ?></td>
                <td><?php echo $person->getBirthday(); ?></td> 
                <td><?php echo $person->getDay();?>曜日</td> 
                <td><?php echo $person->getAge();?>歳</td> 
                <td><?php echo $person->getEmail(); ?></td>
                <td><a href="edit.php?id=<?php echo $person->getId(); ?>">編集</td>
                <td><a href="exam_result.php?person_id=<?php echo $person->getId(); ?>">テスト結果</td>
                <td><?php echo round($person->getAveragePoint());?>点</td>          
            </tr>
        <?php endforeach; ?>
        </table>

    <!--//ページ番号表示 -->
    <a href='datalist.php?page_no=<?php echo $prev_page_number; ?>'> 前へ </a> 
    <?php for($i = 1; $i <= @$total_page_number; $i ++): ?>
    <a href="datalist.php?kanji=<?php echo $datalist->template_vars["kanji"];?>&sort=<?php echo $datalist->template_vars["sort"]?>&page_no=<?php echo $i?>&email=<?php echo $datalist->template_vars["email"]?>&birthday=<?php echo $datalist->template_vars["birthday"]?>"><?php echo $i?></a>
    <?php endfor; ?>
    <a href='datalist.php?page_no=<?php echo $next_page_number; ?>'> 次へ </a>
    <?php endif; ?>

    <a href="./register_form.php"> 登録画面に戻る </a>
    <a href="./datalist.php"> 一覧へ </a> 
    <a href="./logout.php">
        <button type="button">ログアウトする</button>
    </a>
</body>
</html>