<?php

namespace App\Customer;

use App\Core\adminator;
use App\Models\FakturacniSkupina;
use Illuminate\Database\Capsule\Manager as DB;
class fakturacniSkupiny extends adminator
{


    function __construct()
    {
    }

    function getItems()
    {
        $items = array();

        // $items = FakturacniSkupina::where('active', 1)
        //     ->orderBy('name')
        //     ->take(10)
        //     ->get();

        // $fetch = FakturacniSkupina::all();
        // $items =  $fetch->toArray();

        $fetch = DB::table('fakturacni_skupiny')
                ->orderBy('id', 'desc')
                ->get();
        
        if(!is_object($fetch))
        {
            return false;
        }

        $items = $this->objectToArray($fetch);

        return $items;
    }

    function checkNazev($nazev)
    {
        $nazev_check = preg_match('/^([[:alnum:]]|_|-)+$/', $nazev);
        
        if($nazev_check === false)
        {
            global $fail;
            $fail = "true";
            
            global $error;     
            $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Název ( ".$nazev." ) obsahuje nepovolené znaky! (Povolené: čísla, písmena a-Z,_ ,- )</H4></div>";
        }

    } //konec funkce check_nazev

    function Action()
    {
          
        $update_id = $_GET["update_id"];
        if( (strlen($update_id) < 1 ) )
        { $update_id = $_POST["update_id"]; }
        
        $odeslano = $_POST["odeslano"];
        //hidden prvek, kvuli testovani promenych ..
        $send = $_POST["send"];
        
        if( ( !(preg_match('/^([[:digit:]])+$/',$update_id)) and ( $update_id > 0 ) ) )
        {
            echo "<div class=\"vlasnici-add-fail-nick\" style=\"padding-top: 10px; color: red; \">
            <H4>ID fakturační skupiny ( ".$id_fs." ) není ve správnem formátu !!!(Povolené: Čísla v desítkové soustavě.)</H4></div>";    
            exit;
        }
                
        if( ( $update_id > 0 ) ){ $update_status=1; }
        
        if( ( $update_status==1 and !( isset($send) ) ) )
        { 
            //rezim upravy - nacitani predchozich hodnot
            
            $dotaz_upd = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '$update_id' ");
            $radku_upd = $dotaz_upd->num_rows;
        
            if( $radku_upd == 0 )
            { echo "<div style=\"color: red; \" >Chyba! Požadovaná data nelze načíst! </div>"; }
            else
            {
                while( $data=$dotaz_upd->fetch_array() ):
                
                    $id = $data["id"];	
                    $nazev = $data["nazev"];
                    $typ = $data["typ"];
                    
                    $sluzba_int = $data["sluzba_int"];
                    $sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
                    $sluzba_iptv = $data["sluzba_iptv"];
                    $sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];
                    $sluzba_voip = $data["sluzba_voip"];
                    
                    $fakturacni_text = $data["fakturacni_text"];
                    $typ_sluzby = $data["typ_sluzby"];
                
                endwhile;
            
            }
          
        }
        else
        {
            // rezim pridani, ukladani
        
            $nazev = $_POST["nazev"];
            $fakturacni_text = $_POST["fakturacni_text"];
            $typ = $_POST["typ"];
            $typ_sluzby = $_POST["typ_sluzby"];
            $sluzba_int = $_POST["sluzba_int"];
            $sluzba_int_id_tarifu = $_POST["sluzba_int_id_tarifu"];
            $sluzba_iptv = $_POST["sluzba_iptv"];
            $sluzba_iptv_id_tarifu = $_POST["sluzba_iptv_id_tarifu"]; 
            $sluzba_voip = $_POST["sluzba_voip"];
         
        }
       
        //zde generovani nevyplnenych policek ...
        $fakturacni_skupina = new fakturacni_skupina;
        
        //kontrola vlozenych udaju ( kontrolujou se i vygenerovana data ... )
        if( (strlen($nazev) > 0) )
        { $fakturacni_skupina->check_nazev($nazev); }
                                               
        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if( ( ($nazev != "") and ($typ != "") and ( $typ_sluzby >= 0 ) ) ):
       
