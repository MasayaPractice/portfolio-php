<?php
ini_set('display_errors', "On");


Class Person {
    //プロパティ:
    public $id ;
    public $name ;
    public $kana ;
    public $email ;
    public $birthday ;
    public $average_point ;


    
    public function __construct($tmp){ 
        $this->id = $tmp['person_id']?? '';  
        $this->name = $tmp['name_kanji']?? '';   
        $this->kana = $tmp['name_katakana']; 
        $this->email = $tmp['email'];
        $this->birthday = new Datetime($tmp['birthday']); //
        $this->average_point = $tmp['average_point'];
    }





    //メソッド:
    public function getName(){
        return $this->name;
    } 
    public function getKana(){
        return $this->kana;
    } 
    public function getEmail(){

        return $this->email;
    } 
    public function getBirthday(){
        $birthday_obj = $this->birthday; //birthdayプロパティで取得した生年月日を$birthday_objに代入
        $date = $birthday_obj->format('Y年m月d日'); //取得した生年月日を　'Y年m月d日'フォーマットで $dateに代入
        return $date;
    }
    public function getAveragePoint(){
        return $this->average_point;
    }
    public function getId(){
        return $this->id ;
    }

    public function getAge(){       
        //計算
        $now = new Datetime(); 
        $birthday_obj = $this->birthday; 
        $interval = $now->diff($birthday_obj); 
        return $interval->y ;

    }

    public function getDay(){
        $birthday_obj = $this->birthday;
            $Week = array(
            '日',
            '月',
            '火',
            '水',
            '木',
            '金',
            '土'
            );
            $day = $birthday_obj->format('w');
            return $Week[$day];
           }

    }



 


