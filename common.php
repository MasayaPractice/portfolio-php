<?php
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

require_once("./config.php");



function get_db_link() {
    $db_link  = new mysqli (DB_HOST,DB_USERNAME, DB_PASSWORD, DB_LINK);
    return $db_link;
}



function getPersons($kanji, $birthday, $email, $sort, $page_no) {
    
    $db_link  = get_db_link();
    $db_link->set_charset('utf8mb4');
    if(!$db_link){
        exit('接続できませんでした: ' . mysql_error()); 
    }  

    // GETパラメータで指定された検索条件("kanji")でマッチするレコード数を取得。（ページングで使用する）
    if($kanji != "" && $email != "" && $birthday != "") { //氏名に値がある　かつ　生年月日、メールアドレスに値がある
        $where = " where  email like '%" . $email . "%' and birthday like '%" . $birthday . "%' and name_kanji like '%" . $kanji . "%' ";
    } elseif($kanji != ""  && $email == "" && $birthday != "") { //氏名に値がある　かつ　メールアドレスに値がない　かつ　生年月日に値ある
        $where = " where   birthday like '%" . $birthday . "%' and name_kanji like '%" . $kanji . "%' ";
    } elseif($kanji != ""  && $email != "" && $birthday == "") {//氏名に値がある　　かつ　メールアドレスに値がある　かつ　生年月日に値ない
        $where = " where   email like '%" . $email . "%' and name_kanji like '%" . $kanji . "%' ";
    } elseif($kanji != ""  && $email == "" && $birthday == "") {//氏名に値がある　　かつ　メールアドレスに値がない　かつ　生年月日に値ない
        $where = " where name_kanji like '%" . $kanji . "%' ";
    } elseif($kanji == ""  && $email != "" && $birthday != "") {//　氏名に値がない  かつ メールアドレスに値がある。　かつ　生年月日に値がある。
        $where = " where email  like '%". $email . "%' and birthday like '%" . $birthday . "%' ";
    } elseif($kanji == ""  && $email != "" && $birthday == "") {//氏名値がない　かつ　メールアドレスに値がある。　　かつ　氏名、生年月日に値がない。
        $where = " where email like '%" . $email . "%'";
    } elseif ($kanji == "" && $email == "" && $birthday != "") { //　氏名に値がない、　メールアドレスに値がない 生年月日に値がある。　　
        $where = " where email like '%" . $email . "%' and birthday like '%". $birthday . "%' ";
    } elseif ($kanji == "" && $email == "" && $birthday == "") {  //氏名に値がない メールアドレスに値がない  生年月日に値がない。　、
        $where = " ";

    }

    //SQLの実行
    $sql = 'SELECT COUNT(*) as num FROM persons' . $where;
    $result = $db_link->query($sql);  
    $mysqli_fetch_result = $result->fetch_assoc(); 

    //実行したSQLから結果（件数）を取得
    $record_count = $mysqli_fetch_result["num"];

    //Offset値を初期化
    $offset = 0;

    //GETパラメータで指定された検索条件でマッチするレコードをRECORDS_PER_PAGE件取得する。指定されたソート条件も適用する。指定されたページ数にあったoffset値も入れる。
    if($page_no < 1){
        $page_no = 1; 
    }else{
        $offset = ($page_no - 1) * RECORDS_PER_PAGE;
    }

    $limit_offset = " limit " . RECORDS_PER_PAGE . " offset " . $offset;

    
    // ソート条件
    $orderby = " order by email asc "; 
    if($sort == 'email_asc'){ 
        $orderby = ' order by email asc'; 
    }else if($sort == 'email_desc'){ 
        $orderby = ' order by email desc '; 
    }else if($sort == 'birthday_asc'){
        $orderby = ' order by birthday asc';
    }else if($sort == 'birthday_desc'){ 
        $orderby = ' order by birthday desc ';
    }

    //事前に文字列として作っておいたSQLを結合し、"$sql"代入
    $sql = "SELECT * FROM persons " . $where . $orderby . $limit_offset;
    //SQLの実行
    $query_result  = $db_link -> query($sql);
    //実行したSQLから結果（レコード）を取得
    $records = [];
    while ($row = $query_result-> fetch_assoc()){
        //連想配列に追加
        $record = [
            "name_kanji" => $row["name_kanji"],
            "name_katakana" => $row["name_katakana"],
            "birthday"      => $row["birthday"],
            "email"         => $row["email"],
            "birthday_obj"      => new Datetime($row["birthday"]), 
            "person_id"    => $row["id"],
        ];
        $records[] = $record;                                 
 
    }

    //テスト平均の計算
    foreach($records as $key  => $record){  
        $db_link  = get_db_link();
        $db_link->set_charset('utf8mb4');
        $person_id = $record['person_id'];
        //　ここで平均値などを取得
        $sql = "SELECT AVG (result_point) as average_point FROM `examinations` where person_id =".$person_id;
        $query_result = $db_link ->query($sql);
        while ($row = $query_result ->fetch_assoc() ){  
            $average_point = $row['average_point'];                       
            $record['average_point'] = $average_point;   
            $records[$key] = $record;                                                                
        }
    }
    
    $result = array(
        'records'      => $records,
        'record_count' => $record_count
    );
    return $result;
}


//ようこそ～さまというログインしている人の情報を表示
function login(){ 
    $db_link  = get_db_link();
    $db_link->set_charset('utf8mb4');

    if(!$db_link){
        exit('接続できませんでした: ' . mysql_error()); //mysql_error() を用いて エラー文字列を取得
    }  
    $sql = "SELECT * FROM persons where id =".$_COOKIE["login"];
    $result = $db_link->query($sql);
    while($row = $result->fetch_assoc()){
        $login_name_kanji = $row["name_kanji"];
    }
    return $login_name_kanji; 
} 

//personのidを指定すると、personのデータ（連想配列）が戻り地として返ってくる関数
function  PersonInfo($person_id){
    $db_link  = get_db_link();
    $db_link->set_charset('utf8mb4');
    if(!$db_link){
        exit('接続できませんでした: ' . mysql_error()); 
    }  
    $sql = "SELECT * FROM persons where id =".$_COOKIE['login'];
    $result = $db_link->query($sql);
    $row = $result->fetch_assoc();
    $record = $row;

    //　平均値などを取得
    $sql = "SELECT AVG (result_point) as average_point FROM `examinations` where person_id =".$person_id;
    $query_result = $db_link ->query($sql);
    while ($row = $query_result ->fetch_assoc() ){  
        $average_point = $row['average_point'];                          
        $record['average_point'] = $average_point;                                               
    }
    return $record;
}

