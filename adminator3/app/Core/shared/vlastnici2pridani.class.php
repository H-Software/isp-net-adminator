<?php

use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as DB;

class vlastnici2pridani extends adminator
{
    public $conn_mysql;

    public $conn_pgsql;

    public $logger;

    public $smarty;

    // public $container; // for calling stb class over vlastnik2_a2 class

    public $adminator; // handler for instance of adminator class

    public $alert_type;

    public $alert_content;

    public $csrf_html;

    private $error;

    private $fail;

    private $action_az_pole2;

    private $action_az_pole3;

    private $action_affected;

    private $action_vlast_upd_old;

    private $form_update_id;

    private $form_odeslano;

    private $form_send;

    private $form_firma_add;

    private $form_nick;

    private $form_vs;

    private $form_k_platbe;

    private $vlast_upd;

    private $pole_puvodni_data;

    private $form_fakt_skupina;

    private $form_archiv;

    private $form_jmeno;

    private $form_prijmeni;

    private $form_ulice;
    
    private $form_mesto;

    // $psc = $data["psc"];
    // $email = $data["mail"];
    // $icq = $data["icq"];
    // $tel = $data["telefon"];
    // $poznamka = $data["poznamka"];
    // $ucetni_index = $data["ucetni_index"];
    // $typ_smlouvy = $data["typ_smlouvy"];
    // $fakturacni = $data["fakturacni"];
    // $splatnost = $data["splatnost"];
    // $trvani_do = $data["trvani_do"];
    // $datum_podpisu = $data["datum_podpisu"];
    // $sluzba_int = $data["sluzba_int"];
    // $sluzba_iptv = $data["sluzba_iptv"];
    // $sluzba_voip = $data["sluzba_voip"];

    // $sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
    // $sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];

    // $billing_freq = $data["billing_freq"];

    // $billing_suspend_status = $data["billing_suspend_status"];
    // $billing_suspend_reason = $data["billing_suspend_reason"];

    // $billing_suspend_start  = $data["billing_suspend_start"];
    // $billing_suspend_stop   = $data["billing_suspend_stop"];

    private $firma;

    private $updated;

