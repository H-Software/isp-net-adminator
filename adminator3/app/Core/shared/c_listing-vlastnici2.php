<?php
//coded by Warden - http://warden.dharma.cz

/*
priklad vytvareni instance:

$listing = new c_Listing("aktivni link pro strankovani", "pocet zaznamu v jednom listu", 
    "list pro zobrazeni", "formatovani zacatku odkazu strankovani", 
    "formatovani konce odkazu strankovani", "sql dotaz pro vyber vsech zazkamu k vylistovani");
*/

//definice tridy c_Listing

class c_listing_vlastnici2 {
    var $url;
    var $interval;
    var $sql;
    var $list;
    var $before;
    var $after;
    var $numLists;
    var $numRecords;
    var $errName;
    var $befError = "<div align=\"center\" style=\"color: maroon;\">";
    var $aftError = "</div>";
    
   // $select="./objekty.php?";
    
    //konstruktor...naplni promenne
    function c_listing_vlastnici2($conUrl = "./vlastnici.php?", $conInterval = 10, $conList = 1, $conBefore = "", $conAfter = "", $conSql = ""){
        $this->errName[1] = "Při volání konstruktotu nebyl zadán SQL dotaz!<br>\n";
        $this->errName[2] = "Nelze zobrazit listování, chyba databáze(Query)!<br>\n";
        // $this->errName[3] = "Nelze zobrazit listov�n�, chyba datab�ze(Num_Rows)!<br>\n";
        $this->url = $conUrl;
        $this->interval = $conInterval;
        $this->list = $conList;
        $this->before = $conBefore;
        $this->after = $conAfter;
	
        if (empty($conSql)){
            $this->error(1);
        }
        else {
            $this->sql = $conSql;
        }
    }
    
    //vyber dat z databaze
    function dbSelect(){
        $listRecord = pg_query($this->sql);
        if (!$listRecord){
            $this->error(2);
        }
        if($listRecord !== false){
            $allRecords = pg_num_rows($listRecord);
        }
        
        if (!$allRecords){
            $this->error(3);
        }
        try {
            $allLists = ceil($allRecords / $this->interval);
        }
        catch(Exception $e){
        }
        
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
        $this->dbSelect();
        echo $this->before;
        for ($i = 1; $i <= $this->numLists; $i++){
            $isLink = 1;
            $spacer = " | ";
            $from = ($i*$this->interval)-($this->interval-1);
            $to = $i*$this->interval;
            
            if (Empty($this->list)){
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
                echo $from."-".$to." ".$spacer;
            }
            if ($isLink == 1){
                echo "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$from."-".$to."</a> ".$spacer;
            }
        }
        echo $this->after;
    }
    
    //zobrazi aktivni odkaz pouze na dalsi cast intervalu (dopredu, dozadu)
    //napr.:    <<< << 11-20 >> >>>
    function listPart(){
        $this->dbSelect();
        echo $this->before;
        if (Empty($this->list)){
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
?>