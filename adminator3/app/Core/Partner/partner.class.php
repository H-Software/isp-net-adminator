<?php

namespace App\Partner;

use Exception;
use App\Models\PartnerOrder;
use App\Core\adminator;
use Psr\Container\ContainerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Lloricode\LaravelHtmlTable\LaravelHtmlTableGenerator;

class partner extends adminator
{
    private $validator;

    public \PgSql\Connection|\PDO|null $conn_pgsql;
    public \mysqli|\PDO $conn_mysql;

    public ?\PDO $pdoMysql;

    public \Monolog\Logger $logger;

    protected $sentinel;

    protected $loggedUserEmail;

    public $rendererTemplateName;

    public $csrf_html;

    public $url_params;

    public $listItems;

    public $paginateItemsPerPage = 15;

    private $action_form;

    private $form_fail = false;

    public $form_uri;

    private $form_error;

    private $form_jmeno_klienta;
    private $form_bydliste;
    private $form_email;
    private $form_tel;
    private $form_typ_balicku;
    private $form_typ_linky;
    private $form_pozn;
    private $form_odeslat;

    public function __construct(ContainerInterface $container)
    {
        $this->validator = $container->get('validator');
        $this->conn_mysql = $container->get('connMysql');
        $this->pdoMysql = $container->get('pdoMysql');

        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->sentinel = $container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->loggedUserEmail = $this->sentinel->getUser()->email;
    }

    public function listPrepareVars($mode = null)
    {
        $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
        $filtr_pripojeno = intval($_GET["filtr_pripojeno"]);

        if($filtr_akceptovano == 1 or $mode == "updateDesc") {
            $this->listItems = $this->listItems->where('akceptovano', "Ano");
        } elseif($filtr_akceptovano == 2 or $mode == "accept") {
            $this->listItems = $this->listItems->where('akceptovano', "Ne");
        }

        if($filtr_pripojeno == 1) {
            $this->listItems = $this->listItems->where('pripojeno', 1);
        } elseif($filtr_pripojeno == 2) {
            $this->listItems = $this->listItems->where('pripojeno', 0);
        }

        if(isset($_GET['user'])) {
            $this->listItems = $this->listItems->where('vlozil', $_GET['user']);
        }

        if($mode == "accept" or $mode == "updateDesc") {
            $this->listItems = $this->listItems->select(
                [
                    'jmeno',
                    'adresa',
                    'tel',
                    'email',
                    'poznamky',
                    'prio',
                    'vlozil',
                    'datum_vlozeni',
                    'id'
                ]
            );
        }

        if($mode == "accept" or $mode == "updateDesc") {
            $this->listItems = $this->listItems->transform(
                function ($item, $key) use ($mode) {
                    list($a, $b) = preg_split('/(?=[A-Z])/', $mode);
                    $name = strtoupper($a . " " . $b);
                    $id = $item['id'];

                    $item['id'] = "<a href=\"?id=" . $id . "\">" . $name . "</a>";
                    return $item;
                }
            );
        }


        return true;
    }

    private function getItems($mode = null)
    {
        $this->listItems = PartnerOrder::get()
            ->sortByDesc('id');

        $this->listPrepareVars($mode);

        $this->listItems = adminator::collectionPaginate(
            $this->listItems,
            $this->paginateItemsPerPage,
            $_GET['page'],
            [   // $options
                'path' => LengthAwarePaginator::resolveCurrentPath(strtok($_SERVER["REQUEST_URI"], '?')),
                'pageName' => 'page',
            ]
        );

        $data = $this->listItems->toArray();

        list($linkPreviousPage, $linkCurrentPage, $linkNextPage) = adminator::paginateGetLinks($data);
        // echo "<pre>" . var_export($data, true) . "</pre>";

        return array(
            $data,
            $linkPreviousPage,
            $linkCurrentPage,
            $linkNextPage
        );
    }

