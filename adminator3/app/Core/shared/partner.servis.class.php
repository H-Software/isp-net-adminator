<?php

class partner_servis
{
    public \mysqli|\PDO $conn_mysql;

    public ?\PgSql\Connection $conn_pgsql;

    public $jmeno_klienta;
    public $bydliste;
    public $email;
    public $tel;
    public $pozn;

    public $odeslat;

    public $klient_hledat;
    public $klient_id;

    public $fail;
    public $error;

    public $fill_form;

    public $vyrizeni;
    public $update;

    public $prio;

    public function __construct($conn_mysql, $connPgsql)
    {
        $this->conn_mysql = $conn_mysql;
        $this->conn_pgsql = $connPgsql;
    }

    public function show_insert_form()
    {

        echo "  <div style=\"padding-left: 40px; padding-bottom: 10px; padding-top: 10px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >
                   Vložení servisního zásahu
                 </span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Stávající klient: </span>
                 <span style=\"padding-left: 195px; \">hledání</span>
                 <span style=\"padding-left: 45px; \" ><input type=\"text\" name=\"klient_hledat\" size=\"25\" value=\"".$this->klient_hledat."\" ></span>
                
                 <span style=\"padding-left: 195px; \"><input type=\"submit\" name=\"filtrovat\" value=\"HLEDAT\" ></span>
                
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 365px; \">výběr</span>
                 <span style=\"padding-left: 52px; \" >";

        if((strlen($this->klient_hledat) == 0)) {

            echo "<span style=\"padding-right: 70px; font-weight: bold;\">Zadejte výraz pro hledání</span>\n";
        } else {

            $vlastnici = $this->find_clients($this->klient_hledat);

            if(is_countable($vlastnici) && count($vlastnici) == 0) {
                echo "Žádné výsledky dle hledaného výrazu \n";
            } elseif(is_countable($vlastnici) && count($vlastnici) > 200) {

                echo "<span>více nalezených klientů, prosím specifikujte hledání</span>\n";
            } elseif(is_array($vlastnici)) {

                echo "<select size=\"1\" name=\"klient_id\">\n";
                echo "<option value=\"0\" class=\"select-nevybrano\">není vybráno</option>\n";

                $klient_id = intval($this->klient_id);

                foreach($vlastnici as $key => $value) {
                    echo "\t\t<option value=\"".intval($value["id_cloveka"])."\" ";

                    if($value["id_cloveka"] == $klient_id) {
                        echo " selected ";
                    }

                    echo " >".$value["prijmeni"]." ".$value["jmeno"];
                    echo " -- ".$value["ulice"].", ".$value["mesto"]."";
                    echo "</option>\n";
                }

                echo "</select>\n";

            } else {
                echo "<span style=\"color: red;\"> error: select from vlastnici \"failed\" </span>";
            }

        }

        echo "</span>";

        echo "<span style=\"padding-left: 90px;\" >
                	    <input type=\"submit\" name=\"fill_form\" value=\"PŘENÉST DO FORMULÁŘE\">
                       </span>";

        echo "      </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Jméno a příjmení klienta: </span>
                 <span style=\"padding-left: 140px; \" > <input type=\"text\" name=\"jmeno_klienta\" size=\"45\" value=\"".$this->jmeno_klienta."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Bydliště/přípojné místo: </span>
                 <span style=\"padding-left: 148px; \" > <input type=\"text\" name=\"bydliste\" size=\"45\" value=\"".$this->bydliste."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \" >Emailová adresa: </span>
                 <span style=\"padding-left: 218px; \" > <input type=\"text\" name=\"email\" size=\"30\" value=\"".$this->email."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \">Telefon: </span>
                 <span style=\"padding-left: 273px; \" > <input type=\"text\" name=\"tel\" size=\"30\" value=\"".$this->tel."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; margin-left: 45px;  \">
                 <span style=\"position: absolute; padding-top: 35px; \">
                 Poznámka / servisní zásah:
                 </span>
                 <span style=\"padding-left: 325px; \" >

                    <textarea name=\"pozn\" cols=\"35\" rows=\"5\" >".$this->pozn."</textarea>
                 </span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \">Priorita: </span>
                 <span style=\"padding-left: 273px; \" >
                  <select size=\"3\" name=\"prio\">";

        echo "<option value=\"1\" ";
        if($this->prio == 1) {
            echo "selected";
        } echo " >Vysoká</option>";
        echo "<option value=\"2\" ";
        if($this->prio == 2 or !isset($this->prio)) {
            echo "selected";
        } echo " >Normal</option>";
        echo "<option value=\"3\" ";
        if($this->prio == 3) {
            echo "selected";
        } echo " >Nízká</option>";

        echo "</select>
                </div>

                <div style=\" padding-top: 40px; \">
                 <span style=\"padding-left: 100px; \" > <input type=\"submit\" name=\"odeslat\" value=\"ULOŽIT\" >

                 <input type=\"hidden\" name=\"user\" value=\"".$this->user."\" >
                 <input type=\"hidden\" name=\"mod\" value=\"".$this->mod."\" >

                 </span>
                </div>";

    } //end of function