            // check duplicit v modu pridani ...
            if( ( $update_status!=1 ) )
            {
        
                //zjisti jestli neni duplicitni udaj
                $MSQ_NAZEV = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( nazev LIKE '$nazev' AND typ = '$typ' ) ");
                $MSQ_FT = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( fakturacni_text LIKE '$fakturacni_text' AND typ = '$typ' ) ");
                
                if( $MSQ_NAZEV->num_rows > 0 )
                { 
                $error .= "<div style=\"color: #CC0066; \" ><h4>Název (".$nazev.") již existuje!</h4></div>"; 
                $fail = "true"; 
                }
                if( $MSQ_FT->num_rows > 0 )
                { 
                $error .= "<div style=\"color: #CC0066; \" ><h4>Fakturační text (".$fakturacni_text.") již existuje!</h4></div>"; 
                $fail = "true"; 
                }
            
            }
        
            // check duplicit v modu uprava
            if( ( $update_status==1 and (isset($odeslano)) ) )
            {
        
                //zjisti jestli neni duplicitni dns, ip
                $MSQ_NAZEV = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( nazev LIKE '$nazev' AND typ = '$typ' AND id != '$update_id' ) ");
                $MSQ_FT = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( fakturacni_text LIKE '$fakturacni_text' AND typ = '$typ' AND id != '$update_id' ) ");
                
                if($MSQ_NAZEV->num_rows > 0)
                { $error .= "<div style=\"color: #CC0066;\" ><h4>Název (".$nazev.") již existuje!!!</h4></div>"; $fail = "true"; }
                
                if($MSQ_FT->num_rows > 0)
                { $error .= "<div style=\"color: #CC0066;\" ><h4>Fakturační text (".$fakturacni_text.") již existuje!!!</h4></div>"; $fail = "true"; }
            
            }
            
            //checkem jestli se macklo na tlacitko "OK" :)
            if( preg_match("/OK/",$odeslano) )
            { echo ""; }
            else
            { 
                $fail="true"; 
                $error .= "<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko ";
                $error .= "\"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
            }
        
