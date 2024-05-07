<?php

use Lloricode\LaravelHtmlTable\LaravelHtmlTableGenerator;

class admin
{
    private $container;
    private $conn_mysql;

    private $conn_pgsql;

    private $logger;

    public function __construct($container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');
    }

    public function levelListDbQuery()
    {
        $rs = $this->conn_mysql->query("select * from leveling order by level asc");
        $num_rows = $rs->num_rows;

        if ($num_rows > 0) {
            $data = $rs->fetch_all(MYSQLI_ASSOC);
        }

        return array($num_rows, $data);
    }

    public function levelListJson()
    {
        $r_data = array();
        $r_status = 418;
        $r_msg = "";

        // $r_data = ['username' => 'leego.sir',  'age' => 18];

        list($q_num_rows, $q_data) = $this->levelListDbQuery();
        // $this->logger->info("admin\LevelList dump q_data: " . var_export($q_data, true));

        if ($q_num_rows == 0) {
            $r_data = array(0 => "Zadné levely v databazi");
        } else {
            $r_data = $q_data;
        }

        return array($r_data, $r_status, $r_msg);
    }

    public function levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value)
    {

        $output  = "";

        if($_POST['search']) {
            $search_string = $this->conn_mysql->real_escape_string($_POST['search']);
            $this->logger->info("admin\LevelList search string: " . var_export($search_string, true));
        }

        list($q_num_rows, $q_data) = $this->levelListDbQuery();

        // $this->logger->info("admin\LevelList dump q_data: " . var_export($q_data, true));

        $output .= '<div style="padding-top: 10px; padding-left: 10px;" class="fs-5">Výpis levelů stránek</div>';

        if ($q_num_rows == 0) {
            $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Zadné levely v databazi</div>";
        } else {
            // $output .= '<table class="table table-striped fs-6">';
            $output .= '<table
							id="level-list"
							class="table table-striped fs-6"
							data-toggle="table"
							data-pagination="true"
							data-side-pagination="client"
							data-search="true"
							';

            $output .= '>';

            $output .= "\n
			<thead>
				<tr class=\"table-light\">
					<th width=\"5%\" scope=\"col\" data-field=\"id\" data-sortable=\"true\">id</th>
					<th width=\"30%\" scope=\"col\" data-field=\"name\">Popis</th>
															
					<th width=\"20%\" scope=\"col\" data-field=\level\">Level</th>
								
					<th width=\"10%\" scope=\"col\">Úprava</th>
					<th width=\"10%\" scope=\"col\">Smazání</th>
				</tr>
			</thead>
			<tbody id=\"hidden\"> <!-- hidden is used because of jquery duplicates this element -->
			\n";

            foreach ($q_data as $d) {
                $output .= "<tr>"
                . "<td scope=\"row\">".$d["id"]."</td>\n"
                . "<td >".$d["popis"]."</td>\n"
                . "<td>".$d["level"]."</td>\n"
                . '<td>
								<form method="POST" action="/admin/level-action" >
									<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">
									<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">
									<input type="hidden" name="update_id" value="'.$d['id'].'">
									<input type="submit" value="update">
								</form>'
                 . '</td>'
                . "</tr>";
            }

            $output .= "<tbody></table>";
        }
        return $output;
    }

    public function levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value)
    {

        $output = "";

        if (($_POST["popis_new"])) {
            //budeme ukladat
            $popis = $this->conn_mysql->real_escape_string($_POST["popis_new"]);
            $level = intval($_POST["level_new"]);

            $output .= "Zadáno do formuláre : <br><br>";
            $output .= "popis stránky: ".$popis."<br>";
            $output .= "level stránky: ".$level."<br>";

            $id_new = intval($_POST["new_id"]);

            if($id_new > 0) {
                // update
                $sql = "UPDATE leveling SET popis='$popis', level='$level' where id=".$id_new;
            } else {
                // novy zaznam
                $sql = "INSERT INTO leveling (popis, level) VALUES ('$popis','$level')";
            }

            try {
                $rs = $this->conn_mysql->query($sql);
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            if ($rs) {
                $output .= "<br><br>MySql potvrdilo, takze: <br><H2>Data v databazi upravena.</H2><br><br>";
            } else {
                $output .= "Houstone, tento zapis do databaze nevysel :)";
            }

        } else {
            //zobrazime formular

            //nejdrive nacteme predchozi data
            $update_id = intval($_POST["update_id"]);

            if($update_id > 0) {
                try {
                    $vysledek = $this->conn_mysql->query("select * from leveling where id = $update_id ");
                } catch (Exception $e) {
                    die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
                }

                $radku = $vysledek->num_rows;

                if ($radku == 0) {
                    $output .= "Zadné levely v db (divny) ";
                } else {
                    while ($zaznam = $vysledek->fetch_array()):
                        $id = $zaznam["id"];
                        $popis = $zaznam["popis"];
                        $level = $zaznam["level"];
                    endwhile;
                }
            }

            $output .= '
				<br><H4>Úprava levelu stránky: </H4><br>
				<form method="POST" action="'.$_SERVER['SCRIPT_URL'].'">
				<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">
				<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">
				';

            if($update_id > 0) {
                $output .= "<input type=\"hidden\" name=\"new_id\" value=\" " .$id . "\">";
            }

            $output .= '<table border="0" width="100%" id="table2">
				<tr>
				<td width="25%"><label>Popis: </label></td>
				<td><input type="text" name="popis_new" size="30" value="'.$popis.'"></td
				</tr>

				<tr>
					<td><label>Level: </label></td>
						<td><input type="text" name="level_new" size="10" value="'.$level.'"></td>
				</tr>

				<tr>
					<td><br></td>
					<td></td>
				</tr>

					<tr>
					<td></td>
					<td><input type="submit" value="OK" name="B1">
						<input type="reset" value="vymazat" name="B2"></td>
					</tr>

				</table>

				</form>';

        }

        return array($output);
    }

    public function tarifList()
    {

        $output = "";

        $sql_where = "";
        if((preg_match('/^([[:digit:]]+)$/', $_GET["id_tarifu"]))) {
            $sql_where = "WHERE id_tarifu = '".intval($_GET["id_tarifu"])."' ";
        }

        $dotaz_tarify = $this->conn_mysql->query(" SELECT * FROM tarify_int " . $sql_where . " ORDER BY id_tarifu");
        $dotaz_tarify_radku = $dotaz_tarify->num_rows;

        if($dotaz_tarify_radku == 0) {
            $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Žádné záznamy v databázi</div>";
            return array($output);
        }

        $data = $dotaz_tarify->fetch_all(MYSQLI_ASSOC);


        $headers = ['id',
            'název',
            'garant',
            'cena bez DPH',
            'Rychlost <br>download',
            'Agregace',
            'Počet <br>klientů',
            'úprava',
            'smazat'
        ] ;

        $attributes = 'class="a-common-table a-common-table-2line" '
                    . 'id="tarif-table" '
                    . 'style="width: 99%"'
        ;

        $i = 0;
        foreach ($data as $id => $a) {
            if($i == 0) {
                // second header row
                $dataView[$i] = array(
                    $headers[0] => "H",
                    $headers[1] => "Typ",
                    $headers[2] => "zkratka",
                    $headers[3] => "cena s DPH",
                    $headers[4] => "Rychlost <br>upload",
                    $headers[5] => "Agregace <br>smluvní",
                    $headers[6] => "",
                    $headers[7] => "",
                    $headers[8] => "",
                );
                $i++;
            }

            // prepare data
            $garant = $a['garant'];
            if ($garant == 1) {
                $garant = "Ano";
            } elseif ($garant == 0) {
                $garant = "Ne";
            }

            if ($a["typ_tarifu"] == 0) {
                $typ_tarifu = "wifi tarif";
            } elseif ($a["typ_tarifu"] == 1) {
                $typ_tarifu = "optický tarif";
            } else {
                $typ_tarifu = $a["typ_tarifu"];
            }

            $dotaz_lidi = pg_query("SELECT * FROM objekty WHERE id_tarifu = '". intval($a['id_tarifu']). "'");
            $dotaz_lidi_radku = pg_num_rows($dotaz_lidi);

            // first body row
            $dataView[$i] = array(
                $headers[0] => $a["id_tarifu"],
                $headers[1] => $a["jmeno_tarifu"],
                $headers[2] => $garant,
                $headers[3] => $a["cena_bez_dph"],
                $headers[4] => $a["speed_dwn"],
                $headers[5] => $a["agregace"],
                $headers[6] => $dotaz_lidi_radku,
                $headers[7] => "<a href=\"/admin/tarify/action?update_id=".$a["id_tarifu"]."\" >upravit</a>",
                $headers[8] => "<a href=\"/admin/tarify/action?erase_id=".$a["id_tarifu"]."\" >smazat</a>"
            );

            $i++;
            // second body row
            $dataView[$i] = array(
                $headers[0] => $a["id_tarifu"],
                $headers[1] => $typ_tarifu,
                $headers[2] => $a["zkratka_tarifu"],
                $headers[3] => $a["cena_s_dph"],
                $headers[4] => $a["speed_upl"],
                $headers[5] => $a["agregace_smlouva"],
                $headers[6] => "",
                $headers[7] => "",
            );
            $i++;
        }

        // echo "<pre>". var_export($dataView, true) . "</pre>";

        $table = new LaravelHtmlTableGenerator();
        $output .= $table->generate($headers, $dataView, $attributes);


        return array($output);
    }

    public function tarifAction()
    {
        $error = "";
        $update_id = $_GET["update_id"];
        $erase_id = $_GET["erase_id"];

        //kontrola promennych zde ...
        if(isset($update_id)) {
            if(!(preg_match('/^([[:digit:]])+$/', $update_id))) {
                $error .= "<div>Chyba! Update id není ve správném formátu. </div>";
            }
        }

        if(isset($erase_id)) {
            if(!(preg_match('/^([[:digit:]])+$/', $erase_id))) {
                $error .= "<div>Chyba! Erase id není ve správném formátu. </div>";
            }
        }

        if(isset($update_id)) {
            if(isset($send)) {
                //budeme ukladat ..
                $output .= "budeme ukladat ... T.B.A.";

            } else {
                //zobrazeni formu pro update ...
                $output .= "zobrazeni formu pro update .... T.B.A.";

            }

        } //konec if isset update_id
        elseif(isset($erase_id)) {
            if(isset($send)) {
                //budeme ukladat ..
                $output .= "budeme mazat ...";

            } else {
                //zobrazeni formu pro erase ...
                $output .= "zobrazeni formu pro erase ....";

            }

        } //konec if isset erase_id
        else {
            // noting to do :)
        }

        return array($output, $error);
    }

}