    public function find_clients($find_string)
    {

        $fs = "%".$this->conn_mysql->real_escape_string($find_string)."%";

        $select = " WHERE (nick LIKE '$fs' OR jmeno LIKE '$fs' OR prijmeni LIKE '$fs' ";
        $select .= " OR ulice LIKE '$fs' OR mesto LIKE '$fs' OR poznamka LIKE '$fs' )";

        $rs_vlastnici = pg_query($this->conn_pgsql, "SELECT id_cloveka, jmeno, prijmeni, ulice, mesto FROM vlastnici ".$select."");

        if($rs_vlastnici === false) {
            $RetArray[] = "<div>Nelze vypsat vlastniky. DB chyba! (" . pg_last_error() . ")</div>";
        }

        while($array = pg_fetch_array($rs_vlastnici)) {

            $row = array();

            $row["id_cloveka"] = $array["id_cloveka"];
            $row["jmeno"] = $array["jmeno"];
            $row["prijmeni"] = $array["prijmeni"];
            $row["ulice"] = $array["ulice"];
            $row["mesto"] = $array["mesto"];

            $RetArray[] = $row;

        }


        return $RetArray;
    } //end of function

    public function form_copy_values()
    {

        $rs_v = pg_query($this->conn_pgsql, "SELECT id_cloveka, jmeno, prijmeni, ulice, mesto, mail, telefon FROM vlastnici WHERE id_cloveka = '".intval($this->klient_id)."' ");

        while($array = pg_fetch_array($rs_v)) {

            $this->jmeno_klienta = $array["jmeno"]." ".$array["prijmeni"];
            $this->bydliste = $array["ulice"].", ".$array["mesto"];
            $this->email = $array["mail"];
            $this->tel = $array["telefon"];

        }

    } //end of function

