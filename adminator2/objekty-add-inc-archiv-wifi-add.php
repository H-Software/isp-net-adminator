<?php


        //zjistit, krz kterého reinharda jde objekt
        $inserted_id = Aglobal::pg_last_inserted_id($db_ok2, "objekty");

        // pridame to do archivu zmen
        $pole = "<b> akce: pridani objektu ; </b><br>";

	    $pole .= "[id_komplu]=> ".intval($inserted_id)." ";
	
        foreach ($obj_add as $key => $val) {

            if( (strlen($val) > 0) ){
                //pokud v promenne neco, tak teprve resime vlozeni do Archivu zmen

                //nahrazovani na citelné hodnoty
                if($key == "id_tarifu"){

                    $rs_tarif = $conn_mysql->query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");
                    $rs_tarif->data_seek(0);
                    list($tarif) = $rs_tarif->fetch_row();
                    $pole .= " <b>tarif</b> => ".$tarif." ,";

                }
                elseif($key == "id_nodu"){
                    $rs_nod = $conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."' ");
                    $rs_nod->data_seek(0);
                    list($nod) = $rs_nod->fetch_row();
                    $pole .= " <b>přípojný bod</b> => ".$nod." ,";
                }
                else
                if($key == "typ"){

                    if( $val == 1){ $typ = "poc (platici)"; }
                    elseif($val == 2){ $typ = "poc (free)"; }
                    elseif($val == 3){ $typ = "AP"; }
                    else
                    { $typ = $val; }

                    $pole .= " <b>Typ</b> => ".$typ." ,";

                }
                elseif( $key == "verejna"){

                    if( $val == "99"){ $vip = "Ne"; }
                    elseif($val == "1"){ $vip = "Ano"; }
                    else
                    { $vip = $val; }
                    //dalsi moznosti pripadne dodat

                    if( ($val == "1") and (array_key_exists("tunnelling_ip", $obj_add) === true) )
                    {
                        $vip = "Ano - tunelovaná";
                    }

                    $pole .= " <b>Veřejná IP</b> => ".$vip." ,";
                }
                elseif($key == "tunnelling_ip"){

                    //nic, resime v predchozim
                }
                else
                {
                    //nenaslo se nahrazovaci pravidlo, tj. pridat v "surovem" stavu

                    $pole .= " <b>[".$key."]</b> => ".$val." ,";
                }
            }

        } //end of foreach

        if( !($res === false) ){ $vysledek_write=1; }
        else{
            $vysledek_write=0;
        }

        $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                            "('".$conn_mysql->real_escape_string($pole)."',".
                            "'".$conn_mysql->real_escape_string($nick)."',".
                            "'".$vysledek_write."')");



        //automaticke osvezovani/restarty
        if( $typ_ip == 4 )
        {
            //L2TP verejka
            Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
        }

        Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart

        $reinhard_id = Aglobal::find_reinhard($inserted_id);

        //zde dodat if zda-li je NetN ci SikanaA
        if( (preg_match("/.*<b>\[dov_net\]<\/b> => n.*/", $pole) == 1) 
    	     or (preg_match("/.*<b>\[sikana_status\]<\/b> => a.*/", $pole) == 1) ){


            if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
            elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
            elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
            else{

                //nenalezet pozadovany reinhard, takze osvezime vsechny

                Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
                Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
                Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

            } //end of else - if reinhard_id

        }

        if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)
        elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
        elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
        else
        {
            Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
            Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
            Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
        }

?>
