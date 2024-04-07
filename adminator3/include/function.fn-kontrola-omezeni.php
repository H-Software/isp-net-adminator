<?
 //
 //funkce pro stranku
 //

 function nacteni_vlastniku()
 {
   $dotaz_vlastnici = pg_query("SELECT id_cloveka FROM vlastnici WHERE ( firma = '1' AND ( archiv = '0' OR archiv IS NULL ) ) ");
   $dotaz_vlastnici_num = pg_num_rows($dotaz_vlastnici);

   while( $data = pg_fetch_array($dotaz_vlastnici))
   { $vlastnici[] = $data["id_cloveka"]; }

   return array($vlastnici,$dotaz_vlastnici_num);
 }

 function vyber_objektu($pole_vlastniku)
 {
    foreach ($pole_vlastniku as $val)
    {
       $dotaz_obj_sloupce = "id_komplu, id_cloveka, dov_net, sikana_status, sikana_text";
       $dotaz_obj = pg_query("SELECT ".$dotaz_obj_sloupce." FROM objekty WHERE id_cloveka = '$val' ");
       //$dotaz_obj_num = pg_num_rows($dotaz_obj);

       while($data_obj = pg_fetch_array($dotaz_obj))
       {
         if( ($data_obj["dov_net"] == "n") or ($data_obj["sikana_status"] == "a") )
         {
            //print "objekt $i: ".$data_obj["id_komplu"]."<br>";
            $id_komplu = $data_obj["id_komplu"];
            $id_cloveka = $data_obj["id_cloveka"];
            $sikana_text = $data_obj["sikana_text"];

            if( ereg(".+za fakturu č. [0123456789]+.+", $sikana_text) )
            {
                list($a1, $a2) = split("za fakturu č.", $sikana_text, 2);
                list($b1, $b2, $b3) = split(" ", $a2, 3);

                $cislo_faktury = ereg_replace(" ","",$b2);
                //print "cislo faktury: -".$cislo_faktury."-<br>";
            }
            else
            { $cislo_faktury = ""; }

            if( $data_obj["dov_net"] == "n" )
            { $duvod = "netn"; }
            elseif( $data_obj["sikana_status"] == "a")
            { $duvod = "sikana"; }
            else
            { $duvod = ""; }

            $objekty_kontrola[][$id_komplu][$id_cloveka][$duvod] = $cislo_faktury;
         }
       }
     } //konec cyklu foreach

     return $objekty_kontrola;

 } //konec funkce vyber_objektu

 function kontrola($objekty_kontrola)
 {
    while (list($index) = each($objekty_kontrola))
    {
      while (list($id_komplu) = each($objekty_kontrola[$index]))
      {
        while (list($id_cloveka) = each($objekty_kontrola[$index][$id_komplu]))
        {
          while (list($duvod, $c_f) = each($objekty_kontrola[$index][$id_komplu][$id_cloveka]))
          {

            $dotaz_fa = mysql_query("SELECT Cislo,DATE_FORMAT(datum, '%Y-%m') as datum2 FROM faktury_neuhrazene WHERE par_id_vlastnika = '$id_cloveka' ");
            $dotaz_fa_num = mysql_num_rows($dotaz_fa);

            $zprava = "";
            $datum2 = "";
            $db_c_f = "";

            if( $dotaz_fa_num == 0 )
            { //ne-nalezena dluzna faktura

              if( ($duvod == "sikana") and ( $c_f > 0 ) )
              { $zprava .= "<span style=\"color: red;\" > chyba! nic nedluzi, ale ma sikanu za FA </span>"; }
              else
              { $zprava .= "<span style=\"color: maroon;\" > nic nedluzi (divny) </span>"; }
            }
            elseif( $dotaz_fa_num == 1 )
            { //k objektu nalezena 1. faktura

              while( $data_fa = mysql_fetch_array($dotaz_fa) )
              {
                $db_c_f = $data_fa["Cislo"];
                $datum2 = $data_fa["datum2"];
              }

              if( ($duvod == "sikana") and ($c_f == $db_c_f) )
              {
                $platba_dotaz = pg_query("SELECT * FROM platby WHERE ( id_cloveka = '$id_cloveka' AND zaplaceno_za LIKE '$datum2' ) ");
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
              elseif( ($duvod == "netn") and ($c_f == $db_c_f) )
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
            . ",<b>duvod</b>: ".$duvod.", <b>cislo_fa</b>: ".$c_f.". ".$zprava."<br>";

          } //konec 3. sub cyklu
        } //konec 2. sub cyklu
      } //konec sub cyklu
    } //konec hlavniho while

    return $zaznam;

 } //konec funkce kontrola

 function nacti_soubory($find_string)
 {
   $handle=opendir('export/fn_check/');
   $i=0;

   while (false!==($file = readdir($handle)))
   {
      if ( $file!="." && $file!=".." && !is_dir($file) && ereg($find_string,$file) )
      {
         $soubor[$i]="$file";
         $i++;
      }
   }
   closedir($handle);

   return $soubor;
}

 function zamek($akce)
 {
    if( $akce == "lock")
    {
     /* nastavit zámek */
     $uprava=mysql_query("UPDATE fn_kontrola_omez_fa SET value = '1' WHERE id='1'");
    }
    elseif( $akce == "unlock")
    {
     /* zrusit zámek */
     $uprava = mysql_query("UPDATE fn_kontrola_omez_fa SET value = '0' WHERE id='1'");
    }    
    elseif( $akce == "status")
    {
      $stav = mysql_query("SELECT value FROM fn_kontrola_omez_fa WHERE id='1' ");
    
      while( $stav = mysql_fetch_array($stav))
      { $stav_hodnota = $data["value"]; }
      
      return $stav_hodnota;
    }
    
 } //konec funkce zamek
 
 //
 // konec funkci
 //

?>