    public function check_insert_value()
    {

        // zde kontrola, popr. naplneni promenne error

        $this->fail = false;

        //diakriticka predloha: příliš žluťoučký kůň pěl ďábelské ódy

        //kontrola jmena
        if(strlen($this->jmeno_klienta) > 50) {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Jméno a příjmení\" může obsahovat maximálně 50 znaků.</H4></div>";
        } elseif((strlen($this->jmeno_klienta) < 2) and ($this->odeslat == "ULOŽIT")) {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Jméno a příjmení\" musí být vyplněno.</H4></div>";

        }

        //kontrola bydliste / pripojneho mista
        if(strlen($this->bydliste) > 50) {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Bydliště\" může obsahovat maximálně 50 znaků.</H4></div>";
        } elseif((strlen($this->bydliste) < 5) and ($this->odeslat == "ULOŽIT")) {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Bydliště\" musí být vyplněno.</H4></div>";
        }

        //kontrola emailu
        if((strlen($this->email) > 0) and ($this->odeslat == "ULOŽIT")) {
            if(!(filter_var($this->email, FILTER_VALIDATE_EMAIL))) {
                $this->fail = true;
                $this->error .= "<div style=\"color: red; padding-left: 10px;\" >".
                 "<H4>Pole \"Email\" neodpovídá tvaru emailu.</H4></div>";
            }
        }

        //kontrola telefonu, resp. tel. cisla
        if(strlen($this->tel) > 0) {

            if(!(preg_match('/^([[:digit:]])+$/', $this->tel))) {
                $this->fail = true;
                $this->error .= "<div style=\"color: red; padding-left: 10px;\">".
                       "<H4>Pole \"Telefon\" není ve správnem formátu. (pouze číslice)</H4></div>";
            }

            if(strlen($this->tel) <> 9) {

                $this->fail = true;
                $this->error .= "<div style=\"color: red; padding-left: 10px; \">".
                "<H4>Pole \"Telefon\" musí obsahovat 9 číslic. </H4></div>";
            }

        } elseif((strlen($this->tel) == 0) and ($this->odeslat == "ULOŽIT")) {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Telefon\" musí být vyplněno.</H4></div>";
        }

        //kontrola poznamky
        if(strlen($this->pozn) > 0) {
            if(strlen($this->pozn) > 500) {

                $this->fail = true;
                $this->error .= "<div style=\"color: red; padding-left: 10px;\">".
                "<H4>Pole \"Poznámka\" musí obsahovat 9 číslic.</H4></div>";
            }

        } elseif($this->odeslat == "ULOŽIT") {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Poznámka\" musí být vyplněno.</H4></div>";

        }


        /*
        if( !( ereg('^([[:digit:]])+$',$this->tel) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Telefon\" musí obsahovat pouze číslice! </H4></div>";
        }

        if ( !( ereg('^([[:digit:]])+$',$this->typ_balicku) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Typ instalačního balíčku\" je ve špatném formátu! </H4></div>";
        }

        if ( !( ereg('^([[:digit:]])+$',$this->typ_linky) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Linka\" je ve špatném formátu! </H4></div>";
        }

        if( (strlen($this->pozn) > 0) )
        {
            if ( !( ereg('^([[:alnum:]]|ř|í|š|ž|ť|č|ý|ů|ň|ě|ď|á|é|ó|ú|Ř|Í|Š|Ž|Ť|Č|Ý|Ů|Ň|Ě|Ď|Á|É|Ó| |-|\.|_|@|,|\(|\)|\?)+$',$this->pozn) ) )
            {
            $this->fail = true;
            $this->error .= "<div style=\"color: red; \" ><H4>Poznámka obsahuje nepovolené znaky ( povolené: Písmena, čísla,  - , ., _, @, ,(,),? ) ! </H4></div>";
            }
        }
        */

    } //end of function

