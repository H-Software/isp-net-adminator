<?php

//coded by Warden - http://warden.dharma.cz

/*
priklad vytvareni instance:

$listing = new c_Listing_objekty("aktivni link pro strankovani", "pocet zaznamu v jednom listu",
    "list pro zobrazeni", "formatovani zacatku odkazu strankovani",
    "formatovani konce odkazu strankovani", "sql dotaz pro vyber vsech zazkamu k vylistovani");
*/

//definice tridy c_Listing

class c_listing_objekty
{
    public \PgSql\Connection|\mysqli|\PDO $dbHandler;

    public $url;
    public $interval;
    public $sql;
    public $list;
    public $before;
    public $after;
    public $numLists;
    public $numRecords;
    public $errName;
    public $befError = "<div align=\"center\" style=\"color: maroon;\">";
    public $aftError = "</div>";

    public $echo = true;

    // $select="./objekty.php?";

    //konstruktor...naplni promenne
    public function __construct($dbHandler, $conUrl, $conInterval = 10, $conList = 1, $conBefore = "", $conAfter = "", $conSql = "")
    {
        $this->dbHandler = $dbHandler;

        $this->errName[1] = "P�i vol�n� konstruktotu nebyl zad�n SQL dotaz!<br>\n";
        $this->errName[2] = "Nelze zobrazit listov�n�, chyba datab�ze(Query)!<br>\n";
        // $this->errName[3] = "Nelze zobrazit listov�n�, chyba datab�ze(Num_Rows)!<br>\n";
        $this->url = $conUrl;
        $this->interval = $conInterval;
        $this->list = $conList;
        $this->before = $conBefore;
        $this->after = $conAfter;

        if (empty($conSql)) {
            $this->error(1);
        } else {
            $this->sql = $conSql;
        }
    }

    // include("config.pg.php");

    //vyber dat z databaze
    public function dbSelect()
    {
        if($this->dbHandler instanceof \PgSql\Connection) {
            try {
                $listRecord = @pg_query($this->dbHandler, $this->sql);
            } catch (Exception $e) {
                $this->error(2);
                echo $this->befError. $e->getMessage() . $this->aftError;
                return;
            }
        } elseif($this->dbHandler instanceof \PDO) {
            try {
                $listRecord = $this->dbHandler->query($this->sql);
            } catch (Exception $e) {
                $this->error(2);
                echo $this->befError. $e->getMessage() . $this->aftError;
                return;
            }
        } else {
            // unknown type
            return;
        }

        if (!$listRecord) {
            $this->error(2);
        }

        if($listRecord != false) {
            if($this->dbHandler instanceof \PgSql\Connection) {
                $allRecords = @pg_num_rows($listRecord);
            } elseif($this->dbHandler instanceof \PDO) {
                $allRecords = count($listRecord->fetchAll());
            } else {
                // unknown type
                return;
            }
        }

        if(!isset($allRecords)) {
            $this->error(2);
            return;
        }

        if ($allRecords < 1) {
            return;
        }

        try {
            $allLists = ceil($allRecords / $this->interval);
        } catch(DivisionByZeroError $e) {
        }

        $this->numLists = $allLists;
        $this->numRecords = $allRecords;

    }

    //zobrazi pouze seznam cisel listu
    //napr.:    1 | 2 | 3
    public function listNumber()
    {
        $this->dbSelect();
        echo $this->before;
        for ($i = 1; $i <= $this->numLists; $i++) {
            $isLink = 1;
            $spacer = " | ";

            if (empty($this->list)) {
                $this->list = 1;
            }
            if ($i == $this->list) {
                $isLink = 0;
            }
            if ($i == $this->numLists) {
                $spacer = "";
            }
            if ($isLink == 0) {
                echo $i." ".$spacer;
            }
            if ($isLink == 1) {
                echo "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$i."</a> ".$spacer;
            }
        }
        echo $this->after;
    }

    //zobrazi seznam intervalu v zadanem rozsahu ($interval)
    //napr.:    1-10 | 11-20 | 21-30
    public function listInterval()
    {
        $output = "";

        $this->dbSelect();
        $output .= $this->before;
        for ($i = 1; $i <= $this->numLists; $i++) {
            $isLink = 1;
            $spacer = " | ";
            $from = ($i * $this->interval) - ($this->interval - 1);
            $to = $i * $this->interval;

            if (empty($this->list)) {
                $this->list = 1;
            }
            if ($i == $this->list) {
                $isLink = 0;
            }
            if ($i == $this->numLists) {
                $to = $this->numRecords;
                $spacer = "";
            }
            if ($isLink == 0) {
                $output .= $from."-".$to." ".$spacer;
            }
            if ($isLink == 1) {
                $output .= "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$from."-".$to."</a> ".$spacer;
            }
        }
        $output .= $this->after;

        if($this->echo === true) {
            echo $output;
        } else {
            return $output;
        }
    }

    //zobrazi aktivni odkaz pouze na dalsi cast intervalu (dopredu, dozadu)
    //napr.:    <<< << 11-20 >> >>>
    public function listPart()
    {
        $this->dbSelect();
        echo $this->before;
        if (empty($this->list)) {
            $this->list = 1;
        }
        $from = ($this->list * $this->interval) - ($this->interval - 1);
        $to = $this->list * $this->interval;
        $forward = "<a href=\"".$this->url."&list=1\" onFocus=\"blur()\">&lt;&lt;&lt;</a>&nbsp;<a href=\"".$this->url."&list=".($this->list - 1)."\" onFocus=\"blur()\">&lt;&lt;</a>&nbsp;";
        $backward = "&nbsp;<a href=\"".$this->url."&list=".($this->list + 1)."\" onFocus=\"blur()\">&gt;&gt;</a>&nbsp;<a href=\"".$this->url."&list=".$this->numLists."\" onFocus=\"blur()\">&gt;&gt;&gt;</a>";

        if ($this->list == 1) {
            $forward = "";
        }
        if ($this->list == $this->numLists) {
            $to = $this->numRecords;
            $backward = "";
        }
        echo $forward.$from."-".$to.$backward;
        echo $this->after;
    }

    //vypisovani chybovych hlasek
    public function error($errNum = 0)
    {
        if ($errNum != 0) {
            echo $this->befError.$this->errName[$errNum].$this->aftError;
        }
    }
}