    public function list($mode = null)
    {
        $output = "";

        list(
            $data,
            $linkPreviousPage,
            $linkCurrentPage,
            $linkNextPage
        ) = $this->getItems($mode);

        if(count($data) == 0) {
            $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
            return array($output);
        }

        $headers = [
            'id',
            'telefon',
            'jmeno',
            'adresa',
            'email',
            'poznamka',
            'priorita',
            'vlozil kdo',
            'datum vlozeni',
            'pripojeno',
            'pripojeno linka',
            'typ balicku',
            'typ linky',
            'akceptovano'
            // 'datum vlozeni2'
        ] ;

        $attributes = 'class="a-common-table a-common-table-1line" '
        . 'id="partner-order-table" '
        . 'style="width: 99%"'
        ;

        $listTable = new LaravelHtmlTableGenerator();

        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

        $output .= $listTable->generate($headers, $data['data'], $attributes);

        $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

        return array($output);
    }

    private function addPrepareVars(): void
    {
        $this->form_jmeno_klienta = $_POST["jmeno_klienta"];
        $this->form_bydliste = $_POST["bydliste"];
        $this->form_email = $_POST["email"];
        $this->form_tel = $_POST["tel"];
        $this->form_typ_balicku = intval($_POST["typ_balicku"]);
        $this->form_typ_linky = intval($_POST["typ_linky"]);

        $this->form_pozn = $_POST["pozn"];
        $this->form_odeslat = $_POST["odeslat"];

        // TODO: add validation

    }

    private function addSaveData()
    {
        $data['Jméno a příjmení klienta'] = $this->form_jmeno_klienta;
        $data['Bydliště/přípojné místo'] = $this->form_bydliste;
        $data['Emailová adresa'] = $this->form_email;
        $data['Telefon'] = $this->form_tel;
        $data['Typ balícku'] = $this->form_typ_balicku;
        $data['Typ linky'] = $this->form_typ_linky;
        $data['Poznámka'] = $this->form_pozn;

        try {
            $item = PartnerOrder::create(
                [
                    'jmeno' => $this->form_jmeno_klienta,
                    'adresa' => $this->form_bydliste,
                    'email' => $this->form_email,
                    'tel' => $this->form_tel,
                    'poznamky' => $this->form_pozn,
                    'typ_balicku' => $this->form_typ_balicku,
                    'typ_linky' => $this->form_typ_linky,
                    'vlozil' => $this->loggedUserEmail
                ]
            );
        } catch (Exception $e) {
            $item = $e->getMessage();
        }

        return [$item, $data];
    }

    private function addRenderForm()
    {
        $form_id = "partner-order-add";

        $this->action_form = $this->formInit();

        $form_data['f_csrf'] = $this->csrf_html;
        $form_data['f_open'] = $this->action_form->open($form_id, $form_id, $this->form_uri, '', '', $this->csrf_html);
        $form_data['f_close'] = $this->action_form->close();

        $form_data['f_submit_button'] = $this->action_form->input_submit('odeslat', '', 'OK');

        $form_data['f_input_jmeno_klienta'] = $this->action_form->text('jmeno_klienta', 'Jméno a příjmení klienta', $this->form_jmeno_klienta);
        $form_data['f_input_bydliste'] = $this->action_form->text('bydliste', 'Bydliště/přípojné místo', $this->form_bydliste);

        $form_data['f_input_email'] = $this->action_form->email('email', 'Emailová adresa', $this->form_email, '', 'w-75 p-3', '');
        $form_data['f_input_tel'] = $this->action_form->tel('tel', 'Telefon', $this->form_tel);

        $form_data['f_input_typ_balicku'] = $this->action_form->number('typ_balicku', 'Typ balícku', $this->form_typ_balicku);
        $form_data['f_input_typ_linky'] = $this->action_form->number('typ_linky', 'Typ linky', $this->form_typ_linky);

        $form_data['f_input_pozn'] = $this->action_form->textarea('pozn', 'Poznámka', $this->form_pozn, 'rows="5" wrap="soft"');


        return $form_data;
    }