    public function save_form()
    {

        if(isset($this->klient_id)) {
            $this->jmeno_klienta .= ",  V:".$this->klient_id;
        }

        echo "  <div style=\"padding-bottom: 20px; padding-top: 20px; padding-left: 20px; font-size: 18px; font-weight: bold; \">
             <span style=\"border-bottom: 1px solid grey; \" >
                Vložené informace:
             </span>
            </div>

            <div style=\"padding-left: 20px; \" >
                <span style=\"font-weight: bold; \" >Jméno, příjmení klienta: </span>
                <span style=\"padding-left: 80px; \" > ".htmlspecialchars($this->jmeno_klienta)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Bydliště: </span>
                <span style=\"padding-left: 181px; \" > ".htmlspecialchars($this->bydliste)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Emailová adresa: </span>
                <span style=\"padding-left: 125px; \" > ".htmlspecialchars($this->email)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Telefon: </span>
                <span style=\"padding-left: 184px; \" > ".htmlspecialchars($this->tel)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Poznámka / servisní zásah: </span>
                <span style=\"padding-left: 63px; \" > ".htmlspecialchars($this->pozn)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Priorita: </span>
                <span style=\"padding-left: 184px; \" > ";
        if($this->prio == 1) {
            echo "Vysoká";
        } elseif($this->prio == 2) {
            echo "Normální";
        } elseif($this->prio == 3) {
            echo "Nízká";
        } else {
            echo "Nejze zjistit (".intval($this->prio).")";
        }
        echo "</span>
            </div>

          <div style=\"padding-top: 5px; \" ></div>";

        if(isset($user)) {
            $user_plaint = $_SESSION["user_plaint"];
        } else {
            $user_plaint = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;
        }

        $tel = $this->conn_mysql->real_escape_string($this->tel);
        $email = $this->conn_mysql->real_escape_string($this->email);

        $jmeno_klienta = $this->conn_mysql->real_escape_string($this->jmeno_klienta);
        $bydliste = $this->conn_mysql->real_escape_string($this->bydliste);
        $pozn = $this->conn_mysql->real_escape_string($this->pozn);

        $prio = intval($this->prio);

        $user_plaint = $this->conn_mysql->real_escape_string($user_plaint);

        $add = $this->conn_mysql->query(
            "INSERT INTO partner_klienti_servis (tel, jmeno, adresa, email, poznamky, prio, vlozil)
                            VALUES ('$tel','$jmeno_klienta','$bydliste','$email','$pozn', '$prio', '$user_plaint') "
        );

        echo "<div style=\"padding-left: 20px; padding-top: 15px; padding-bottom: 10px;\" >";

        if($add == 1) {
            echo "<div style=\"color: green; font-size: 18px; font-weight: bold;\" >Záznam úspěšně uložen.</div>";
        } else {
            echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </div>";
            // echo "<div style=\"color: grey; \">debug: ".mysql_error()."</div>";
        }

        echo "</div>";


    } //end of function

    public function list_show_legend($vyrizeni = false, $update = false)
    {
        if($vyrizeni == true) {

            echo "  <div style=\"padding-left: 40px; padding-top: 20px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Akceptování žádostí o připojení</span>
                </div>";
        } elseif($update == true) {
            echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky</span>
                </div>";
        } else {
            echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 10px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Výpis vložených položek</span>
                </div>";
        }

    } //end of function list show legend

