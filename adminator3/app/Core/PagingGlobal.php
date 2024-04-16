<?php

namespace App\Core;

class paging_global {

    var $url;
    var $interval;
    var $sql;
    var $list;
    var $before = "<div class=\"text-listing2\" style=\"text-align: center;\" >\n";
    var $after  = "</div>";
    var $numLists;
    var $numRecords;
    var $errName;
    var $befError = "<div align=\"center\" style=\"color: maroon;\">";
    var $aftError = "</div>\n";
    
	var $conn_mysql;
    var $db_type = "mysql";
    
    //konstruktor...naplni promenne
    function __construct($conn_mysql, $conUrl = "home.php", $conInterval = 10, $conList = 1, $conBefore, $conAfter, $conSql = ""){

		$this->conn_mysql = $conn_mysql;

        $this->errName[1] = "Při volání konstruktoru nebyl zadán SQL dotaz!<br>\n";
        $this->errName[2] = "Nelze zobrazit listování, chyba databáze(Query)!<br>\n";
        // $this->errName[3] = "Nelze zobrazit listov▒n▒, chyba datab▒ze(Num_Rows)!<br>\n";

		$this->url = $conUrl;
        $this->interval = $conInterval;
        
        $this->list = $conList;
        
        if( (strlen($conBefore) > 0))
        { $this->before = $conBefore; }
        
        if( (strlen($conBefore) > 0))
        { $this->after = $conAfter; }

        if (empty($conSql)){
            $this->error(1);
        }
        else {
            $this->sql = $conSql;
        }
    }

    //vyber dat z databaze
    function dbSelect(){
		
		global $db_ok2;

        if($this->db_type == "mysql")
    	    $listRecord = $this->conn_mysql->query($this->sql);
        elseif($this->db_type == "pgsql")
    	    $listRecord = pg_query($this->sql);
        else{
        }
        
        if (!$listRecord){
            $this->error(2);
        }

        if($this->db_type == "mysql")        
    	    $allRecords = $listRecord->num_rows;
        elseif($this->db_type == "pgsql")
    	    $allRecords = pg_num_rows($listRecord);
        else{
        
        }
        
        if (!$allRecords){
            $this->error(3);
        }
        
        $allLists = ceil($allRecords / $this->interval);

        $this->numLists = $allLists;
        $this->numRecords = $allRecords;

    }

    //zobrazi pouze seznam cisel listu
    //napr.:    1 | 2 | 3
    function listNumber(){
        $this->dbSelect();
        echo $this->before;
        for ($i = 1; $i <= $this->numLists; $i++){
            $isLink = 1;
            $spacer = " | ";

            if (empty($this->list)){
                $this->list = 1;
            }
            if ($i == $this->list){
                $isLink = 0;
            }
            if ($i == $this->numLists){
                $spacer = "";
            }
            if ($isLink == 0){
                echo $i." ".$spacer;
            }
            if ($isLink == 1){
                echo "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$i."</a> ".$spacer;
            }
        }
        echo $this->after;
    }

    //zobrazi seznam intervalu v zadanem rozsahu ($interval)
    //napr.:    1-10 | 11-20 | 21-30
    function listInterval(){
        $output = "";
        $this->dbSelect();
        $output .= $this->before;
        for ($i = 1; $i <= $this->numLists; $i++){
            $isLink = 1;
            $spacer = " | ";
            $from = ($i*$this->interval)-($this->interval-1);
            $to = $i*$this->interval;

            if (empty($this->list)){
                $this->list = 1;
            }
            if ($i == $this->list){
                $isLink = 0;
            }
            if ($i == $this->numLists){
                $to = $this->numRecords;
                $spacer = "";
            }
            if ($isLink == 0){
                $output .= $from."-".$to." ".$spacer;
            }
            if ($isLink == 1){
                $output .= "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$from."-".$to."</a> ".$spacer."\n";
            }
        }
        $output .= $this->after;

        return $output;
    }

    //zobrazi aktivni odkaz pouze na dalsi cast intervalu (dopredu, dozadu)
    //napr.:    <<< << 11-20 >> >>>
    function listPart(){
        $this->dbSelect();
        echo $this->before;
        if (empty($this->list)){
                $this->list = 1;
        }
        $from = ($this->list*$this->interval)-($this->interval-1);
        $to = $this->list*$this->interval;
        $forward = "<a href=\"".$this->url."&list=1\" onFocus=\"blur()\">&lt;&lt;&lt;</a>&nbsp;<a href=\"".$this->url."&list=".($this->list-1)."\" onFocus=\"blur()\">&lt;&lt;</a>&nbsp;";
        $backward = "&nbsp;<a href=\"".$this->url."&list=".($this->list+1)."\" onFocus=\"blur()\">&gt;&gt;</a>&nbsp;<a href=\"".$this->url."&list=".$this->numLists."\" onFocus=\"blur()\">&gt;&gt;&gt;</a>";

        if ($this->list == 1){
            $forward = "";
        }
        if ($this->list == $this->numLists){
            $to = $this->numRecords;
            $backward = "";
        }
        echo $forward.$from."-".$to.$backward;
        echo $this->after;
    }

    //vypisovani chybovych hlasek
    function error($errNum = 0){
        if ($errNum != 0){
            echo $this->befError.$this->errName[$errNum].$this->aftError;
        }
    }
}