    public function add(): bool
    {
        $this->logger->info("partner\add called");

        $this->addPrepareVars();

        if((isset($this->form_odeslat) and ($this->form_fail == false))) {
            // mod ukladani

            list($insertRs, $insertedData) = $this->addSaveData();

            if(is_object($insertRs)) {
                $this->smarty->assign("alert_type", "success");
                $this->smarty->assign("alert_content", "Data byla úspěšně uložena.");

                $this->logger->info("partner\add: insert into database was successful");
            } else {
                $content  = "Chyba! Data se nepodařilo uložit. <br> Data nelze uložit do databáze!";
                $content .= '<div>(' . $insertRs . ")</div>";

                $this->smarty->assign("alert_type", "danger");
                $this->smarty->assign("alert_content", $content);

                $this->logger->error("partner\add: insert into database has failed. Error message: " . var_export($insertRs, true));
            }

            // $this->logger->info("partner\add insertedData: " . var_export($insertedData, true));

            $this->smarty->assign("insertedData", $insertedData);

            $this->rendererTemplateName = 'partner/order-add.tpl';
            return true;
        } else {
            // zobrazime formular
            //
            if(isset($this->form_odeslat)) {
                $this->smarty->assign("form_error_message", $this->form_error);
            }

            $form_data = $this->addRenderForm();
            // $bodyContent .=  print_r($form_data);
            $this->smarty->assign($form_data);

            $this->rendererTemplateName = 'partner/order-add-form.tpl';
            return true;
        }
    }

    public function accept(): void
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $output = "";