    public function list_show_items($filtr_akceptovano, $filtr_prio, $dotaz_sql)
    {
        echo "<div style=\"padding-left: 45px; \">
                <table border=\"0\" width=\"90%\" cellpadding=\"5\" >\n";

        echo "<form action=\"\" method=\"GET\" >\n";

        echo "<tr><td colspan=\"8\" >
                    <span style=\"font-weight: bold; \" >Filtrování:</span>

                    <span style=\"font-weight: bold; padding-left: 20px; color: gray; \" >Akceptováno technikem:</span>
                    <span style=\"padding-left: 20px; \">
                        <select name=\"filtr_akceptovano\">
                        <option value=\"0\" ";
        if ($filtr_akceptovano == 0 or !isset($filtr_akceptovano)) {
            echo " selected ";
        }
        echo "class=\"select-nevybrano\">Nevybráno</option>
                         <option value=\"1\" ";
        if ($filtr_akceptovano == 1) {
            echo " selected ";
        } echo ">Ano</option>
                         <option value=\"2\" ";
        if ($filtr_akceptovano == 2) {
            echo " selected ";
        } echo ">Ne</option>
                        </select>
                    </span>

                    <span style=\"font-weight: bold; padding-left: 20px; color: gray; \" >Priorita:</span>
                    <span style=\"padding-left: 20px; \">
                        <select name=\"filtr_prio\" size=\"1\">
                         <option value=\"0\" ";
        if ($filtr_prio == 0 or !isset($filtr_prio)) {
            echo " selected ";
        }
        echo " class=\"select-nevybrano\">Nevybráno</option>
                         <option value=\"1\" ";
        if ($filtr_prio == 1) {
            echo " selected ";
        } echo " >Vysoká</option>
                         <option value=\"2\" ";
        if ($filtr_prio == 2) {
            echo " selected ";
        } echo " >Normální</option>
                         <option value=\"3\" ";
        if ($filtr_prio == 3) {
            echo " selected ";
        } echo " >Nízká</option>
                         
                        </select>
                    </span>

                    <span style=\"padding-left: 60px; \" ><input type=\"submit\" name=\"filtr\" value=\"FILTRUJ\" ></span>

                   </td></tr>";

        echo "<input type=\"hidden\" name=\"user\" value=\"".htmlspecialchars($user)."\" >
                   <input type=\"hidden\" name=\"mod\" value=\"".htmlspecialchars($mod)."\" >";

        echo "</form>";

        echo "<tr><td colspan=\"8\" ><br></td></tr>";

        $filtr = "";

        //prvne dotaz

        $dotaz = $this->conn_mysql->query($dotaz_sql);

        //if( !$dotaz )
        //{ echo "error: mysql_query: ".mysql_error().": sql: ".$dotaz_sql."\n"; }

        $dotaz_radku = $dotaz->num_rows;

        if ($dotaz_radku > 0) {
            echo "<tr><td colspan=\"8\" >
                    <span style=\"font-weight: bold;\" >Počet zákazníků:</span> ".$dotaz_radku."
                   </td></tr>";

            echo "<tr><td colspan=\"8\" ><br></td></tr>";

            // popis sloupcu
            echo "<tr>
                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Jméno klienta: </span></td>
                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Bydliště: </span></td>

                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Email: </span></td>
                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">Telefon: </span></td>

                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold;\">Akceptováno technikem: </span></td>
                        
                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>\n";

            if ($this->vyrizeni == true) {
                echo "<td class=\"table-vypis-1-line2\" colspan=\"2\" ><span style=\"font-weight: bold; \">Akceptovat</span></td>\n";
            } elseif($this->update == true) {
                echo "<td class=\"table-vypis-1-line2\" colspan=\"2\" ><span style=\"font-weight: bold; \">Upravit</span></td>\n";
            } else {
                echo "<td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>
				<td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>\n";
            }

            if (!($this->vyrizeni == true) and !($this->update == true)) {

                echo "</tr>
                    <tr>
                      <td colspan=\"2\" class=\"table-vypis-1-line\">
                        <span style=\"font-weight: bold; \">Poznámka/servis. úkol: </span>
                      </td>
                      <td colspan=\"1\" class=\"table-vypis-1-line\">
                        <span style=\"font-weight: bold; \">Vložil: </span>
                      </td>
                      <td colspan=\"1\" class=\"table-vypis-1-line\">
                       <span style=\"font-weight: bold; \">Priorita: </span>
                      </td>
                      <td colspan=\"2\" class=\"table-vypis-1-line\">
                       <span style=\"font-weight: bold; \">Poznámka technika: </span>

                      </td>
                      <td colspan=\"2\" class=\"table-vypis-1-line\" >
                        <span style=\"font-weight: bold; \">Datum vložení: </span>
                      </td>
                    </tr>";
            }

            echo "<tr><td colspan=\"8\" ><br></td></tr>";

            while($data = $dotaz->fetch_array()) {
                $jmeno = htmlspecialchars($data["jmeno"]);

                //nahrazeni id vlasntníka odkazem
                if(preg_match("/V:\d/", $jmeno)) {
                    $id_cloveka_res = "";
                    $pomocne = explode("V:", $jmeno);
                    $id_cloveka_pomocne = $pomocne[1];

                    $dotaz_vlastnik_pom = pg_query($this->conn_pgsql, "SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");

                    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom)) {
                        $firma_vlastnik = $data_vlastnik_pom["firma"];
                        $archiv_vlastnik = $data_vlastnik_pom["archiv"];
                    }

                    if($archiv_vlastnik == 1) {
                        $id_cloveka_res .= "<a href=\"/vlastnici-archiv.php";
                    } elseif($firma_vlastnik == 1) {
                        $id_cloveka_res .= "<a href=\"/vlastnici2.php";
                    } else {
                        $id_cloveka_res .= "<a href=\"/vlastnici.php";
                    }

                    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >V:".$id_cloveka_pomocne."</a>";

                    // TODO: probably wrong switch from ereg
                    // http://php.adamharvey.name/manual/en/function.preg-replace.php
                    $jmeno = preg_replace("/V:".$id_cloveka_pomocne."/", $id_cloveka_res, $jmeno);

                }

                if(($this->vyrizeni == true) or ($this->update == true)) {
                    $class = "table-vypis-suda-radka";
                }

                echo "<tr>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">".$jmeno."</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                if((strlen($data["adresa"]) < 1)) {
                    echo "&nbsp;";
                } else {
                    echo htmlspecialchars($data["adresa"]);
                }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                if((strlen($data["email"]) < 1)) {
                    echo "&nbsp;";
                } else {
                    echo htmlspecialchars($data["email"]);
                }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                if((strlen($data["tel"]) < 1)) {
                    echo "&nbsp;";
                } else {
                    echo htmlspecialchars($data["tel"]);
                }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";


                if($data["akceptovano"] == 1) {
                    echo "<span style=\"color: green; font-weight: bold; \">Ano </span>";
                    echo "<span style=\"\">(".htmlspecialchars($data["akceptovano_kym"]).")</span>";
                } else {
                    echo "<span style=\"color: orange; font-weight: bold; \" >Ne</span>";
                }

                echo "</span></td>";

                echo "<td class=\"".$class."\" >&nbsp;</td>";
                echo "<td class=\"".$class."\" >&nbsp;</td>";

                if($this->vyrizeni == true) {
                    echo "<td colspan=\"2\" class=\"".$class."\"><a href=\"".$_SERVER["PHP_SELF"]."?accept=1&id=".intval($data["id"])."\">akceptovat</a></td>";
                } elseif($this->update == true) {
                    echo "<td colspan=\"2\" class=\"".$class."\"><a href=\"".$_SERVER["PHP_SELF"]."?edit=1&id=".intval($data["id"])."\">upravit</a></td>";
                } else {

                    echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">&nbsp;</span></td>";

                }

                echo "</tr>";

                if (!($this->vyrizeni == true) and !($this->update == true)) {
                    // druha radka

                    echo "<tr>";

                    echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                    if((strlen($data["poznamky"]) < 1)) {
                        echo "poznámka nevložena";
                    } else {
                        echo htmlspecialchars($data["poznamky"]);
                    }
                    echo "</span></td>";

                    echo "<td colspan=\"1\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                    if((strlen($data["vlozil"]) < 1)) {
                        echo "vložil";
                    } else {
                        echo htmlspecialchars($data["vlozil"]);
                    }
                    echo "</span></td>";

                    echo "<td class=\"table-vypis-suda-radka\" ><span style=\"font-size: 13px; color: gray; \">";

                    if($data["prio"] == 1) {
                        echo "<span style=\"color: #990033;\">Vysoká</span>";
                    } elseif($data["prio"] == 2) {
                        echo "Normální";
                    } elseif($data["prio"] == 3) {
                        echo "Nízká";
                    } else {
                        echo "Nelze zjistit";
                    }

                    echo "</span></td>";

                    echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                    if((strlen($data["akceptovano_pozn"]) < 1)) {
                        echo "poznámka nevložena";
                    } else {
                        echo $data["akceptovano_pozn"];
                    }
                    echo "</span></td>";

                    echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" >
                        <span style=\"font-size: 12px; color: #555555; \">";
                    if($data["datum_vlozeni2"] == "00.00.0000 00:00:00") {
                        echo "není dostupné";
                    } else {
                        echo $data["datum_vlozeni2"];
                    }
                    echo "</span>
                      </td>";

                    echo "</tr>";

                } // konec if ! vyrizeno == true

            } // konec while

        } // konec if dotaz_radku vetsi > 0
        else {
            echo "<tr><td colspan=\"\" ><span style=\"font-size: 16px;\">Žádný zákazník v databázi neuložen.</span></td></tr>";
        }

        //konec vnitrni kabulky
        echo "</table></div>";


    } //end of function

} //end of class