    private $writed;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function action(): string
    {
        $output = "";
        $this->error = null;

        $output .= $this->actionPrepareVars();

        if ($this->form_update_id > 0) {
            $this->smarty->assign("content_header", "Úprava vlastníka");
        } else {
            $this->smarty->assign("content_header", "Přidání nového vlastníka");
        }

        //kontrola promených
        if(isset($this->form_send)) {
            if((strlen($this->form_nick) > 0)) {
                vlastnici2pridani::checknick($this->form_nick);
            }

            if((strlen($this->form_vs) > 0)) {
                vlastnici2pridani::checkvs($this->form_vs);
            }

            if((strlen($this->form_k_platbe) > 0)) {
                vlastnici2pridani::check_k_platbe($this->form_k_platbe);
            }

            if((strlen($splatnost) > 0)) {
                vlastnici2pridani::check_splatnost($splatnost);
            }

            if((strlen($icq) > 0)) {
                vlastnici2pridani::check_icq($icq);
            }

            if((strlen($email) > 0)) {
                vlastnici2pridani::check_email($email);
            }

            if((strlen($ucetni_index) > 0)) {
                vlastnici2pridani::check_uc_index($ucetni_index);
            }

            if((strlen($tel) > 0)) {
                vlastnici2pridani::check_tel($tel);
            }

            if((strlen($datum_podpisu) > 0)) {
                vlastnici2pridani::check_datum($datum_podpisu, "Datum podpisu");
            }

            if($typ_smlouvy == 2) {
                vlastnici2pridani::check_datum($trvani_do, "Trvání do");
            } elseif((strlen($trvani_do) > 0)) {
                vlastnici2pridani::check_datum($trvani_do, "Trvání do");
            }

            if($billing_suspend_status == 1) {

                vlastnici2pridani::check_datum($billing_suspend_start, "Poz. fakturace - od kdy");
                vlastnici2pridani::check_datum($billing_suspend_stop, "Poz. fakturace - do kdy");

            }

            if((strlen($billing_suspend_reason) > 0) and ($billing_suspend_status == 1)) {
                vlastnici2pridani::check_b_reason($billing_suspend_reason);
            }

        }

        if(($this->form_update_id > 0 and !(isset($this->form_send)))) {
            // $trvani_do = "";
            if((strlen($trvani_do) > 0)) {
                list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("\-", $trvani_do);
                $trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
            }

            if((strlen($datum_podpisu) > 0)) {
                list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("\-", $datum_podpisu);
                $datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
            }

        }

        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if(($this->form_nick != "") and ($this->form_vs != "") and ($this->form_k_platbe != "") and (($this->form_fakt_skupina > 0) or ($this->firma <> 1) or ($this->form_archiv == 1))) {

            if($this->form_update_id < 1) {
                //zjisti jestli neni duplicitni : nick, vs
                $MSQ_NICK = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE nick LIKE '$this->form_nick' ");
                if (pg_num_rows($MSQ_NICK) > 0) {
                    $this->error .= "<h4>Nick ( ".$this->form_nick." ) již existuje!!!</h4>";
                    $this->fail = "true";
                }
            }

            // check v modu uprava
            if(($this->form_update_id > 0 and (isset($this->form_odeslano)))) {
                //zjisti jestli neni duplicitni : nick, vs
                $MSQ_NICK = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE nick LIKE '$this->form_nick' and id_cloveka <> '$this->form_update_id' ");
                if (pg_num_rows($MSQ_NICK) > 0) {
                    $this->error .= "<h4>Nick ( ".$this->form_nick." ) již existuje!!!</h4>";
                    $this->fail = "true";
                }
            }

            //checkem jestli se macklo na tlacitko "OK" :)
            if (preg_match("/^OK$/", $this->form_odeslano)) {
                echo "";
            } else {
                $this->fail = "true";
                $this->error .= "<div class=\"vlastnici2-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", ";
                $this->error .= "pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
            }

            //ulozeni
            if (!(isset($this->fail))) {
                if ($this->form_update_id > 0) {
                    // rezim upravy
                    $output .= $this->actionSaveIntoDatabaseChange();
                } else {
                    // rezim pridani
                    $output .= $this->actionSaveIntoDatabaseAdd();
                }
            }
            // else {
            // } // konec else ( !(isset(fail) ), else tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        } elseif (isset($this->form_send)) {
            $this->error = "<h4>Chybí povinné údaje !!! ( aktuálně jsou povinné:  nick, vs, k platbě, Fakturační skupina ) </H4>";
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if (($this->error != null) or (!isset($this->form_send))) {
            $output .= $this->error;
            $output .= $this->actionForm();

            return $output;
        }

        if ((isset($this->writed) or isset($this->updated))) {
            $output .= $this->actionShowResults();
        }

        return $output;
    }

    private function actionPrepareVars()
    {
        $output = "";

        $this->form_update_id = intval($_POST["update_id"]);
        $this->form_odeslano = $_POST["odeslano"];
        $this->form_send = $_POST["send"];
        $this->form_firma_add = $_GET["firma_add"];

        // if(($this->form_update_id > 0)) {
        //     $update_status = 1;
        // }

        if(($this->form_update_id > 0 and !(isset($this->form_send)))) { //rezim upravy

            $dotaz_upd = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka='".intval($this->form_update_id)."' ");
            $radku_upd = pg_num_rows($dotaz_upd);

            if($radku_upd == 0) {
                $output .= "<div>Chyba! Požadovaná data nelze načíst!</div>";
            } else {

                while($data = pg_fetch_array($dotaz_upd)):

                    // primy promenny
                    $this->form_nick = $data["nick"];
                    $this->form_vs = $data["vs"];
                    $this->form_k_platbe = $data["k_platbe"];
                    $this->form_jmeno = $data["jmeno"];
                    $this->form_prijmeni = $data["prijmeni"];
                    $this->form_ulice = $data["ulice"];
                    $this->form_mesto = $data["mesto"];
                    $psc = $data["psc"];
                    $email = $data["mail"];
                    $icq = $data["icq"];
                    $tel = $data["telefon"];
                    $this->firma = $data["firma"];
                    $poznamka = $data["poznamka"];
                    $ucetni_index = $data["ucetni_index"];
                    $this->form_archiv = $data["archiv"];
                    $this->form_fakt_skupina = $data["fakturacni_skupina_id"];
                    $typ_smlouvy = $data["typ_smlouvy"];
                    $fakturacni = $data["fakturacni"];
                    $splatnost = $data["splatnost"];
                    $trvani_do = $data["trvani_do"];
                    $datum_podpisu = $data["datum_podpisu"];
                    $sluzba_int = $data["sluzba_int"];
                    $sluzba_iptv = $data["sluzba_iptv"];
                    $sluzba_voip = $data["sluzba_voip"];

                    $sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
                    $sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];

                    $billing_freq = $data["billing_freq"];

                    $billing_suspend_status = $data["billing_suspend_status"];
                    $billing_suspend_reason = $data["billing_suspend_reason"];

                    $billing_suspend_start  = $data["billing_suspend_start"];
                    $billing_suspend_stop   = $data["billing_suspend_stop"];

                    //konverze z DB formatu
                    list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $billing_suspend_start);
                    $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                    list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $billing_suspend_stop);
                    $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

                endwhile;

            }

        } else { // rezim pridani, ukladani

            $this->form_nick = trim($_POST["nick2"]);
            $this->form_vs = trim($_POST["vs"]);
            $this->form_k_platbe = trim($_POST["k_platbe"]);
            $this->form_jmeno = trim($_POST["jmeno"]);
            $this->form_prijmeni = trim($_POST["prijmeni"]);
            $this->form_ulice = trim($_POST["ulice"]);

            $this->form_mesto = $_POST["mesto"];
            $psc = $_POST["psc"];
            $email = $_POST["email"];
            $icq = $_POST["icq"];
            $tel = $_POST["tel"];

            $fakturacni = $_POST["fakturacni"];
            $ftitle = $_POST["ftitle"];
            $fulice = $_POST["fulice"];
            $fmesto = $_POST["fmesto"];
            $fpsc = $_POST["fpsc"];
            $ico = $_POST["ico"];
            $dic = $_POST["dic"];
            $ucet = $_POST["ucet"];
            $splatnost = $_POST["splatnost"];
            $cetnost = $_POST["cetnost"];
            $this->firma = $_POST["firma"];
            $poznamka = $_POST["poznamka"];
            $ucetni_index = $_POST["ucetni_index"];
            $this->form_archiv = $_POST["archiv"];
            $this->form_fakt_skupina = intval($_POST["fakt_skupina"]);
            $splatnost = $_POST["splatnost"];

            $typ_smlouvy = intval($_POST["typ_smlouvy"]);

            $trvani_do = $_POST["trvani_do"];
            $datum_podpisu = $_POST["datum_podpisu"];

            $sluzba_int = intval($_POST["sluzba_int"]);
            $sluzba_iptv = intval($_POST["sluzba_iptv"]);
            $sluzba_voip = intval($_POST["sluzba_voip"]);

            $sluzba_int_id_tarifu = intval($_POST["sluzba_int_id_tarifu"]);
            $sluzba_iptv_id_tarifu = intval($_POST["sluzba_iptv_id_tarifu"]);

            $billing_freq = intval($_POST["billing_freq"]);

            $billing_suspend_status = intval($_POST["billing_suspend_status"]);
            $billing_suspend_reason = $_POST["billing_suspend_reason"];
            $billing_suspend_start  = $_POST["billing_suspend_start"];
            $billing_suspend_stop   = $_POST["billing_suspend_stop"];

            //systémove
            $this->form_send = $_POST["send"];

            if($this->form_firma_add == 2) {
                $this->firma = "";
            } elseif ($this->form_firma_add == 1) {
                $this->firma = "1";
            }

            if((strlen($splatnost) < 1)) {
                $splatnost = "15";
            }
        }

        return $output;
    }

    private function actionShowResults(): string
    {
        $output = "";

        $back = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE nick LIKE '$this->form_nick' ");
        $back_radku = pg_num_rows($back);

        while ($data_back = pg_fetch_array($back)) {
            $firma_back = $data_back["firma"];
            $archiv_back = $data_back["archiv"];
        }

        if ($archiv_back == 1) {
            $stranka = "vlastnici-archiv.php";
        } elseif ($firma_back == 1) {
            $stranka = "vlastnici2.php";
        } else {
            $stranka = "vlastnici.php";
        }

        $output .= '<table border="0" width="50%" >
                <tr>
                <td align="right">Zpět na vlastníka </td>
                <td><form action="'.$stranka.'" method="GET" >
                <input type="hidden" value="' . $this->form_nick . '" name="find" >
                <input type="submit" value="ZDE" name="odeslat" > </form></td>

                <td align="right">Restart (all iptables ) </td>
                <td><form action="work.php" method="POST" ><input type="hidden" name="iptables" value="1" >
                    <input type="submit" value="ZDE" name="odeslat" > </form> </td>
                </tr>
                </table>';

        $output .= '<br>';

        if ($firma_back == 1) {
            $output .= "<div style=\"padding-top: 10px; padding-bottom: 20px; font-size: 18px; \">
                <span style=\"font-weight: bold; \">Upozornění!</span> Změny je nutné dát vědet účetní. </div>";
        }

        $output .= '
        Objekt byl přidán/upraven , zadané údaje:<br><br> 
        <b>Nick</b>: ' . $this->form_nick . ' <br> 
        <b>VS</b>: ' . $this->form_vs . ' <br> 
        <b>K_platbě</b>: ' . $this->form_k_platbe . ' <br>';

        $output .= '<br>';

        $output .= '<b>Jméno</b>: ' . $this->form_jmeno . ' <br>
        <b>Příjmení</b>: ' . $this->form_prijmeni . ' <br>
        <b>Ulice</b>: ' . $this->form_ulice . '<br>
        <b>PSČ</b>: ' . $psc . '<br>';

        $output .= '<br>';

        $output .= '<b>e-mail</b>: ' . $email . '<br>
        <b>icq</b>: ' . $icq . '<br>
        <b>telefon</b>: ' . $tel . '<br> 
        <br>';

        $output .= '<b>firma</b>: ';

        if($this->firma == 1) {
            $output .= "Vlastníci2 - Copmany, s.r.o.";
        } else {
            $output .= "Vlastníci - FO";
        }

        $output .= "<br>";
        $output .= "<b>Archivovat: </b>";

        if($this->form_archiv == 1) {
            $output .= " Ano ";
        } else {
            $output .= " Ne ";
        }

        $output .= "<br><b>Fakturační skupina: </b> ".$this->form_fakt_skupina."<br>";

        $output .= '<b>Typ smlouvy:</b> ';

        if($typ_smlouvy == 0) {
            $output .= "[nezvoleno]";
        } elseif($typ_smlouvy == 1) {
            $output .= "[na dobu neurčitou]";
        } elseif($typ_smlouvy == 2) {
            $output .= "[na dobu určitou]";
            $output .= " ( doba trvání do: ";

            list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("-", $trvani_do);
            $trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;

            $output .= $trvani_do." )";
        } else {
            $output .= "[nelze zjistit]";
        }

        $output .= '<br>';

        $output .= '<b>Datum podpisu</b>: ';

        if((strlen($datum_podpisu) > 0)) {
            list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("-", $datum_podpisu);
            $datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
        }

        $output .= $datum_podpisu;

        $output .= '<br><br>';

        $output .= '<b>Služba Internet:</b>';

        if($sluzba_inet == 0) {
            $output .= "Ne";
        } elseif($sluzba_inet == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$sluzba_inet;
        }

        $output .= '<br>'
            . '<b>Služba IPTV:</b>';

        if($sluzba_iptv == 0) {
            $output .= "Ne";
        } elseif($sluzba_iptv == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$sluzba_iptv;
        }

        $output .= '<br>'
            . '<b>Služba VoIP:</b>';

        if($sluzba_voip == 0) {
            $output .= "Ne";
        } elseif($sluzba_voip == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$sluzba_voip;
        }

        $output .= '<br><br>';

        $output .= '<b>Pozastavené fakturace:</b> ';

        if($billing_suspend_status == 1) {
            $output .= "Ano";
        } else {
            $output .= "Ne";
        }

        $output .= "<br>";

        if($billing_suspend_status == 1) {
            list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $billing_suspend_start);
            $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

            list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $billing_suspend_stop);
            $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

            $output .= "<b>od kdy</b>: ".$billing_suspend_start."<br>\n";
            $output .= "<b>do kdy</b>: ".$billing_suspend_stop."<br>\n";

            $output .= "<b>důvod</b>: ".$billing_suspend_reason."<br>\n";
        }

        $output .= '<br>'
        . '<br><br>';

        return $output;
    }

    private function actionForm(): string
    {
        $output = "";

        $output .= '<form name="form1" method="post" action="" >
                <input type="hidden" name="send" value="true">
                <input type="hidden" name="update_id" value="'.intval($this->form_update_id).'" >'
                . '<input type="hidden" name="fakturacni" value="'.intval($fakturacni).'" >';

        $output .= $this->csrf_html;

        $output .= '<table border="0" width="100%">
            <tr>
            <td width="70">nick:
            <input type="Text" name="nick2" size="10" maxlength="20" value="'.$this->form_nick.'" ></td>'

            . '<td colspan="3" width="80" align="left" >'


            . 'vs: <input type="Text" name="vs" size="" maxlength="" value="'.$this->form_vs.'" >'

            . '<span style="padding-left: 10px; padding-right: 10px; ">'
            . 'k platbě: </span><input type="text" name="k_platbe" size="" maxlength="" value="'.$this->form_k_platbe.'" >'

            . '<span style="padding-left: 10px; padding-right: 10px; ">Splatnost (ke dni):';

        if ($this->firma == 1) {
            $output .= '<input type="text" name="splatnost" size="8" maxlength="" value="'.$splatnost.'" >';
        } else {
            $output .= "<span style=\"color: grey; \" > není dostupné </span>";
        }

        $output .= '</span>'

            . '</td>'
            . '</tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>'
            . '<td> jméno a příjmení: </td>'
            . '<td colspan="" >'
                . '<input type="text" name="jmeno" value="'.$this->form_jmeno.'" >'
                . '<input type="text" name="prijmeni" value="'.$this->form_prijmeni.'" >'
                . '</td>'

                . '<td>účetní index: <span style="padding-left: 10px; "></span>';

        //if ( $this->firma == 1)
        { $output .= '<input type="text" name="ucetni_index" value="'.$ucetni_index.'" >'; }
        //else
        //{ $output .= "<span style=\"color: grey; \" >není dostupné</span>"; }

        $output .= '</td>
                </tr>'
            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Ulice a čp. :</td>
                <td colspan="1" ><input type="text" name="ulice" size="35" maxlength="" value="'.$this->form_ulice.'" ></td>'
                . '<td>Fakturační skupina: ';

        if($this->firma == 1) {

            $output .= '<span style="padding-left: 10px;" >'

                . '<select name="fakt_skupina" size="1" >'

                ."\t\t".'<option value="0" class="vlastnici2-fakt-skupina" ';
            if ($this->form_fakt_skupina == 0) {
                $output .= " selected ";
            }
            $output .= ' > žádná </option> '."\n";

            if ($fakturacni > 0) {
                $sql = "SELECT * FROM fakturacni_skupiny WHERE typ = 2 order by nazev DESC";
            } else {
                $sql = "SELECT * FROM fakturacni_skupiny WHERE typ = 1 order by nazev DESC";
            }

            try {
                $dotaz_fakt_skup = $this->conn_mysql->query($sql);
                $dotaz_fakt_skup_radku = $dotaz_fakt_skup->num_rows;
            } catch (Exception $e) {
                $output .= "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2>";
            }

            if($dotaz_fakt_skup_radku > 0) {
                while($data_fakt_skup = $dotaz_fakt_skup->fetch_array()) {
                    $output .= "\t\t<option value=\"".$data_fakt_skup["id"]."\" ";
                    if ($this->form_fakt_skupina == $data_fakt_skup["id"]) {
                        $output .= " selected ";
                    }
                    $output .= " > ".$data_fakt_skup["nazev"];

                    if($data_fakt_skup["typ"] == 1) {
                        $output .= " (DÚ) ";
                    } elseif($data_fakt_skup["typ"] == 2) {
                        $output .= " (FÚ) ";
                    } else {
                        $output .= $data_fakt_skup["typ"];
                    }

                    $output .= " </option>\n";
                } // konec while
            } // kone if dotaz > 0

        } // konec if firma == 1
        else {
            $output .= "<span style=\"color: grey; \" >není dostupné</span>";
        }

        $output .= '        
                </select>
                </td>
            </tr>'
            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Město , PSČ: </td>
                <td colpsan="2" >
                    <input type="text" name="mesto" size="" maxlength="" value="'.$this->form_mesto.'">
                    <input type="text" name="psc" size="10" value="'.$psc.'">
                </td>'

            . '<td valign="top" rowspan="7" >'
            . 'Poznámka: <br>
                <textarea rows="10" name="poznamka" cols="40">' . $poznamka . '</textarea>
            </td>'
            . '</tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Email: </td>
                <td colspan="3" ><input type="text" name="email" size="30" value="'.$email.'" ></td>
            </tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
            <td>ICQ:</td>
            <td colspan="3" >
                <input type="text" name="icq" size="30" value="'.$icq.'">
            </td>'

            . '</tr>'

            . '<tr><td><br></td></tr>'

             .'<tr>
                <td>Telefon: </td>
                <td><input type="text" name="tel" size="30" value="'.$tel.'"> 
                </td>'

            . '</tr>'

            . '<tr><td colspan="2"><br></td></tr>'

            . '<tr>
            <td> Firma: </td>
            <td colspan="1" >'

            . '<select name="firma" size="1" onChange="self.document.forms.form1.submit()" >
            <option value="" ';
        if (($this->firma == "")) {
            $output .= " selected ";
        } $output .= ' >Fyzická os. ( vlastníci )</option>
            <option value="1" ';
        if (($this->firma == 1)) {
            $output .= " selected ";
        } $output .= ' >Company, s.r.o. ( vlastníci2 ) </option>
            </select>'
        . '</td>'
        . '<td>';

        if ($this->form_update_id > 0) {
            $output .= "<span style=\"padding-right: 20px; \" >Archivovat: </span>";

            $output .= " <select name=\"archiv\" size=\"1\" >
                <option value=\"0\"";
            if (($this->form_archiv != "1")) {
                $output .= " selected ";
            } $output .= " > Ne </option>
                <option value=\"1\"";
            if (($this->form_archiv == "1")) {
                $output .= " selected ";
            } $output .= " > Ano </option>";
        } else {
            $output .= "<br>";
        }

        $output .= '</td>
                </tr>'

                . '<tr><td colspan="3"><br></td><tr>'

                . '<tr>
                <td colspan="" >Smlouva na dobu: </td>
                <td colspan="" >';

        if($this->firma == 1) {
            $output .= '<select name="typ_smlouvy" size="1" onChange="self.document.forms.form1.submit()" >    
                <option value="0"';
            if(($typ_smlouvy == 0) or (!isset($typ_smlouvy))) {
                $output .= " selected ";
            }

            $output .= 'class="vlastnici-nezvoleno" >Nevybráno</option>
                <option value="1"';
            if($typ_smlouvy == 1) {
                $output .= " selected ";
            } $output .= ' >Neurčitou</option>
                <option value="2"';
            if($typ_smlouvy == 2) {
                $output .= " selected ";
            } $output .= ' >Určitou</option>
                </select>';
        } else {
            $output .= "<span style=\"color: gray; \" >Není dostupné</span>";
        }

        $output .= "</td>";

        $output .= "<td><span style=\"font-weight: bold;\" >Aktivace / deaktivace služeb:</span></td>";

        $output .= '</td>
                <tr>

                <tr><td colspan="3" ><br></td></tr>

                <tr>
                <td>Trvání do:</td>';

        $output .= "<td colspan=\"\" >";

        if((($typ_smlouvy == 2) and ($this->firma == 1))) {
            $output .= "<input type=\"text\" name=\"trvani_do\" value=\"".$trvani_do."\" >";
            $output .= "<span style=\"padding-left: 15px; \" >formát: ( dd.mm.rrrr )</span>";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        if($this->firma == 1) {
            //sluzba internet
            $output .= "<td><span style=\"padding-right: 40px; \" ><b>Internet:</b></span>";

            $output .= "<select name=\"sluzba_int\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_int == 0 or !isset($sluzba_int)) {
                $output .= " selected ";
            } $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_int == 1) {
                $output .= " selected ";
            } $output .= " >Ano</option>";

            $output .= "</select>";

            $output .= "</td>";

        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= '</tr>';

        if($sluzba_int == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($sluzba_int_id_tarifu == 999 or !isset($sluzba_int_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_tarify_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            while($data_tarify = $dotaz_tarify_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_tarify["id_tarifu"]."\" ";
                if($sluzba_int_id_tarifu == $data_tarify["id_tarifu"]) {
                    $output .= " selected ";
                }
                $output .= " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";

            }

            $output .= "</select>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= '<tr><td colspan="3" ><br></td></tr>
            <tr>
            <td colspan="" >Datum podpisu: </td>
            <td>';

        if ($this->firma == 1) {
            $output .= '<input type="text" name="datum_podpisu" size="10" class=tcal value='."\"".$datum_podpisu."\" > (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        $output .= "</td>";

        if($this->firma == 1) {
            //sluzba iptv
            $output .= "<td><span style=\"padding-right: 5px; \" ><b>IPTV</b> (televize): </span>";

            $output .= "<select name=\"sluzba_iptv\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_iptv == 0 or !isset($sluzba_iptv)) {
                $output .= " selected ";
            }
            $output .= ">Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_iptv == 1) {
                $output .= " selected ";
            }
            $output .= ">Ano</option>";

            $output .= "</select>";

            $output .= "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= "</tr>";

        if($sluzba_iptv == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($sluzba_iptv_id_tarifu == 999 or !isset($sluzba_iptv_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_iptv_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            while($data_iptv = $dotaz_iptv_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_iptv["id_tarifu"]."\" ";
                if($sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"]) {
                    $output .= " selected ";
                }
                $output .= " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
            }

            $output .= "</select>";
            $output .= "</td>";

            $output .= "</tr>";

        }

        $output .= "<tr><td colspan=\"3\" ><br></td></tr>";

        $output .= "<tr>";
        $output .= "<td>Frekvence fakturování:</td>";

        if($this->firma == 1) {
            $output .= "<td>".
            "<select size=\"1\" name=\"billing_freq\">";

            $output .= "<option value=\"0\" ";
            if($billing_freq == 0 or empty($billing_freq)) {
                $output .= "selected";
            } $output .= " >Měsíční</option>";
            $output .= "<option value=\"1\" ";
            if($billing_freq == 1) {
                $output .= "selected";
            } $output .= " >Čtvrtletní</option>";

            $output .= "</select>".
                "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        if($this->firma == 1) {
            //sluzba VoIP
            $output .= "<td><span style=\"padding-right: 10px; \" ><b>VoIP</b> (telefon): </span>";

            $output .= "<select name=\"sluzba_voip\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_voip == 0 or !isset($sluzba_voip)) {
                $output .= " selected ";
            }
            $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_voip == 1) {
                $output .= " selected ";
            }
            $output .= ">Ano</option>";

            $output .= "</select>";

            $output .= "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= '<tr><td colspan="3" ><br></td></tr>';

        $output .= '<tr>
                    <td>Pozastavené fakturace:</td>
                    <td>';

        $output .= "<select size=\"1\" name=\"billing_suspend_status\" onChange=\"self.document.forms.form1.submit()\">";

        $output .= "<option value=\"0\" ";
        if(($billing_suspend_status == 0) or (!isset($billing_suspend_status))) {
            $output .= " selected ";
        } $output .= ">Ne</option>";
        $output .= "<option value=\"1\" ";
        if($billing_suspend_status == 1) {
            $output .= " selected ";
        } $output .= ">Ano</option>";

        $output .= "</select>";

        $output .= '</td>
            <td>Důvod pozastavení:</td>
            </tr>

            <tr>
            <td colspan="2" ><br></td>';


        if($billing_suspend_status == 1) {
            $output .= "<td rowspan=\"3\">
                <textarea type=\"text\" name=\"billing_suspend_reason\" cols=\"40\" rows=\"4\" >".htmlspecialchars($billing_suspend_reason)."</textarea>
                </td>";
        } else {
            $output .= "<td rowspan=\"3\"><span style=\"color: grey; \">Není dostupné</span></td>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_reason\" value=\"".htmlspecialchars($billing_suspend_reason)."\" >";
        }

        $output .= '</tr>
                <tr>
                    <td>Poz. fakturace - od kdy:</td>
                <td>';

        if($billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_start\" size=\"10\" class=\"tcal\" value=\"".
                htmlspecialchars($billing_suspend_start)."\" > datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_start\" value=\"".htmlspecialchars($billing_suspend_start)."\" >";
        }


        $output .= '
            </td>
            </tr>

            <tr>
            <td>Poz. fakturace - do kdy:</td>
            <td>';

        if($billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_stop\" size=\"10\" value=\"".
            htmlspecialchars($billing_suspend_stop)."\" class=\"tcal\"> datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_stop\" value=\"".htmlspecialchars($billing_suspend_stop)."\" >";
        }

        $output .= '
            </td>
            </tr>
            
            <tr><td colspan="3" ><br></td></tr>
            
            <tr>
            <td colspan="2" align="center">
            
            <hr>
            <input name="odeslano" type="submit" value="OK" >
            </td>
            <td><br></td>
            
            </tr>

            <tr><td colspan="3" ><br></td></tr>

        </table>
        </form>';

        return $output;
    }

    private function actionArchivZmen(): void
    {
        $this->action_az_pole2 .= " diferencialni data: ";

        //novy zpusob archivovani dat
        foreach($this->pole_puvodni_data as $key => $val) {
            if (!($this->vlast_upd[$key] == $val)) {
                if (!($key == "id_cloveka")) {
                    if($key == "vs") {
                        $this->action_az_pole3 .= "změna <b>Variabilního symbolu</b> z: ";
                        $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        $this->action_az_pole3 .= ", ";
                    } //konec key == vs
                    elseif($key == "archiv") {
                        $this->action_az_pole3 .= "změna <b>Archivu</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";
                    } //konec key == archiv
                    elseif($key == "sluzba_int") {
                        $this->action_az_pole3 .= "změna <b>Služba Internet</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";
                    } //konec key == sluzba_int
                    elseif($key == "sluzba_iptv") {
                        $this->action_az_pole3 .= "změna <b>Služba IPTV</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";
                    } //konec key == sluzba_iptv
                    elseif($key == "sluzba_voip") {
                        $this->action_az_pole3 .= "změna <b>Služba VoIP</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";
                    } //konec key == sluzba_voip
                    elseif($key == "billing_freq") {
                        $this->action_az_pole3 .= "změna <b>Frekvence fakturování</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Měsíční</span> na: <span class=\"az-s2\">Čtvrtletní</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Čtvrtletní</span> na: <span class=\"az-s2\">Měsíční</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";
                    }
                    // TODO: fix pretify fakt. skupiny in AZ call
                    // elseif( $key == "fakturacni_skupina_id" )
                    // {
                    //   $this->action_az_pole3 .= "změna <b>Fakturační skupiny</b> z: ";

                    //   $fs_ols_rs = $this->conn_mysql->query("SELECT nazev FROM fakturacni_skupiny WHERE id = '".intval($val)."'");
                    //   $fs_old_rs->data_seek(0);
                    //   list($fs_old) = $fs_old_rs->fetch_row();

                    //   $fs_new_rs = $this->conn_mysql->query("SELECT nazev FROM fakturacni_skupiny WHERE id = '".intval($this->vlast_upd[$key])."'");

                    //   $fs_new_rs->data_seek(0);
                    //   list($fs_new) = $fs_new->fetch_row();

                    //   if( isset($fs_old) )
                    //   { $this->action_az_pole3 .= "<span class=\"az-s1\">".$fs_old."</span> "; }
                    //   else
                    //   { $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> "; }

                    //   if( isset($fs_new) )
                    //   { $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$fs_new."</span>"; }
                    //   else
                    //   { $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>"; }

                    //   $this->action_az_pole3 .= ", ";

                    // } //end of elseif fakturacni_skupina_id
                    elseif($key == "billing_suspend_status") {
                        $this->action_az_pole3 .= "změna <b>Pozastavené fakturace</b> z: ";

                        if($val == "0" and $this->vlast_upd[$key] == "1") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>";
                        } elseif($val == "1" and $this->vlast_upd[$key] == "0") {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>";
                        } else {
                            $this->action_az_pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>";
                        }

                        $this->action_az_pole3 .= ", ";

                    } elseif($key == "billing_suspend_reason") {
                        $this->action_az_pole3 .= "změna <b>Důvod pozastavení</b> z: ";
                        $this->action_az_pole3 .= "<span class=\"az-s1\" >".$val."</span> ";
                        $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>, ";
                    } elseif($key == "billing_suspend_start") {
                        $this->action_az_pole3 .= "změna <b>Poz. fakturace - od kdy</b> z: ";

                        list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $val);
                        $val_cz = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                        $this->action_az_pole3 .= "<span class=\"az-s1\" >".$val_cz."</span> ";

                        list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $this->vlast_upd[$key]);
                        $val_cz_2 = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                        $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$val_cz_2."</span>, ";

                    } elseif($key == "billing_suspend_stop") {
                        $this->action_az_pole3 .= "změna <b>Poz. fakturace - do kdy</b> z: ";

                        list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $val);
                        $val_cz = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                        $this->action_az_pole3 .= "<span class=\"az-s1\" >".$val_cz."</span> ";

                        list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $this->vlast_upd[$key]);
                        $val_cz_2 = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                        $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$val_cz_2."</span>, ";

                    } else { // ostatni mody, nerozpoznane
                        $this->action_az_pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        $this->action_az_pole3 .= "na: <span class=\"az-s2\">".$this->vlast_upd[$key]."</span>, ";
                    }
                } //konec if nejde li od id cloveka
            } // konec if obj == val
        } // konec foreach

        //$this->action_az_pole2 .=",<br> stavajici data: ";

        foreach ($this->action_vlast_upd_old as $key => $val) {
            //if( $key == "id_cloveka" )
            { $this->action_az_pole2 .= " [".$key."] => ".$val." , "; }
        }

        $this->action_az_pole2 .= "".$this->action_az_pole3;

        if ($this->action_affected == 1) {
            $vysledek_write = 1;
        } else {
            $vysledek_write = 0;
        }

        $id = DB::table('archiv_zmen')
                        ->insertGetId([
                            'akce' => $this->action_az_pole2,
                            'vysledek' => $vysledek_write,
                            'provedeno_kym' => \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email
                        ]);

        if($id > 0) {
            echo "<br><H3><div style=\"color: green;\" >Změna byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
        } else {
            echo "<br><H3><div style=\"color: red;\" >Chyba! Změnu do archivu změn se nepodařilo přidat.</div></H3>\n";
        }
    }

    private function actionSaveIntoDatabaseAdd(): string
    {
        $output = "";

        if((strlen($trvani_do) > 0)) {
            list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = preg_split("/\./", $trvani_do);
            $trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
        }

        if((strlen($datum_podpisu) > 0)) {
            list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = preg_split("/\./", $datum_podpisu);
            $datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
        }


        $vlastnik_add = array( "nick" => $this->form_nick ,  "vs" => $this->form_vs, "k_platbe" => $this->form_k_platbe,
            "jmeno" => $this->form_jmeno, "prijmeni" => $this->form_prijmeni, "ulice" => $this->form_ulice,
            "mesto" => $this->form_mesto, "psc" => $psc, "ucetni_index" => $ucetni_index,
            "fakturacni_skupina_id" => $this->form_fakt_skupina, "splatnost" => $splatnost,
            "typ_smlouvy" => $typ_smlouvy, "sluzba_int" => $sluzba_int,
            "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
            "billing_freq" => $billing_freq );

        if ((strlen($this->firma) > 0)) {
            $vlastnik_add["firma"] = $this->firma;
        }
        if ((strlen($email) > 0)) {
            $vlastnik_add["mail"] = $email;
        }
        if ($icq > 0) {
            $vlastnik_add["icq"] = $icq;
        }
        if ((strlen($tel) > 0)) {
            $vlastnik_add["telefon"] = $tel;
        }
        if ($ucetni_index > 0) {
            $vlastnik_add["ucetni_index"] = $ucetni_index;
        }
        if ((strlen($poznamka) > 0)) {
            $vlastnik_add["poznamka"] = $poznamka;
        }
        if ((strlen($trvani_do) > 0)) {
            $vlastnik_add["trvani_do"] = $trvani_do;
        }
        if ((strlen($datum_podpisu) > 0)) {
            $vlastnik_add["datum_podpisu"] = $datum_podpisu;
        }

        if ($this->form_fakt_skupina < 1) {
            $this->vlast_upd["fakturacni_skupina_id"] = null;
        }

        if($sluzba_int == 1) {
            $vlast_add["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu;
        }
        if($sluzba_iptv == 1) {
            $vlast_add["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu;
        }

        if($billing_suspend_status == 1) {
            $vlastnik_add["billing_suspend_status"] = intval($billing_suspend_status);
            $vlastnik_add["billing_suspend_reason"] = $this->conn_mysql->real_escape_string($billing_suspend_reason);

            list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $billing_suspend_start);
            $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

            list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $billing_suspend_stop);
            $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

            $vlastnik_add["billing_suspend_start"] = $this->conn_mysql->real_escape_string($billing_suspend_start);
            $vlastnik_addd["billing_suspend_stop"] = $this->conn_mysql->real_escape_string($billing_suspend_stop);
        }

        $res = pg_insert($this->conn_pgsql, 'vlastnici', $vlastnik_add);

        if($res) {
            $this->alert_type = "success";
            $this->alert_content = "Data úspěšně uloženy do databáze vlastníků.";
        } else {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Data do databáze vlastníků nelze uložit. </br>".pg_last_error($this->conn_pgsql);
        }

        $this->smarty->assign("alert_type", $this->alert_type);
        $this->smarty->assign("alert_content", $this->alert_content);

        // pridame to do archivu zmen
        $pole = "<b>akce: pridani vlastnika ; </b><br>";

        foreach($vlastnik_add as $key => $val) {
            $pole = $pole." [".$key."] => ".$val."\n";
        }

        if ($res) {
            $vysledek_write = 1;
        } else {
            $vysledek_write = 0;
        }

        $id = DB::table('archiv_zmen')
                ->insertGetId([
                    'akce' => $pole,
                    'vysledek' => $vysledek_write,
                    'provedeno_kym' => \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email
                ]);

        if($id > 0) {
            $this->alert_type = "success";
            $this->alert_content = "Změna byla úspěšně zaznamenána do archivu změn.";
        } else {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Změnu do archivu změn se nepodařilo přidat.";
        }

        $this->smarty->assign("alert_type2", $this->alert_type);
        $this->smarty->assign("alert_content2", $this->alert_content);

        // $add=$this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");

        $this->writed = "true";

        return $output;
    }

    private function actionSaveIntoDatabaseChange(): string
    {
        $output = "";

        //prvne stavajici data docasne ulozime
        $this->action_az_pole2 = "<b>akce: uprava vlastnika; </b><br>";

        $vysl4 = pg_query($this->conn_pgsql, "select * from vlastnici WHERE id_cloveka='".intval($this->form_update_id)."' ");
        if((pg_num_rows($vysl4) <> 1)) {
            $output .= "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>";
        } else {
            while ($data4 = pg_fetch_array($vysl4)):

                $this->action_vlast_upd_old["id_cloveka"] = $data4["id_cloveka"];

                //novy zpusob archivace - pro porovnavani zmen
                $this->pole_puvodni_data["id_cloveka"] = $data4["id_cloveka"];
                $this->pole_puvodni_data["nick"] = $data4["nick"];
                $this->pole_puvodni_data["jmeno"] = $data4["jmeno"];
                $this->pole_puvodni_data["prijmeni"] = $data4["prijmeni"];
                $this->pole_puvodni_data["ulice"] = $data4["ulice"];
                $this->pole_puvodni_data["mesto"] = $data4["mesto"];
                $this->pole_puvodni_data["psc"] = $data4["psc"];
                $this->pole_puvodni_data["icq"] = $data4["icq"];
                $this->pole_puvodni_data["mail"] = $data4["mail"];
                $this->pole_puvodni_data["telefon"] = $data4["telefon"];
                $this->pole_puvodni_data["poznamka"] = $data4["poznamka"];
                $this->pole_puvodni_data["vs"] = $data4["vs"];
                $this->pole_puvodni_data["datum_podpisu"] = $data4["datum_podpisu"];
                $this->pole_puvodni_data["k_platbe"] = $data4["k_platbe"];
                $this->pole_puvodni_data["ucetni_index"] = $data4["ucetni_index"];
                $this->pole_puvodni_data["archiv"] = $data4["archiv"];
                $this->pole_puvodni_data["fakturacni_skupina_id"] = $data4["fakturacni_skupina_id"];
                $this->pole_puvodni_data["splatnost"] = $data4["splatnost"];
                $this->pole_puvodni_data["typ_smlouvy"] = $data4["typ_smlouvy"];
                $this->pole_puvodni_data["firma"] = $data4["firma"];
                $this->pole_puvodni_data["trvani_do"] = $data4["trvani_do"];

                $this->pole_puvodni_data["sluzba_int"] = $data4["sluzba_int"];
                $this->pole_puvodni_data["sluzba_iptv"] = $data4["sluzba_iptv"];
                $this->pole_puvodni_data["sluzba_voip"] = $data4["sluzba_voip"];

                $this->pole_puvodni_data["billing_freq"] = $data4["billing_freq"];

                $this->pole_puvodni_data["billing_suspend_status"] = $data4["billing_suspend_status"];
                $this->pole_puvodni_data["billing_suspend_reason"] = $data4["billing_suspend_reason"];

                $this->pole_puvodni_data["billing_suspend_start"]  = $data4["billing_suspend_start"];
                $this->pole_puvodni_data["billing_suspend_stop"]   = $data4["billing_suspend_stop"];

                if($sluzba_int == 1) {
                    $this->pole_puvodni_data["sluzba_int_id_tarifu"] = $data4["sluzba_int_id_tarifu"];
                }

                if($sluzba_iptv == 1) {
                    $this->pole_puvodni_data["sluzba_iptv_id_tarifu"] = $data4["sluzba_iptv_id_tarifu"];
                }

                //$this->pole_puvodni_data["fakturacni"]=$data4["fakturacni"];

            endwhile;
        }

        if((strlen($trvani_do) > 0)) {
            list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = split("\.", $trvani_do);
            $trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
        }

        if((strlen($datum_podpisu) > 0)) {
            list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = split("\.", $datum_podpisu);
            $datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
        }

        if((strlen($billing_freq) <> 1)) {
            $billing_freq = 0;
        }

        $this->vlast_upd = array( "nick" => $this->form_nick, "jmeno" => $this->form_jmeno, "prijmeni" => $this->form_prijmeni, "ulice" => $this->form_ulice, "mesto" => $this->form_mesto, "psc" => $psc,
            "vs" => $this->form_vs, "k_platbe" => $this->form_k_platbe, "archiv" => $this->form_archiv, "fakturacni_skupina_id" => $this->form_fakt_skupina,
            "splatnost" => $splatnost, "trvani_do" => $trvani_do, "sluzba_int" => $sluzba_int,
            "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
            "billing_freq" => $billing_freq );

        if ((strlen($this->firma) > 0)) {
            $this->vlast_upd["firma"] = $this->firma;
        } else {
            $this->vlast_upd["firma"] = null;
        } // u firmy else musi byt

        if ((strlen($email) > 0)) {
            $this->vlast_upd["mail"] = $email;
        } else {
            $this->vlast_upd["mail"] = null;
        }

        if ($icq > 0) {
            $this->vlast_upd["icq"] = $icq;
        } else {
            $this->vlast_upd["icq"] = "";
        }

        if ((strlen($tel) > 0)) {
            $this->vlast_upd["telefon"] = $tel;
        } else {
            $this->vlast_upd["telefon"] = null;
        }

        if ($ucetni_index > 0) {
            $this->vlast_upd["ucetni_index"] = $ucetni_index;
        } else {
            $this->vlast_upd["ucetni_index"] = null;
        }

        //if ( (strlen($poznamka) > 0 ) )
        { $this->vlast_upd["poznamka"] = $poznamka; }

        if ((strlen($datum_podpisu) > 0)) {
            $this->vlast_upd["datum_podpisu"] = $datum_podpisu;
        } else {
            $this->vlast_upd["datum_podpisu"] = null;
        }

        if ((strlen($typ_smlouvy) > 0)) {
            $this->vlast_upd["typ_smlouvy"] = $typ_smlouvy;
        } else {
            $this->vlast_upd["typ_smlouvy"] = 0;
        }

        if ($this->form_fakt_skupina < 1) {
            $this->vlast_upd["fakturacni_skupina_id"] = null;
        }

        if($sluzba_int == 1) {
            $this->vlast_upd["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu;
        }

        if($sluzba_iptv == 1) {
            $this->vlast_upd["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu;
        }

        if($billing_suspend_status == 1) {
            $this->vlast_upd["billing_suspend_status"] = intval($billing_suspend_status);
            $this->vlast_upd["billing_suspend_reason"] = $this->conn_mysql->real_escape_string($billing_suspend_reason);

            list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $billing_suspend_start);
            $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

            list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $billing_suspend_stop);
            $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

            $this->vlast_upd["billing_suspend_start"]  = $this->conn_mysql->real_escape_string($billing_suspend_start);
            $this->vlast_upd["billing_suspend_stop"]   = $this->conn_mysql->real_escape_string($billing_suspend_stop);
        } else {
            $this->vlast_upd["billing_suspend_status"] = 0;
            $this->vlast_upd["billing_suspend_reason"] = null;
            $this->vlast_upd["billing_suspend_start"]  = null;
            $this->vlast_upd["billing_suspend_stop"] = null;
        }

        // $output .= "<pre>" . var_export($this->vlast_upd, true) ."</pre>";

        // $output .= "<pre>ID: " . var_export( $vlast_id, true ) ."</pre>";

        try {
            $this->action_affected = DB::connection('pgsql')
                        ->table('vlastnici')
                        ->where('id_cloveka', $this->form_update_id)
                        ->update($this->vlast_upd);
        } catch (Exception $e) {
            $error_nr = $e->getMessage();
        }

        if($this->action_affected == 1) {
            $output .= "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3> (affected: " . $this->action_affected . ")\n";
        } else {
            $output .= "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>(Error: " . $error_nr . ")\n";
        }

        $output .= $this->actionArchivZmen();

        $this->updated = "true";

        return $output;
    }

    private function checknick($nick)
    {
        $nick_check = preg_match('/^([[:alnum:]]|_|-)+$/', $nick);
        if(!($nick_check)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick.") není ve správnem formátu!!! (Povoleno alfanumerické znaky, dolní podtržítko, pomlčka)</H4></div>";
        }

        if((strlen($nick) > 20)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick.") je moc dlouhý! (Maximální délka je 20 znaků)</H4></div>";
        }

    } // konec funkce check nick

    private function checkvs($input)
    {
        $input_check = preg_match('/^([[:digit:]]+)$/', $input);
        if(!($input_check)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Variabilní symbol ( ".$input." ) není ve správnem formátu!!! (Pouze čísla)</H4></div>";
        }
    } // konec funkce check vs

    private function check_k_platbe($input)
    {
        $platba_check = preg_match('/^([[:digit:]]|\.)+$/', $input);

        if (!($platba_check)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>K_platbe ( ".$input." ) není ve správnem formátu !!! </H4></div>";
        }

    } // konec funkce check rra

    private function check_uc_index($ucetni_index)
    {
        $ui_check = preg_match('/^([[:digit:]]|\.)+$/', $ucetni_index);

        if(!($ui_check)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) není ve správnem formátu (Povoleny pouze čísla)!!! </H4></div>";
        }

        $ui_check2 = strlen($ucetni_index);

        if($ui_check2 > 5) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) překračuje povolenou délku (5 znaků) !!! </H4></div>";
        }

    } //konec funkce check_uc_index

    private function check_splatnost($number)
    {
        if (!(preg_match('/^([[:digit:]])+$/', $number))) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Splatnost (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
        }

    } //end of function check_splatnost

    private function check_icq($number)
    {
        if (!(preg_match('/^([[:digit:]])+$/', $number))) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>ICQ (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
        }

    } //end of function check_icq

    private function check_email($email)
    {
        $rs = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($rs === false) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Emailová adresa (".$email.") není ve správnem formátu!</H4></div>";
        }
    } //end of function check_icq

    private function check_tel($number)
    {
        if(!(preg_match('/^([[:digit:]])+$/', $number))) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Telefon (".$number.") není ve správnem formátu! (pouze číslice)</H4></div>";
        }

        if(strlen($number) <> 9) {

            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole Telefon (".$number.") musí obsahovat 9 číslic!</H4></div>";
        }
    } //end of function check_tel

    private function check_datum($date, $desc)
    {
        $a_date = explode('.', $date);

        $day =   intval($a_date["0"]);
        $month = intval($a_date["1"]);
        $year =  intval($a_date["2"]);

        if(!checkdate($month, $day, $year)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Datum ".$desc." (".$date.") není ve správném formátu! (dd.mm.rrrr)</H4></div>";
        }

    } //end of function check_datum

    private function check_b_reason($reason)
    {
        if((strlen($reason) > 30)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole \"Důvod pozastavení\" je moc dlouhé! Maximální počet je 30 znaků.</H4></div>";
        }

    } //end of function check_b_reason

}
