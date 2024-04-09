<?php

class adminator {
    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;
    
    public function __construct($conn_mysql, $smarty, $logger, $auth)
    {
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        
        $this->logger->addInfo("adminator\__construct called");
	}

    function show_stats_faktury_neuhr()
    {
        //
        // vypis neuhrazenych faktur
        //
        // return hodnoty
        //
        // 0. neuhr. faktur celkem
        // 1. nf ignorovane
        // 2. nf nesparovane
        // 3. datum posl. importu
        
        $ret = array();

     try {
			$dotaz_fn = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene");
            $dotaz_fn_radku = $dotaz_fn->num_rows;
            $ret[0] = $dotaz_fn_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn4 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id");
            $dotaz_fn4_radku = $dotaz_fn4->num_rows;
            $ret[1] = $dotaz_fn4_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn2 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ");
            $dotaz_fn2_radku = $dotaz_fn2->num_rows;
            $ret[2] = $dotaz_fn2_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn3 = $this->conn_mysql->query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
            $dotaz_fn3_radku = $dotaz_fn3->num_rows;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

         while( $data3=$dotaz_fn3->fetch_array() )
         { $datum_fn3=$data3["datum"]; }
        
         if(strlen($datum_fn3) > 0){
            $ret[3] = $datum_fn3;
         } else{
            $ret[3] = "Unknown";
         }
         
        return $ret;
    }

    function synchro_db_nf()
    {
        // synchro tabulky neuhr. faktur mezi MySQL a Postgresem :)
        $this->logger->addInfo("adminator\synchro_db_nf called");

        global $db_ok2;

        $pocet_cyklu=0;

        $vymazani_pg_fn = pg_query("DELETE FROM faktury_neuhrazene");
        $this->logger->addInfo("adminator\synchro_db_nf: vymazani_pg_fn query result: ".var_export($vymazani_pg_fn, true));

        try {
            $dotaz_mysql_fn = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene ORDER BY id");
            $dotaz_mysql_fn_radku = $dotaz_mysql_fn->num_rows;
        } catch (Exception $e) {
          die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        $this->logger->addInfo("adminator\synchro_db_nf: dotaz_mysql_fn query: "
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
              $this->logger->addError("adminator\\synchro_db_nf pg_insert res failed! ".pg_last_error($db_ok2));
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

    function fn_kontrola_omezeni()
    {
        $ret = array();
        
        global $db_ok2;

        $this->logger->addInfo("adminator\\fn_kontrola_omezeni called");

        $sql_dotaz =
    
        "SELECT 
         DISTINCT ON (t2.ip)
          COALESCE(nf.id,0),
        t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.billing_suspend_status,
        t2.id_komplu, t2.ip, t2.dov_net, t2.sikana_status, 
            t2.sikana_text, nf.datsplat, nf.cislo, count(nf.id) AS nf_pocet,
        to_char(nf.datum, 'YYYY-MM') as nf_datum2
       FROM 
        vlastnici AS t1 LEFT JOIN objekty AS t2
       ON t1.id_cloveka=t2.id_cloveka 
    
       LEFT JOIN faktury_neuhrazene nf
       ON t1.id_cloveka=nf.par_id_vlastnika
    
         WHERE ( t1.archiv IS NULL OR t1.archiv = 0) 
               AND (t2.dov_net LIKE 'n' 
            OR t2.sikana_status LIKE 'a')
           AND (t1.billing_suspend_status = 0)
    
            GROUP BY t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.billing_suspend_status,
             t2.id_komplu, t2.ip, t2.dov_net, t2.sikana_status, 
                 nf.datsplat, nf.cislo, nf.datum, nf.id, t2.sikana_text";
      
       $dotaz_vlastnici = pg_query($sql_dotaz);
       if ($dotaz_vlastnici === false){
          $this->logger->addError("adminator\\fn_kontrola_omezeni pg_query dotaz_vlastnici failed! ".pg_last_error($db_ok2));
          return $ret;
       } else{
          $dotaz_vlastnici_num = pg_num_rows($dotaz_vlastnici);
          $this->logger->addInfo("adminator\\fn_kontrola_omezeni pg_query dotaz_vlastnici: "
                                  . " result: ".var_export($dotaz_vlastnici, true)
                                  . " num_rows: ".var_export($dotaz_vlastnici_num, true));
       }

       $index = 1;   
       while( $data = pg_fetch_array($dotaz_vlastnici))
       { 
              //print "objekt $i: ".$data_obj["id_komplu"]."<br>";
            $id_komplu = $data["id_komplu"];
            $id_cloveka = $data["id_cloveka"];
            $sikana_text = $data["sikana_text"];
            $nf_cislo = $data["cislo"];
    
            $nf_pocet = $data["nf_pocet"];
            $nf_datum2 = $data["nf_datum2"];
    
            $zprava = "";
    
            if( $data["dov_net"] == "n" )
            { $duvod = "netn"; }
            elseif( $data["sikana_status"] == "a")
            { 
                  $duvod = "sikana"; 
        
                  if( ereg(".+za fakturu č. [0123456789]+.+", $sikana_text) )
                {
                    list($a1, $a2) = split("za fakturu č.", $sikana_text, 2);
                    list($b1, $b2, $b3) = split(" ", $a2, 3);
    
                    $cislo_faktury_sikana = ereg_replace(" ","",$b2);
                    //print "cislo faktury: -".$cislo_faktury."-<br>";
                }
                else
                { $cislo_faktury_sikana = ""; }
    
              }
            else
            { $duvod = ""; }
    
            //$dotaz_fa = mysql_query("SELECT Cislo,DATE_FORMAT(datum, '%Y-%m') as datum2 FROM faktury_neuhrazene WHERE par_id_vlastnika = '$id_cloveka' ");
            //$dotaz_fa_num = mysql_num_rows($dotaz_fa);
    
            if( $nf_pocet == 0 )
            { //ne-nalezena dluzna faktura
    
              if( ($duvod == "sikana") and ( $cislo_faktury_sikana > 0 ) )
              { $zprava .= "<span style=\"color: red;\" > chyba! nic nedluzi, ale ma sikanu za FA </span>"; }
              else
              { $zprava .= "<span style=\"color: maroon;\" > nic nedluzi (divny) </span>"; }
            }
            elseif( $nf_pocet == 1 )
            { //k objektu nalezena 1. faktura
            
                 if( ($duvod == "sikana") and ($nf_cislo == $cislo_faktury_sikana) )
                 {
                    $platba_dotaz = pg_query("SELECT * FROM platby WHERE ( id_cloveka = '$id_cloveka' AND zaplaceno_za LIKE '$nf_datum2' ) ");
                    if ($platba_dotaz === false){
                      $this->logger->addError("adminator\\fn_kontrola_omezeni pg_query platba_dotaz failed! ".pg_last_error($db_ok2));
                    }

                    $platba_dotaz_num = pg_num_rows($platba_dotaz);
    
                    if( $platba_dotaz_num > 0 )
                    {
                      $zprava .= "<span style=\"color: red;\" > chyba! existuje hot. platba a ma sikanu za Neuhr. FA</span>";
                    }
                    else
                    {
                      $zprava .= "<span style=\"color: green;\" > dluzi furt (OK) </span>";
                    }
                  }
                  elseif( ($duvod == "netn") and ($nf_cislo == $cislo_faktury_sikana) )
                  {
                      $zprava .= "<span style=\"color: maroon;\" >nic nedluzi, ale ma netn (divny)</span>";
                  }
                  else
                  {
                      $zprava .= "<span style=\"color: maroon;\" > nic nedluzi, ale ma omezeni (asi za neco jinyho) </span>";
                  }
            }
            else
            { //nalezeno více faktur
              $zprava .= "<span style=\"color: maroon;\" >dluzi vice faktur, neumim zjistit </span>";
            }
    
            $zaznam[] = "<b>zaznam c</b>: ".$index.", <b>id_komplu</b>: ".$id_komplu.", <b>id_cloveka</b>: ".$id_cloveka
                        . ",<b>duvod</b>: ".$duvod.", <b>cislo_fa</b>: ".$nf_cislo.", <b>cislo_fa_sikana:</b> ".$cislo_faktury_sikana
                        .". ".$zprava."<br>";
    
            $index++;
       }
       
       $ret[0] = $dotaz_vlastnici_num;
       $ret[1] = array($zaznam);

       return $ret;
    }
}