            //ulozeni
            if( !( isset($fail) ) ) 
            { 
                // priprava / konverze promennych pred ulozenim ...
                //if ( $dov_net == 2 ) { $dov_net_w ="a"; } else { $dov_net_w="n"; }
                
                if( $update_status =="1" )
                {
            
                    if( !( check_level($level,140) ) ) 
                    {
                        echo "<br><div style=\"color: red; font-size: 18px; \" >Fakt. Skupiny nelze upravovat, není dostatečné oprávnění. </div><br>";
                        exit;
                    }
                    else
                    {
                        // rezim upravy
                        
                        //prvne stavajici data docasne ulozime 
                        $pole3 .= "<b>akce: uprava fakturacni skupiny; </b><br>";
                            
                        $vysl4 = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '". intval($update_id). "' ");
                    
                        if( ( $vysl4->num_rows <> 1 ) )
                        { 
                            echo "<div style=\"color: red; padding-top: 5px; padding-bottom: 5px; \" >";
                            echo "Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </div>"; 
                        }
                        else  
                        { 
                            while ($data4=$vysl4->fetch_array() ):
                        
                                //tuto tam asi bejt nemusi $pole_puvodni_data["id"] = $data4["id"];		
                                
                                $pole_puvodni_data["nazev"] = $data4["nazev"];
                                $pole_puvodni_data["typ"] = $data4["typ"];
                                $pole_puvodni_data["sluzba_int"] = $data4["sluzba_int"];
                                $pole_puvodni_data["sluzba_int_id_tarifu"] = $data4["sluzba_int_id_tarifu"];
                                $pole_puvodni_data["sluzba_iptv"] = $data4["sluzba_iptv"];
                                $pole_puvodni_data["sluzba_iptv_id_tarifu"] = $data4["sluzba_iptv_id_tarifu"];
                                $pole_puvodni_data["sluzba_voip"] = $data4["sluzba_voip"];
                                $pole_puvodni_data["fakturacni_text"] = $data4["fakturacni_text"];
                                $pole_puvodni_data["typ_sluzby"] = $data4["typ_sluzby"];
                                
                            endwhile;
                        } // konec else if radku <> 1
                        
                        //pridavani do pole pro porovnavani z archivu zmen...
                        $fs_upd["nazev"] = $nazev;		
                        $fs_upd["typ"] = $typ;
                        $fs_upd["sluzba_int"] = $sluzba_int;		
                        $fs_upd["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu;		
                        $fs_upd["sluzba_iptv"] = $sluzba_iptv;		
                        $fs_upd["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu;
                        $fs_upd["sluzba_voip"] = $sluzba_voip;		
                        $fs_upd["typ_sluzby"] = $typ_sluzby;		
                    
                        $fs_upd["fakturacni_text"] = $fakturacni_text;
                    
                        $res = $conn_mysql->query("UPDATE fakturacni_skupiny SET nazev = '$nazev', typ = '$typ',
                                    sluzba_int = '$sluzba_int', sluzba_int_id_tarifu = '$sluzba_int_id_tarifu', 
                                sluzba_iptv = '$sluzba_iptv', sluzba_iptv_id_tarifu = '$sluzba_iptv_id_tarifu',
                                sluzba_voip = '$sluzba_voip', fakturacni_text = '$fakturacni_text',
                                typ_sluzby = '$typ_sluzby' WHERE id = '$update_id' Limit 1 ");
                
                    } // konec else jestli je opravneni
            
                
                    if($res){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
                    else{ echo "<br><H3><div style=\"color: red; \" >Chyba! Data v databázi nelze změnit.</div></h3>\n"; }
            
                    echo "<div style=\"font-weight: bold; font-size: 18px; \">Změny je třeba dát vědět účetní!</div>";
                                
                    //ted vlozime do archivu zmen
                    include("vlastnici2-fs-update-inc-archiv.php");
            
                    $updated="true";
            
                }
                else
                {
                    // rezim pridani
                    $res = $conn_mysql->query("INSERT INTO fakturacni_skupiny 
                                (nazev, typ, sluzba_int, sluzba_int_id_tarifu, sluzba_iptv, sluzba_iptv_id_tarifu, 
                            sluzba_voip, fakturacni_text, typ_sluzby, vlozil_kdo) 
                                VALUES 
                            ('$nazev','$typ','$sluzba_int','$sluzba_int_id_tarifu','$sluzba_iptv',
                                '$sluzba_iptv_id_tarifu','$sluzba_voip','$fakturacni_text', '$typ_sluzby', '$nick') ");
            
                    if( $res )
                    { echo "<br><H3><div style=\"color: green;\" >Fakturační skupina úspěšně přidána do databáze.</div></H3>\n"; } 
                    else
                    { echo "<br><H3><div style=\"color: red;\" >Chyba! Fakturační skupinu nelze přidat.</div></H3>\n"; }	
                
                    // pridame to do archivu zmen
                    $pole = "<b> akce: pridani fakt. skupiny; </b><br>";
                
                    $pole .= "[nazev]=> ".$nazev.", [typ]=> ".$typ.", [sluzba_int]=> ".$sluzba_int;
                    $pole .= ", [sluzba_int_id_tarifu]=> ".$sluzba_int_id_tarifu.", [sluzba_iptv]=> ".$sluzba_iptv;
                    $pole .= ", [sluzba_iptv_id_tarifu]=> ".$sluzba_iptv_id_tarifu.", [sluzba_voip]=> ".$sluzba_voip;
                    $pole .= " [fakturacni_text]=> ".$fakturacni_text.", [typ_sluzby]=> ".$typ_sluzby;
                        
                    if( $res == 1){ $vysledek_write="1"; }
                
                    $add = $conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$nick','$vysledek_write')");
                    
                    $writed = "true"; 
                    
                    // konec else - rezim pridani
                }
        
            }
            else {} // konec else ( !(isset(fail) ), musi tu musi bejt, pac jinak nefunguje nadrazeny if-elseif
       
        elseif ( isset($send) ): 
            $error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné: název FS, Typ, Typ služby) $nazev $typ $typ_sluzby</H4>"; 
        endif;
       
        if($update_status==1)
        { echo '<h3 align="center" style="padding-top: 15px; " >Úprava fakturační skupiny</h3>'; } 
        else
        { echo '<h3 align="center" style="padding-top: 15px; " >Přidání fakturační skupiny</h3>'; }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if ( (isset($error)) or (!isset($send)) ): 
            echo $error;

            echo $info;

            // vlozeni vlastniho formu
            $this->actionForm();

        elseif ( ( isset($writed) or isset($updated) ) ): 
       
            echo '<div style="">
                <a href="vlastnici2-fakt-skupiny.php" >Zpět na "Fakturační skupiny"</a>
            </div>
            
            <br>
            fakturační skupina přidána/upravena, zadané údaje:<br><br>
            
            <b>Název skupiny</b>:' . $nazev . "<br><br>";
    
    //    <b>Typ</b>:  
    //    
    //        if( $typ == 1 )
    //        { echo "DÚ - domácí uživatel"; }
    //        elseif( $typ == 2 )
    //        { echo "FÚ - firemní uživatel"; }
    //        else
    //        { echo "Typ nelze zjistit"; }
    //    <br>
       
    //    <b>Typ služby</b>: 
    //    
    //        if( $typ_sluzby == 0 )
    //        { echo "wifi"; }
    //        elseif( $typ_sluzby == 1 )
    //        { echo "optika"; }
    //        else
    //        { echo "nelze zjistit"; }
           
    //    <br><br>
       
    //    <b>Služba "Internet"</b>: 
    //    
    //        if( $sluzba_int == 0 )
    //        { echo "Ne"; }
    //        elseif( $sluzba_int == 1 )
    //        { echo "Ano"; }
    //        else
    //        { echo "Nelze zjistit"; }
           
    //    <br>
    //    <b>Sluzba internet :: tarif</b>: echo $sluzba_int_id_tarifu;
    //    <br><br>
       
    //    <b>Služba "IPTV"</b>: 
    //    
    //        if( $sluzba_iptv == 0 )
    //        { echo "Ne"; }
    //        elseif( $sluzba_iptv == 1 )
    //        { echo "Ano"; }
    //        else
    //        { echo "Nelze zjistit"; }
       
    //    <br>
       
    //    <b>Sluzba iptv :: tarif</b>: echo $sluzba_iptv_id_tarifu;
    //    <br><br>
       
    //    <b>Služba "VoIP"</b>:
    //  
    //        if( $sluzba_voip == 0 )
    //        { echo "Ne"; }
    //        elseif( $sluzba_voip == 1 )
    //        { echo "Ano"; }
    //        else
    //        { echo "Nelze zjistit"; }
    //  
       
        endif; 
        echo "<br><br>";
    }

    function actionForm()
    {
        echo '<form name="form1" method="post" >
        <input type="hidden" name="send" value="true" >
        <input type="hidden" name="update_id" value="' . $update_id . '" >
        
        <table border="0" width="" cellspacing="5" >
        
           <tr><td colspan="2" ><br></td></tr>
        
                <tr>
                <td colspan="" >&nbsp;</td>
            </tr>
        
                <tr>
                 <td  width="50px" >Název skupiny: </td>
                 <td><input type="text" name="nazev" size="30"' . "value=\"" . $nazev . "\"></td>" .
                
             '<td width="50px" >&nbsp;</td>
             
             <td width="200px" rowspan="5" valign="top" >
             <div style="padding-bottom: 10px; " >Fakturační text:</div>
             
             <textarea name="fakturacni_text" cols="35" rows="5" >' . $fakturacni_text . '</textarea>
             
             </td>
            </tr>
        
                <tr><td colspan="2" ><br></td></tr>
        
                <tr>
                 <td  width="250px" >Typ: </td>
                  <td>
                    <select name="typ" size="1" >
                        <option value="1" '; if($typ == 1){ echo "selected "; } echo ' >DÚ - domácí uživatel</option>
                        <option value="2" '; if($typ == 2){ echo "selected "; } echo ' >FÚ - firemní uživatel</option>
                    </select>
                 </td>
                </tr>
        
                <tr><td colspan="2" ><br></td></tr>
        
                <tr>
                 <td  width="250px" >Typ služby:</td>
                  <td>
                    <select name="typ_sluzby" size="1" >
                        <option value="0" '; if($typ_sluzby == 0){ echo "selected "; } echo ' >wifi</option>
                        <option value="1" '; if($typ_sluzby == 1){ echo "selected "; } echo ' >optika</option>
                    </select>
                 </td>
                </tr>
        
                <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba internet */
        echo '<td>
                <span style="" ><b>Služba "Internet":</b></span>
              </td>
              <td>    
                <select name="sluzba_int" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( $sluzba_int == 0 or !isset($sluzba_int) ){ echo " selected "; } 
            echo ' >Ne</option>
                <option value="1" ';
                    if( $sluzba_int == 1){ echo " selected "; }
            echo ' >Ano</option>
                </select>
              </td>
            </tr>
            
            <tr>
              <td>
                <span style="" >Služba Internet :: Vyberte tarif:</span>
              </td>
              <td>';
        
               if( $sluzba_int != 1)
               {
                 echo "<span style=\"color: gray; \" >Není dostupné</span>";
                 echo "<input type=\"hidden\" name=\"sluzba_int_id_tarifu\" value=\"0\" >";
               }
               else
               {
                  //vypis tarifu
                  echo "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
                         
                 /* echo "<option value=\"0\" ";
                    if($sluzba_int_id_tarifu == 0 or !isset($sluzba_int_id_tarifu) ){ echo " selected "; }
                  echo " style=\"color: gray; \">Nevybráno</option>";
                 */							   
                  $dotaz_tarify_id_tarifu = $conn_mysql->query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
                                                
                  while( $data_tarify = $dotaz_tarify_id_tarifu->fetch_array() )
                  {
                      echo "<option value=\"".$data_tarify["id_tarifu"]."\" ";
                      if( $sluzba_int_id_tarifu == $data_tarify["id_tarifu"] ){ echo " selected "; }
                      echo " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";
                  }
                    echo "</select>";
                }// konec else if sluzba_int != 1
               
        echo '
              </td>
            </tr>
        
            <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba iptv */
        echo '<td>
                <span style="" ><b>Služba "IPTV" (televize):</b></span>
              </td>
              <td>    
                <select name="sluzba_iptv" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( $sluzba_iptv == 0 or !isset($sluzba_iptv) ){ echo " selected "; }
        echo	' >Ne</option>
                <option value="1" ';
                    if( $sluzba_iptv == 1){ echo " selected "; }
                echo ' >Ano</option>
                </select>
              </td>
            </tr>
            
            <tr>
              <td>
                <span style="" >Služba IPTV :: Vyberte tarif:</span>
              </td>
              <td>';
        
               if( $sluzba_iptv != 1)
               {
                 echo "<span style=\"color: gray; \" >Není dostupné</span>";
                 echo "<input type=\"hidden\" name=\"sluzba_iptv_id_tarifu\" value=\"0\" >";
               }
               else
               {
                  //vypis tarifu
                  echo "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
                              
                  echo "<option value=\"0\" ";
                       if($sluzba_iptv_id_tarifu == 0 or !isset($sluzba_iptv_id_tarifu) ){ echo " selected "; }
                  echo " style=\"color: gray; \">Nevybráno</option>";
                  
                  $dotaz_iptv_id_tarifu = $conn_mysql->query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
            
                  while( $data_iptv = $dotaz_iptv_id_tarifu->fetch_array() )
                  {
                     echo "<option value=\"".$data_iptv["id_tarifu"]."\" ";
                       if( $sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"] ){ echo " selected "; }
                     echo " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
                  }
             
                  echo "</select>";
                  
                }// konec else if sluzba_iptv != 1
               
        echo '
              </td>
            </tr>
            
            <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba voip */ 
        echo '
              </td>
                <span style="" ><b>Služba "VoIP":</b></span>
              </td>
              <td>    
                <select name="sluzba_voip" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( $sluzba_voip == 0 or !isset($sluzba_voip) ){ echo " selected "; } 
            echo' >Ne</option>
                <option value="1" ';
                    if( $sluzba_voip == 1 ){ echo " selected "; }
            echo ' >Ano</option>
                </select>
              </td>
            </tr>
            
            <tr><td colspan="2" ><br></td></tr>
            
           <tr><td colspan="4" ><br></td></tr>
                     
         <tr><td colspan="4" align="center" >
          <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
         </td></tr>
            
        </table>
        </form>';
    }
}