        if ($_GET["accept"] != 1 and !isset($_GET['id'])) {
            // list view

            list(
                $data,
                $linkPreviousPage,
                $linkCurrentPage,
                $linkNextPage
            ) = $this->getItems("accept");

            if(count($data) == 0) {
                $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
                $this->smarty->assign("body", $output[0]);
                $this->rendererTemplateName = 'partner/order-accept.tpl';
                return;
            }

            $headers = [
                'jmeno',
                'adresa',
                'telefon',
                'email',
                'poznamka',
                'priorita',
                'vlozil kdo',
                'datum vlozeni',
                'akceptovat'
            ] ;

            $attributes = 'class="a-common-table a-common-table-1line" '
                        . 'id="partner-order-table" '
                        . 'style="width: 99%"'
            ;

            $listTable = new LaravelHtmlTableGenerator();

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

            $output .= $listTable->generate($headers, $data['data'], $attributes);

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);
        } elseif ($_GET["accept"] != 1) {
            // confirm form

            $output .= "<form action=\"\" method=\"GET\" >";


            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Pokud je třeba, vložte poznámku: </div>";

            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                <textarea name=\"pozn\" cols=\"50\" rows=\"6\"></textarea>
            </div>";

            $output .=  "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
            </div>";

            $output .=  "<input type=\"hidden\" name=\"accept\" value=\"1\">
                <input type=\"hidden\" name=\"id\" value=\"".intval($_GET["id"])."\" >";
            $output .=  "</form>";

        } elseif($_GET["accept"] == 1 and intval($_GET['id']) > 0) {
            // update item in DB
            $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
            $id = intval($_GET["id"]);

            try {
                $this->conn_mysql->query(
                    "UPDATE partner_klienti "
                                    . "SET akceptovano='1', "
                                        . "akceptovano_kym='". $this->loggedUserEmail ."', "
                                        . " akceptovano_pozn = '$pozn' "
                                    . "WHERE id = ".$id." Limit 1"
                );

                $this->smarty->assign("alert_type", "success");
                $this->smarty->assign("alert_content", "Zákazník úspěšně akceptován.");
            } catch (Exception $e) {
                $content  = "Chyba! Zákazníka nelze akceptovat. </br> Data nelze uložit do databáze.";
                $content .= '<div>(' . $e->getMessage() . ")</div>";

                $this->smarty->assign("alert_type", "danger");
                $this->smarty->assign("alert_content", $content);
            }
        } else {
            // unknown mode
        }

        $output = array($output);

        $this->smarty->assign("body", $output[0]);

        $this->rendererTemplateName = 'partner/order-accept.tpl';
    }

    public function updateDesc(): void
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $output = "";

        if ($_GET["accept"] != 1 and !isset($_GET['id'])) {
            // list view

            list(
                $data,
                $linkPreviousPage,
                $linkCurrentPage,
                $linkNextPage
            ) = $this->getItems("updateDesc");

            if(count($data) == 0) {
                $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi (num_rows: " . count($data) . ")</div>";
                $this->smarty->assign("body", $output[0]);
                $this->rendererTemplateName = 'partner/order-update-desc.tpl';
                return;
            }

            $headers = [
                'jmeno',
                'adresa',
                'telefon',
                'email',
                'poznamka',
                'priorita',
                'vlozil kdo',
                'datum vlozeni',
                'akceptovat'
            ] ;

            $attributes = 'class="a-common-table a-common-table-1line" '
                        . 'id="partner-order-table" '
                        . 'style="width: 99%"'
            ;

            $listTable = new LaravelHtmlTableGenerator();

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);

            $output .= $listTable->generate($headers, $data['data'], $attributes);

            $output .= adminator::paginateRenderLinks($linkPreviousPage, $linkCurrentPage, $linkNextPage);
        } elseif ($_GET["edit"] != 1) {
            // update form

            $id = intval($_GET['id']);
            $dotaz = $this->pdoMysql->query("SELECT akceptovano_pozn FROM partner_klienti WHERE id = '" . $id. "' ");
            $data = $dotaz->fetchAll();

            $output .= "<form action=\"\" method=\"GET\" >"

            . "<div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; \" >Upravte poznámku: </div>"

            . "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
                <textarea name=\"pozn\" cols=\"50\" rows=\"6\">".htmlspecialchars($data[0]["akceptovano_pozn"])."</textarea>
            </div>"

            . "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
                <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
            </div>"

            . "<input type=\"hidden\" name=\"edit\" value=\"1\">
            <input type=\"hidden\" name=\"id\" value=\"".$id."\" >"

            . "</form>";

        } elseif($_GET["edit"] == 1 and intval($_GET['id']) > 0) {
            // update in DB

            $pozn = $this->conn_mysql->real_escape_string($_GET["pozn"]);
            $id = intval($_GET["id"]);

            try {
                $this->conn_mysql->query("UPDATE partner_klienti SET akceptovano_pozn = '$pozn' WHERE id=".$id." Limit 1 ");

                $this->smarty->assign("alert_type", "success");
                $this->smarty->assign("alert_content", "Poznámka úspěšně upravena");
            } catch (Exception $e) {
                $content  = "Chyba! Poznámku nelze upravit. <br> Data nelze uložit do databáze!";
                $content .= '<div>(' . $e->getMessage() . ")</div>";

                $this->smarty->assign("alert_type", "danger");
                $this->smarty->assign("alert_content", $content);
            }

        } else {
            // unknown mode
            $output .= "unknown mode";
        }

        $output = array($output);

        $this->smarty->assign("body", $output[0]);

        $this->rendererTemplateName = 'partner/order-update-desc.tpl';
    }

    public function changeStatus(): void
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
        $output = "";

        $id_zadosti = intval($_GET['id_zadosti']);
        $pripojeno = intval($_GET["pripojeno"]);
        $akt_tarif = intval($_GET["akt_tarif"]);

        if(
            $_GET["odeslat"] == "OK"
            and (
                $id_zadosti == 0
                or
                $pripojeno == 0
                or
                $akt_tarif == 0
            )
        ) {
            $content  = "Chyba! Data nelze upravit, je třeba vyplnit všechny pole.";

            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", $content);
        }

        if (
            $id_zadosti == 0
            or
            $pripojeno == 0
            or
            $akt_tarif == 0
        ) {
            // list view
            $output .= "\n<form action=\"\" method=\"GET\" >\n"

                . "<table border=\"0\" width=\"95%\" style=\"margin-top: 20px;\">
                    <tr>\n"

                . "<td width=\"30%\" valign=\"top\" >"

                //prvni sloupec
                . "<div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
                    Vyberte zákazníka: </div>\n\n"

                . "	<select name=\"id_zadosti\" size=\"5\" >
                        <option value=\"0\" class=\"select-nevybrano\" "
                        . " selected "

                . ">Nevybráno</option>\n";

            $dotaz_zadosti = $this->conn_mysql->query("SELECT * FROM partner_klienti ORDER BY id DESC");

            while($data = $dotaz_zadosti->fetch_array()) {
                $output .= "<option value=\"".$data["id"]."\" ";
                if ($id_zadosti == $data["id"]) {
                    $output .= " selected ";
                }

                $output .= " > ".substr($data["jmeno"], 0, 22).",   ";

                $output .= substr($data["adresa"], 0, 22)."</option>\n";
            }

            $output .= "</select>"

                 . "</td>"

                 . "<td valign=\"top\" width=\"30%\" >"

                 . "<div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
              Vyberte stav pole \"Připojeno\": </div>\n"

                 . "<div style=\"padding-left: 20px; \" >

                <select name=\"pripojeno\" size=\"1\" >
                    <option value=\"0\" class=\"select-nevybrano\" ";
            if ($pripojeno == 0) {
                $output .= " selected ";
            }
            $output .= ">Nevybráno</option>\n
               <option value=\"1\" >Ano</option>\n
               <option value=\"2\" >Ne</option>\n
              </select>"

            . "<div style=\"padding-top: 20px; font-weight: bold; \">
              Vyberte stav pole \"Aktuální linka\": </div>\n\n"

            . "<div style=\"padding-top: 20px; padding-bottom: 20px; \" >

              <select name=\"akt_tarif\" size=\"1\" >
               <option value=\"0\" class=\"select-nevybrano\" ";
            if ($akt_tarif == 0) {
                $output .= " selected ";
            }
            $output .= ">Nevybráno</option>\n
               <option value=\"1\" ";
            if ($akt_tarif == 1) {
                $output .= " selected ";
            }
            $output .= ">SmallCity</option>\n
               <option value=\"2\" ";
            if ($akt_tarif == 2) {
                $output .= " selected ";
            }
            $output .= " >Metropolitní</option>\n
               <option value=\"3\" ";
            if ($akt_tarif == 3) {
                $output .= " selected ";
            }
            $output .= " >Jiná</option>\n

              </select>
              </div>"

              . "</div></td>"

              . "<td valign=\"top\" width=\"30%\" >

                <div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
                    Potvrdit: </div>\n\n"

              . "<div style=\"padding-left: 20px; \" >
              <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
                 </div></td>"

              . "</tr></table>"

              . "</form>";
        } elseif ($_GET["odeslat"] == "OK" and $id_zadosti > 1) {
            try {
                $this->conn_mysql->query(
                    "UPDATE partner_klienti "
                            . " SET pripojeno='$pripojeno', pripojeno_linka='$akt_tarif' "
                            . " WHERE id=".$id_zadosti." Limit 1 "
                );

                $this->smarty->assign("alert_type", "success");
                $this->smarty->assign("alert_content", "Pole \"Připojeno, Aktuální tarif\" úspěšně upraveno.");
            } catch (Exception $e) {
                $content  = "Chyba! Pole \"Připojeno, Aktuální tarif\" nelze upravit. </br> Data nelze uložit do databáze.";
                $content .= '<div>(' . $e->getMessage() . ")</div>";

                $this->smarty->assign("alert_type", "danger");
                $this->smarty->assign("alert_content", $content);
            }

        } else {
            // unknown state
        }

        $output = array($output);

        $this->smarty->assign("body", $output[0]);

        $this->rendererTemplateName = 'partner/order-change-status.tpl';
    }
}
