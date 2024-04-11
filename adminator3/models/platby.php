<?php

class platby
{
    var $conn_mysql;

    var $logger;

    function __construct($conn_mysql, $logger) {
        $this->conn_mysql = $conn_mysql;
        $this->logger = $logger;
    }

    function synchro_db_nf()
    {
        // synchro tabulky neuhr. faktur mezi MySQL a Postgresem :)
        $this->logger->addInfo("platby\synchro_db_nf called");

        global $db_ok2;

        $pocet_cyklu=0;

        $vymazani_pg_fn = pg_query("DELETE FROM faktury_neuhrazene");
        $this->logger->addInfo("platby\synchro_db_nf: vymazani_pg_fn query result: ".var_export($vymazani_pg_fn, true));

        try {
            $dotaz_mysql_fn = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene ORDER BY id");
            $dotaz_mysql_fn_radku = $dotaz_mysql_fn->num_rows;
        } catch (Exception $e) {
          die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        $this->logger->addInfo("platby\synchro_db_nf: dotaz_mysql_fn query: "
                                . "result: ".var_export($vymazani_pg_fn, true)
                                . " num_rows: ".var_export($dotaz_mysql_fn_radku, true));

        while( $data = $dotaz_mysql_fn->fetch_array() )
        {
            //vypis z mysql
            $id = $data["id"];
            $Cislo = $data["Cislo"];
            $VarSym = $data["VarSym"];
            $Datum = $data["Datum"];
            $DatSplat = $data["DatSplat"];
            $KcCelkem = $data["KcCelkem"];
            $KcLikv = $data["KcLikv"];
            $Firma = $data["Firma"];
            $Jmeno = $data["Jmeno"];
            $ICO = $data["ICO"];
            $DIC = $data["DIC"];
            $par_id_vlastnika = $data["par_id_vlastnika"];
            $par_stav = $data["par_stav"];
            $datum_vlozeni = $data["datum_vlozeni"];
            $overeno = $data["overeno"];
            $aut_email_stav = $data["aut_email_stav"];
            $aut_email_datum = $data["aut_email_datum"];
            $aut_sms_stav = $data["aut_sms_stav"];
            $aut_sms_datum = $data["aut_sms_datum"];
            $ignorovat = $data["ignorovat"];
            $po_splatnosti_vlastnik = $data["po_splatnosti_vlastnik"];

            //vlozeni do PG
            $fn_add = array( "id" => $id, "cislo" => $Cislo,"varsym" => $VarSym, "datum" => $Datum,
                                "datsplat" => $DatSplat, "kccelkem" => $KcCelkem, "kclikv" => $KcLikv,
                                "firma" => $Firma, "jmeno" => $Jmeno , "ico" => $ICO, "dic" => $DIC,
                                "par_id_vlastnika" => $par_id_vlastnika, "par_stav" => $par_stav,
                                "datum_vlozeni" => $datum_vlozeni, "overeno" => $overeno,
                                "aut_email_stav" => $aut_email_stav, "aut_email_datum" => $aut_email_datum,
                                "aut_sms_stav" => $aut_sms_stav, "aut_sms_datum" => $aut_sms_datum,
                                "ignorovat" => $ignorovat, "po_splatnosti_vlastnik" => $po_splatnosti_vlastnik
                );


            $res = pg_insert($db_ok2, 'faktury_neuhrazene', $fn_add);
            if($res === false)
            {
              $this->logger->addError("platby\\synchro_db_nf pg_insert res failed! ".pg_last_error($db_ok2));
            }
            else{
              $res_rows = pg_affected_rows($res);
              $this->logger->addInfo("adminator\synchro_db_nf: pg_insert res: "
                                      . " result: ".var_export($res, true)
                                      . " affected_rows: ".var_export($res_rows, true)
                                    );
            }

            $pocet_cyklu++;

        } //konec while 

        return $pocet_cyklu;
    }
}
