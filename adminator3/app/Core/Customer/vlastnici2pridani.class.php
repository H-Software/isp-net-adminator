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

    protected $cache;

    // public $container; // for calling stb class over vlastnik2_a2 class

    public $adminator; // handler for instance of adminator class

    public $alert_type;

    public $alert_content;

    public $csrf_html;

    private $error;

    private $fail;

    private $locked;

    private $lock_name;

    private $lock_handler;

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

    private $form_psc;

    private $form_email;

    private $form_icq;

    private $form_tel;

    private $form_poznamka;

    private $form_ucetni_index;

    private $form_fakturacni;

    private $form_typ_smlouvy;

    private $form_splatnost;

    private $form_trvani_do;

    private $form_datum_podpisu;

    private $form_sluzba_int;

    private $form_sluzba_iptv;

    private $form_sluzba_voip;

    private $form_sluzba_int_id_tarifu;

    private $form_sluzba_iptv_id_tarifu;

    private $form_billing_freq;

    private $form_billing_suspend_status;

    private $form_billing_suspend_reason;

    private $form_billing_suspend_start;

    private $form_billing_suspend_stop;

    private $firma;

    private $updated;

    private $writed;

    public function __construct(ContainerInterface $container, $adminator)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');
        $this->cache = $container->get('cache');

        $this->adminator = $adminator;
        if(!isset($this->adminator->userIdentityUsername) or $this->adminator->userIdentityUsername == null) {
            throw new Exception("Call " . __CLASS__ . "\\" . __FUNCTION__ . " failed: cannot get user identity!");
        }
        // $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function action(): string
    {
        $output = "";
        $this->error = null;

        $output .= $this->actionPrepareVars();

        if ($this->form_update_id > 0) {
            $this->smarty->assign("content_header", "Úprava vlastníka");

            // set lock
            $this->lock_name = 'vlastnici2pridani:update:' . $this->form_update_id;

            $this->lock_handler = $this->cache->lock($this->lock_name, 60, $this->adminator->userIdentityUsername);

            if ($this->lock_handler->get()) {
                $this->locked = true;
                $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": lock " . var_export($this->lock_name, true) . " accquired.");
                $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": lock owner " . var_export($this->lock_handler->owner(), true));

            } else {
                $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": lock for " . var_export($this->lock_name, true) . " failed.");
            }

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

            if((strlen($this->form_splatnost) > 0)) {
                vlastnici2pridani::check_splatnost($this->form_splatnost);
            }

            if((strlen($this->form_icq) > 0)) {
                vlastnici2pridani::check_icq($this->form_icq);
            }

            if((strlen($this->form_email) > 0)) {
                vlastnici2pridani::check_email($this->form_email);
            }

            if((strlen($this->form_ucetni_index) > 0)) {
                vlastnici2pridani::check_uc_index($this->form_ucetni_index);
            }

            if((strlen($this->form_tel) > 0)) {
                vlastnici2pridani::check_tel($this->form_tel);
            }

            if((strlen($this->form_datum_podpisu) > 0)) {
                vlastnici2pridani::check_datum($this->form_datum_podpisu, "Datum podpisu");
            }

            if($this->form_typ_smlouvy == 2) {
                vlastnici2pridani::check_datum($this->form_trvani_do, "Trvání do");
            } elseif((strlen($this->form_trvani_do) > 0)) {
                vlastnici2pridani::check_datum($this->form_trvani_do, "Trvání do");
            }

            if($this->form_billing_suspend_status == 1) {

                vlastnici2pridani::check_datum($this->form_billing_suspend_start, "Poz. fakturace - od kdy");
                vlastnici2pridani::check_datum($this->form_billing_suspend_stop, "Poz. fakturace - do kdy");

            }

            if((strlen($this->form_billing_suspend_reason) > 0) and ($this->form_billing_suspend_status == 1)) {
                vlastnici2pridani::check_b_reason($this->form_billing_suspend_reason);
            }

        }

        if(($this->form_update_id > 0 and !(isset($this->form_send)))) {
            // $this->form_trvani_do = "";
            if((strlen($this->form_trvani_do) > 0)) {
                list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("\-", $this->form_trvani_do);
                $this->form_trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
            }

            if((strlen($this->form_datum_podpisu) > 0)) {
                list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("\-", $this->form_datum_podpisu);
                $this->form_datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
            }

        }

        if ($this->form_update_id > 0 and $this->locked !== true) {
            // first of all, check lock

            $this->fail = "true";

            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", "Operaci nelze provést, nepodařilo se nastavit zámek. (Lock failed!)");

            $this->error .= $this->smarty->fetch('partials/bootstrap-alert-with-columns.tpl');

            $this->smarty->clearAssign(array('alert_type', 'alert_content'));
        } elseif(($this->form_nick != "") and ($this->form_vs != "") and ($this->form_k_platbe != "") and (($this->form_fakt_skupina > 0) or ($this->firma <> 1) or ($this->form_archiv == 1))) {
            // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje

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

            //checkem jestli se (NE)macklo na tlacitko "OK" :)
            if (!preg_match("/^OK$/", $this->form_odeslano)) {
                $this->fail = "true";

                $this->smarty->assign("alert_type", "info");
                $this->smarty->assign("alert_content", "Data neuloženy, nebylo použito tlačítko \"OK\".</br>Pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!");

                $this->error .= $this->smarty->fetch('partials/bootstrap-alert-with-columns.tpl');

                $this->smarty->clearAssign(array('alert_type', 'alert_content'));
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

        if($this->form_update_id > 0 and $this->locked !== true) {
            // we dont have lock, do nothing
            $output .= $this->error;
            return $output;
        }

        if (($this->error != null) or (!isset($this->form_send))) {
            // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form

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
                    $this->form_nick = trim($data["nick"]);
                    $this->form_vs = trim($data["vs"]);
                    $this->form_k_platbe = trim($data["k_platbe"]);
                    $this->form_jmeno = trim($data["jmeno"]);
                    $this->form_prijmeni = trim($data["prijmeni"]);
                    $this->form_ulice = trim($data["ulice"]);
                    $this->form_mesto = trim($data["mesto"]);
                    $this->form_psc = trim($data["psc"]);
                    $this->form_email = trim($data["mail"]);
                    $this->form_icq = trim($data["icq"]);
                    $this->form_tel = trim($data["telefon"]);
                    $this->firma = $data["firma"];
                    $this->form_poznamka = trim($data["poznamka"]);
                    $this->form_ucetni_index = trim($data["ucetni_index"]);
                    $this->form_archiv = $data["archiv"];
                    $this->form_fakt_skupina = $data["fakturacni_skupina_id"];
                    $this->form_typ_smlouvy = $data["typ_smlouvy"];
                    $this->form_fakturacni = $data["fakturacni"];
                    $this->form_splatnost = $data["splatnost"];
                    $this->form_trvani_do = $data["trvani_do"];
                    $this->form_datum_podpisu = $data["datum_podpisu"];
                    $this->form_sluzba_int = $data["sluzba_int"];
                    $this->form_sluzba_iptv = $data["sluzba_iptv"];
                    $this->form_sluzba_voip = $data["sluzba_voip"];

                    $this->form_sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
                    $this->form_sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];

                    $this->form_billing_freq = $data["billing_freq"];

                    $this->form_billing_suspend_status = $data["billing_suspend_status"];
                    $this->form_billing_suspend_reason = $data["billing_suspend_reason"];

                    $this->form_billing_suspend_start  = $data["billing_suspend_start"];
                    $this->form_billing_suspend_stop   = $data["billing_suspend_stop"];

                    //konverze z DB formatu
                    list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $this->form_billing_suspend_start);
                    $this->form_billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                    list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $this->form_billing_suspend_stop);
                    $this->form_billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

                endwhile;

            }

        } else { // rezim pridani, ukladani

            $this->form_nick = trim($_POST["nick2"]);
            $this->form_vs = trim($_POST["vs"]);
            $this->form_k_platbe = trim($_POST["k_platbe"]);
            $this->form_jmeno = trim($_POST["jmeno"]);
            $this->form_prijmeni = trim($_POST["prijmeni"]);
            $this->form_ulice = trim($_POST["ulice"]);

            $this->form_mesto = trim($_POST["mesto"]);
            $this->form_psc = trim($_POST["psc"]);
            $this->form_email = trim($_POST["email"]);
            $this->form_icq = trim($_POST["icq"]);
            $this->form_tel = trim($_POST["tel"]);

            $this->form_fakturacni = $_POST["fakturacni"];
            // $ftitle = $_POST["ftitle"];
            // $fulice = $_POST["fulice"];
            // $fmesto = $_POST["fmesto"];
            // $fpsc = $_POST["fpsc"];
            // $ico = $_POST["ico"];
            // $dic = $_POST["dic"];
            // $ucet = $_POST["ucet"];
            // $cetnost = $_POST["cetnost"];

            $this->firma = $_POST["firma"];
            $this->form_poznamka = trim($_POST["poznamka"]);
            $this->form_ucetni_index = trim($_POST["ucetni_index"]);
            $this->form_archiv = $_POST["archiv"];
            $this->form_fakt_skupina = intval($_POST["fakt_skupina"]);
            $this->form_splatnost = trim($_POST["splatnost"]);

            $this->form_typ_smlouvy = intval($_POST["typ_smlouvy"]);

            $this->form_trvani_do = $_POST["trvani_do"];
            $this->form_datum_podpisu = $_POST["datum_podpisu"];

            $this->form_sluzba_int = intval($_POST["sluzba_int"]);
            $this->form_sluzba_iptv = intval($_POST["sluzba_iptv"]);
            $this->form_sluzba_voip = intval($_POST["sluzba_voip"]);

            $this->form_sluzba_int_id_tarifu = intval($_POST["sluzba_int_id_tarifu"]);
            $this->form_sluzba_iptv_id_tarifu = intval($_POST["sluzba_iptv_id_tarifu"]);

            $this->form_billing_freq = intval($_POST["billing_freq"]);

            $this->form_billing_suspend_status = intval($_POST["billing_suspend_status"]);
            $this->form_billing_suspend_reason = $_POST["billing_suspend_reason"];
            $this->form_billing_suspend_start  = $_POST["billing_suspend_start"];
            $this->form_billing_suspend_stop   = $_POST["billing_suspend_stop"];

            //systémove
            $this->form_send = $_POST["send"];

            if($this->form_firma_add == 2) {
                $this->firma = "";
            } elseif ($this->form_firma_add == 1) {
                $this->firma = "1";
            }

            if((strlen($this->form_splatnost) < 1)) {
                $this->form_splatnost = "15";
            }
        }

        return $output;
    }

    private function actionShowResults(): string
    {
        $output = "";
        $output_return_link_vlastnik = "";

        $back = pg_query($this->conn_pgsql, "SELECT firma, archiv FROM vlastnici WHERE nick LIKE '" . $this->form_nick. "' ");
        $back_radku = pg_num_rows($back);

        if($back_radku == 0){
            $p_bs_alerts = array(
                            "info" => "test1",
                            "danger" => "Nelze načíst data pro vytvoření odkazu na vlastníka."
                            );

            $this->smarty->assign("p_bs_alerts", $p_bs_alerts);

            $output .= $this->smarty->fetch('partials/bootstrap-alert-with-columns-array.tpl');
        }
        else{
            while ($data_back = pg_fetch_array($back)) {
                $firma_back = $data_back["firma"];
                $archiv_back = $data_back["archiv"];
            }
    
            if ($archiv_back == 1) {
                $stranka = "/vlastnici/archiv";
            } elseif ($firma_back == 1) {
                $stranka = "/vlastnici2";
            } else {
                $stranka = "/vlastnici";
            }

            $output_return_link_vlastnik =
                 '<form action="'.$stranka.'" method="GET" >'
                . '<input type="hidden" value="' . $this->form_nick . '" name="find" >
                <input type="submit" value="ZDE" name="odeslat" > </form>';
        }

        $output .= '<table border="0" width="50%" >
                <tr>
                <td align="right">Zpět na vlastníka </td>
                <td>'
                . $output_return_link_vlastnik
                .'</td>

                <td align="right">Restart (all iptables ) </td>
                <td><form action="/work" method="POST" >'
                . $this->csrf_html
                . '<input type="hidden" name="iptables" value="1" >
                    <input type="submit" value="ZDE" name="odeslat" >'
                . '</form> </td>
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
        <b>PSČ</b>: ' . $this->form_psc . '<br>';

        $output .= '<br>';

        $output .= '<b>e-mail</b>: ' . $this->form_email . '<br>
        <b>icq</b>: ' . $this->form_icq . '<br>
        <b>telefon</b>: ' . $this->form_tel . '<br> 
        <br>';

        $output .= '<b>firma</b>: ';

        if($this->firma == 1) {
            $output .= "Vlastníci2 - Company, s.r.o.";
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

        if($this->form_typ_smlouvy == 0) {
            $output .= "[nezvoleno]";
        } elseif($this->form_typ_smlouvy == 1) {
            $output .= "[na dobu neurčitou]";
        } elseif($this->form_typ_smlouvy == 2) {
            $output .= "[na dobu určitou]";
            $output .= " ( doba trvání do: ";

            list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("-", $this->form_trvani_do);
            $this->form_trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;

            $output .= $this->form_trvani_do." )";
        } else {
            $output .= "[nelze zjistit]";
        }

        $output .= '<br>';

        $output .= '<b>Datum podpisu</b>: ';

        if((strlen($this->form_datum_podpisu) > 0)) {
            list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("-", $this->form_datum_podpisu);
            $this->form_datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
        }

        $output .= $this->form_datum_podpisu;

        $output .= '<br><br>';

        $output .= '<b>Služba Internet:</b>';

        if($this->form_sluzba_int == 0) {
            $output .= "Ne";
        } elseif($this->form_sluzba_int == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$this->form_sluzba_int;
        }

        $output .= '<br>'
            . '<b>Služba IPTV:</b>';

        if($this->form_sluzba_iptv == 0) {
            $output .= "Ne";
        } elseif($this->form_sluzba_iptv == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$this->form_sluzba_iptv;
        }

        $output .= '<br>'
            . '<b>Služba VoIP:</b>';

        if($this->form_sluzba_voip == 0) {
            $output .= "Ne";
        } elseif($this->form_sluzba_voip == 1) {
            $output .= "Ano";
        } else {
            $output .= "Nelze zjistit - hodnota: ".$this->form_sluzba_voip;
        }

        $output .= '<br><br>';

        $output .= '<b>Pozastavené fakturace:</b> ';

        if($this->form_billing_suspend_status == 1) {
            $output .= "Ano";
        } else {
            $output .= "Ne";
        }

        $output .= "<br>";

        if($this->form_billing_suspend_status == 1) {
            list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $this->form_billing_suspend_start);
            $this->form_billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

            list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $this->form_billing_suspend_stop);
            $this->form_billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

            $output .= "<b>od kdy</b>: ".$this->form_billing_suspend_start."<br>\n";
            $output .= "<b>do kdy</b>: ".$this->form_billing_suspend_stop."<br>\n";

            $output .= "<b>důvod</b>: ".$this->form_billing_suspend_reason."<br>\n";
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
                . '<input type="hidden" name="fakturacni" value="'.intval($this->form_fakturacni).'" >';

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
            $output .= '<input type="text" name="splatnost" size="8" maxlength="" value="'.$this->form_splatnost.'" >';
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
        { $output .= '<input type="text" name="ucetni_index" value="'.$this->form_ucetni_index.'" >'; }
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

            if (intval($this->form_fakturacni) > 0) {
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
                    <input type="text" name="psc" size="10" value="'.$this->form_psc.'">
                </td>'

            . '<td valign="top" rowspan="7" >'
            . 'Poznámka: <br>
                <textarea rows="10" name="poznamka" cols="40">' . $this->form_poznamka . '</textarea>
            </td>'
            . '</tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Email: </td>
                <td colspan="3" ><input type="text" name="email" size="30" value="'.$this->form_email.'" ></td>
            </tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
            <td>ICQ:</td>
            <td colspan="3" >
                <input type="text" name="icq" size="30" value="'.$this->form_icq.'">
            </td>'

            . '</tr>'

            . '<tr><td><br></td></tr>'

             .'<tr>
                <td>Telefon: </td>
                <td><input type="text" name="tel" size="30" value="'.$this->form_tel.'"> 
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
            if(($this->form_typ_smlouvy == 0) or (!isset($this->form_typ_smlouvy))) {
                $output .= " selected ";
            }

            $output .= 'class="vlastnici-nezvoleno" >Nevybráno</option>
                <option value="1"';
            if($this->form_typ_smlouvy == 1) {
                $output .= " selected ";
            } $output .= ' >Neurčitou</option>
                <option value="2"';
            if($this->form_typ_smlouvy == 2) {
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

        if((($this->form_typ_smlouvy == 2) and ($this->firma == 1))) {
            $output .= "<input type=\"text\" name=\"trvani_do\" value=\"".$this->form_trvani_do."\" >";
            $output .= "<span style=\"padding-left: 15px; \" >formát: ( dd.mm.rrrr )</span>";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        if($this->firma == 1) {
            //sluzba internet
            $output .= "<td><span style=\"padding-right: 40px; \" ><b>Internet:</b></span>";

            $output .= "<select name=\"sluzba_int\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($this->form_sluzba_int == 0 or !isset($this->form_sluzba_int)) {
                $output .= " selected ";
            } $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($this->form_sluzba_int == 1) {
                $output .= " selected ";
            } $output .= " >Ano</option>";

            $output .= "</select>";

            $output .= "</td>";

        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= '</tr>';

        if($this->form_sluzba_int == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($this->form_sluzba_int_id_tarifu == 999 or !isset($this->form_sluzba_int_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_tarify_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
            } catch (Exception $e) {
                $output .= "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2>";
            }

            while($data_tarify = $dotaz_tarify_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_tarify["id_tarifu"]."\" ";
                if($this->form_sluzba_int_id_tarifu == $data_tarify["id_tarifu"]) {
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
            $output .= '<input type="text" name="datum_podpisu" size="10" class=tcal value='."\"".$this->form_datum_podpisu."\" > (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        $output .= "</td>";

        if($this->firma == 1) {
            //sluzba iptv
            $output .= "<td><span style=\"padding-right: 5px; \" ><b>IPTV</b> (televize): </span>";

            $output .= "<select name=\"sluzba_iptv\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($this->form_sluzba_iptv == 0 or !isset($this->form_sluzba_iptv)) {
                $output .= " selected ";
            }
            $output .= ">Ne</option>";
            $output .= "<option value=\"1\" ";
            if($this->form_sluzba_iptv == 1) {
                $output .= " selected ";
            }
            $output .= ">Ano</option>";

            $output .= "</select>";

            $output .= "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= "</tr>";

        if($this->form_sluzba_iptv == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($this->form_sluzba_iptv_id_tarifu == 999 or !isset($this->form_sluzba_iptv_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_iptv_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
            } catch (Exception $e) {
                $output .= "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2>";
            }

            while($data_iptv = $dotaz_iptv_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_iptv["id_tarifu"]."\" ";
                if($this->form_sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"]) {
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
            if($this->form_billing_freq == 0 or empty($this->form_billing_freq)) {
                $output .= "selected";
            } $output .= " >Měsíční</option>";
            $output .= "<option value=\"1\" ";
            if($this->form_billing_freq == 1) {
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
            if($this->form_sluzba_voip == 0 or !isset($this->form_sluzba_voip)) {
                $output .= " selected ";
            }
            $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($this->form_sluzba_voip == 1) {
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
        if(($this->form_billing_suspend_status == 0) or (!isset($this->form_billing_suspend_status))) {
            $output .= " selected ";
        } $output .= ">Ne</option>";
        $output .= "<option value=\"1\" ";
        if($this->form_billing_suspend_status == 1) {
            $output .= " selected ";
        } $output .= ">Ano</option>";

        $output .= "</select>";

        $output .= '</td>
            <td>Důvod pozastavení:</td>
            </tr>

            <tr>
            <td colspan="2" ><br></td>';


        if($this->form_billing_suspend_status == 1) {
            $output .= "<td rowspan=\"3\">
                <textarea type=\"text\" name=\"billing_suspend_reason\" cols=\"40\" rows=\"4\" >".htmlspecialchars($this->form_billing_suspend_reason)."</textarea>
                </td>";
        } else {
            $output .= "<td rowspan=\"3\"><span style=\"color: grey; \">Není dostupné</span></td>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_reason\" value=\"".htmlspecialchars($this->form_billing_suspend_reason)."\" >";
        }

        $output .= '</tr>
                <tr>
                    <td>Poz. fakturace - od kdy:</td>
                <td>';

        if($this->form_billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_start\" size=\"10\" class=\"tcal\" value=\"".
                htmlspecialchars($this->form_billing_suspend_start)."\" > datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_start\" value=\"".htmlspecialchars($this->form_billing_suspend_start)."\" >";
        }


        $output .= '
            </td>
            </tr>

            <tr>
            <td>Poz. fakturace - do kdy:</td>
            <td>';

        if($this->form_billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_stop\" size=\"10\" value=\"".
            htmlspecialchars($this->form_billing_suspend_stop)."\" class=\"tcal\"> datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_stop\" value=\"".htmlspecialchars($this->form_billing_suspend_stop)."\" >";
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
            $this->alert_type = "success";
            $this->alert_content = "Změna byla úspěšně zaznamenána do archivu změn.";
        } else {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Změnu do archivu změn se nepodařilo přidat.";
        }

        $this->smarty->assign("alert_type2", $this->alert_type);
        $this->smarty->assign("alert_content2", $this->alert_content);

    }

    private function actionSaveIntoDatabaseAdd(): string
    {
        $output = "";

        if((strlen($this->form_trvani_do) > 0)) {
            list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = preg_split("/\./", $this->form_trvani_do);
            $this->form_trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
        }

        if((strlen($this->form_datum_podpisu) > 0)) {
            list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = preg_split("/\./", $this->form_datum_podpisu);
            $this->form_datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
        }


        $vlastnik_add = array( "nick" => $this->form_nick ,  "vs" => $this->form_vs, "k_platbe" => $this->form_k_platbe,
            "jmeno" => $this->form_jmeno, "prijmeni" => $this->form_prijmeni, "ulice" => $this->form_ulice,
            "mesto" => $this->form_mesto, "psc" => $this->form_psc, "ucetni_index" => $this->form_ucetni_index,
            "fakturacni_skupina_id" => $this->form_fakt_skupina, "splatnost" => $this->form_splatnost,
            "typ_smlouvy" => $this->form_typ_smlouvy, "sluzba_int" => $this->form_sluzba_int,
            "sluzba_iptv" => $this->form_sluzba_iptv, "sluzba_voip" => $this->form_sluzba_voip,
            "billing_freq" => $this->form_billing_freq );

        if ((strlen($this->firma) > 0)) {
            $vlastnik_add["firma"] = $this->firma;
        }
        if ((strlen($this->form_email) > 0)) {
            $vlastnik_add["mail"] = $this->form_email;
        }
        if ($this->form_icq > 0) {
            $vlastnik_add["icq"] = $this->form_icq;
        }
        if ((strlen($this->form_tel) > 0)) {
            $vlastnik_add["telefon"] = $this->form_tel;
        }
        if ($this->form_ucetni_index > 0) {
            $vlastnik_add["ucetni_index"] = $this->form_ucetni_index;
        }
        if ((strlen($this->form_poznamka) > 0)) {
            $vlastnik_add["poznamka"] = $this->form_poznamka;
        }
        if ((strlen($this->form_trvani_do) > 0)) {
            $vlastnik_add["trvani_do"] = $this->form_trvani_do;
        }
        if ((strlen($this->form_datum_podpisu) > 0)) {
            $vlastnik_add["datum_podpisu"] = $this->form_datum_podpisu;
        }

        if ($this->form_fakt_skupina < 1) {
            $this->vlast_upd["fakturacni_skupina_id"] = null;
        }

        if($this->form_sluzba_int == 1) {
            $vlast_add["sluzba_int_id_tarifu"] = $this->form_sluzba_int_id_tarifu;
        }
        if($this->form_sluzba_iptv == 1) {
            $vlast_add["sluzba_iptv_id_tarifu"] = $this->form_sluzba_iptv_id_tarifu;
        }

        if($this->form_billing_suspend_status == 1) {
            $vlastnik_add["billing_suspend_status"] = intval($this->form_billing_suspend_status);
            $vlastnik_add["billing_suspend_reason"] = $this->conn_mysql->real_escape_string($this->form_billing_suspend_reason);

            list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $this->form_billing_suspend_start);
            $this->form_billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

            list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $this->form_billing_suspend_stop);
            $this->form_billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

            $vlastnik_add["billing_suspend_start"] = $this->conn_mysql->real_escape_string($this->form_billing_suspend_start);
            $vlastnik_add["billing_suspend_stop"] = $this->conn_mysql->real_escape_string($this->form_billing_suspend_stop);
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

                if($this->form_sluzba_int == 1) {
                    $this->pole_puvodni_data["sluzba_int_id_tarifu"] = $data4["sluzba_int_id_tarifu"];
                }

                if($this->form_sluzba_iptv == 1) {
                    $this->pole_puvodni_data["sluzba_iptv_id_tarifu"] = $data4["sluzba_iptv_id_tarifu"];
                }

                //$this->pole_puvodni_data["fakturacni"]=$data4["fakturacni"];

            endwhile;
        }

        if((strlen($this->form_trvani_do) > 0)) {
            list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = explode("\.", $this->form_trvani_do);
            $this->form_trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
        }

        if((strlen($this->form_datum_podpisu) > 0)) {
            list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = explode("\.", $this->form_datum_podpisu);
            $this->form_datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
        }

        if((strlen($this->form_billing_freq) <> 1)) {
            $this->form_billing_freq = 0;
        }

        $this->vlast_upd = array( "nick" => $this->form_nick, "jmeno" => $this->form_jmeno, "prijmeni" => $this->form_prijmeni, "ulice" => $this->form_ulice, "mesto" => $this->form_mesto, "psc" => $this->form_psc,
            "vs" => $this->form_vs, "k_platbe" => $this->form_k_platbe, "archiv" => $this->form_archiv, "fakturacni_skupina_id" => $this->form_fakt_skupina,
            "splatnost" => $this->form_splatnost, "trvani_do" => $this->form_trvani_do, "sluzba_int" => $this->form_sluzba_int,
            "sluzba_iptv" => $this->form_sluzba_iptv, "sluzba_voip" => $this->form_sluzba_voip,
            "billing_freq" => $this->form_billing_freq );

        if ((strlen($this->firma) > 0)) {
            $this->vlast_upd["firma"] = $this->firma;
        } else {
            $this->vlast_upd["firma"] = null;
        } // u firmy else musi byt

        if ((strlen($this->form_email) > 0)) {
            $this->vlast_upd["mail"] = $this->form_email;
        } else {
            $this->vlast_upd["mail"] = null;
        }

        if ($this->form_icq > 0) {
            $this->vlast_upd["icq"] = $this->form_icq;
        } else {
            $this->vlast_upd["icq"] = "";
        }

        if ((strlen($this->form_tel) > 0)) {
            $this->vlast_upd["telefon"] = $this->form_tel;
        } else {
            $this->vlast_upd["telefon"] = null;
        }

        if ($this->form_ucetni_index > 0) {
            $this->vlast_upd["ucetni_index"] = $this->form_ucetni_index;
        } else {
            $this->vlast_upd["ucetni_index"] = null;
        }

        //if ( (strlen($this->form_poznamka) > 0 ) )
        { $this->vlast_upd["poznamka"] = $this->form_poznamka; }

        if ((strlen($this->form_datum_podpisu) > 0)) {
            $this->vlast_upd["datum_podpisu"] = $this->form_datum_podpisu;
        } else {
            $this->vlast_upd["datum_podpisu"] = null;
        }

        if ((strlen($this->form_typ_smlouvy) > 0)) {
            $this->vlast_upd["typ_smlouvy"] = $this->form_typ_smlouvy;
        } else {
            $this->vlast_upd["typ_smlouvy"] = 0;
        }

        if ($this->form_fakt_skupina < 1) {
            $this->vlast_upd["fakturacni_skupina_id"] = null;
        }

        if($this->form_sluzba_int == 1) {
            $this->vlast_upd["sluzba_int_id_tarifu"] = $this->form_sluzba_int_id_tarifu;
        }

        if($this->form_sluzba_iptv == 1) {
            $this->vlast_upd["sluzba_iptv_id_tarifu"] = $this->form_sluzba_iptv_id_tarifu;
        }

        if($this->form_billing_suspend_status == 1) {
            $this->vlast_upd["billing_suspend_status"] = intval($this->form_billing_suspend_status);
            $this->vlast_upd["billing_suspend_reason"] = $this->conn_mysql->real_escape_string($this->form_billing_suspend_reason);

            list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $this->form_billing_suspend_start);
            $this->form_billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

            list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $this->form_billing_suspend_stop);
            $this->form_billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

            $this->vlast_upd["billing_suspend_start"]  = $this->conn_mysql->real_escape_string($this->form_billing_suspend_start);
            $this->vlast_upd["billing_suspend_stop"]   = $this->conn_mysql->real_escape_string($this->form_billing_suspend_stop);
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
            $this->alert_type = "success";
            $this->alert_content = "Data v databázi úspěšně změněny. (affected:" . $this->action_affected . ")\n";
        } else {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Data v databázi nelze změnit.<br>(Error: " . $error_nr . ")\n";
        }

        $this->smarty->assign("alert_type", $this->alert_type);
        $this->smarty->assign("alert_content", $this->alert_content);

        $this->actionArchivZmen();

        $this->updated = "true";

        $this->lock_handler->release();

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

    private function check_uc_index($input)
    {
        $ui_check = preg_match('/^([[:digit:]]|\.)+$/', $input);

        if(!($ui_check)) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$input." ) není ve správnem formátu (Povoleny pouze čísla)!!! </H4></div>";
        }

        $ui_check2 = strlen($input);

        if($ui_check2 > 5) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$input." ) překračuje povolenou délku (5 znaků) !!! </H4></div>";
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

    private function check_email($input)
    {
        $rs = filter_var($input, FILTER_VALIDATE_EMAIL);
        if ($rs === false) {
            $this->fail = "true";
            $this->error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Emailová adresa (".$input.") není ve správnem formátu!</H4></div>";
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
