<?php

namespace App\Customer;

use App\Core\adminator;
use App\Models\FakturacniSkupina;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Container\ContainerInterface;

class fakturacniSkupiny extends adminator
{
    var $db_table_name = 'fakturacni_skupiny';
    var $conn_mysql;

    var $csrf_html;

    var $form_update_id;

    var $adminator_ctl;

    var $action_form;
    
    function __construct(ContainerInterface $container, $conn_mysql = null)
    {
        $this->logger = $container->logger;

        $i = $container->auth->getIdentity();
        $this->loggedUserEmail = $i['username'];

        $this->conn_mysql = $conn_mysql;
    }

    function getItems()
    {
        $items = array();

        $items = FakturacniSkupina::get()
            ->sortByDesc('id');

        // $fetch = DB::table($this->db_table_name)
        //         ->orderBy('id', 'desc')
        //         ->get();
        
        // if(!is_object($fetch))
        // {
        //     return false;
        // }

        // $items = $this->objectToArray($fetch);

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
        $output = "";

        $this->action_form = $this->formInit();

        $update_id = $_GET["update_id"];
        if( (strlen($update_id) < 1 ) )
        { $update_id = $_POST["update_id"]; }
        
        $odeslano = $_POST["odeslano"];
        //hidden prvek, kvuli testovani promenych ..
        $send = $_POST["send"];

        if( ( !(preg_match('/^([[:digit:]])+$/',$update_id)) and ( $update_id > 0 ) ) )
        {
            $output .= "<div class=\"vlasnici-add-fail-nick\" style=\"padding-top: 10px; color: red; \">
            <H4>ID fakturační skupiny ( ".$update_id." ) není ve správnem formátu !!! (Povolené: Čísla v desítkové soustavě.)</H4></div>";    
            exit;
        }
        else{
            $this->form_update_id = $update_id;
        }

        if( ( $this->form_update_id > 0 ) ){ 
            // run this code in update mode
            $update_status=1; 
            $this->logger->addInfo("fakturacniSkupiny\Action: update mode set");

            if ($this->adminator_ctl->checkLevel(140, false) === false)
            {
                $output .= "<div class=\"alert alert-danger\" role=\"alert\">Fakturacni Skupiny nelze upravovat, není dostatečné oprávnění. </div>";
                return $output;
            }
        }
        
        if( ( $update_status==1 and !( isset($send) ) ) )
        { 
            //rezim upravy - nacitani predchozich hodnot
            $dotaz_upd = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '". intval($this->form_update_id) . "' ");
            $radku_upd = $dotaz_upd->num_rows;
        
            if( $radku_upd == 0 )
            { $output .= "<div style=\"color: red; \" >Chyba! Požadovaná data nelze načíst! </div>"; }
            else
            {
                $this->logger->addInfo("fakturacniSkupiny\Action: form_data from DB loaded");

                $form_data = $dotaz_upd->fetch_assoc();
                unset($form_data["id"]);
                unset($form_data["vlozil_kdo"]);
                
                // $output .= "<pre>DB: <br>";
                // $output .= var_export($form_data, true);
                // $output .= "</pre>";            
            }
        }
        else
        {
            // rezim pridani, ukladani, reloadu ??
            $form_data = $this->action_form->validate('nazev, fakturacni_text, '.
                                                'typ(gte[0]), typ_sluzby(gte[0]), '.
                                                 'sluzba_int(gte[0]), sluzba_int_id_tarifu(gte[0]), sluzba_iptv(gte[0]), sluzba_iptv_id_tarifu(gte[0]), sluzba_voip(gte[0]), sluzba_voip_id_tarifu(gte[0])');

            /// fix missing zero values in array (some bug in formr ??)
            $form_data = $this->fillEmptyVarsInArray($form_data, array('nazev', 'fakturacni_text'));

            // $output .= "<pre>Form: <br>";
            // $output .= var_export($form_data, true);
            // $output .= "</pre>";  
        }
               
        //kontrola vlozenych udaju ( kontrolujou se i vygenerovana data ... )
        if( (strlen($form_data['nazev']) > 0) )
        { $this->checkNazev($form_data['nazev']); }
                                               
        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if( ( ($form_data['nazev'] != "") and ($form_data['typ'] != "") and ( $form_data['typ_sluzby'] >= 0 ) ) ):
       
            // check duplicit v modu pridani ...
            if( ( $update_status!=1 ) )
            {
                $MSQ_NAZEV = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( nazev LIKE '" . $form_data['nazev'] . "' AND typ = '" . $form_data['typ'] . "' ) ");
                $MSQ_FT = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( fakturacni_text LIKE '" . $form_data['fakturacni_text'] . "' AND typ = '" . $form_data['typ'] . "' ) ");
                
                if( $MSQ_NAZEV->num_rows > 0 )
                { 
                    $error .= "<div style=\"color: #CC0066; \" ><h4>Název (".$form_data['nazev'].") již existuje!</h4></div>"; 
                    $fail = "true"; 
                }
                if( $MSQ_FT->num_rows > 0 )
                { 
                    $error .= "<div style=\"color: #CC0066; \" ><h4>Fakturační text (".$form_data['fakturacni_text'].") již existuje!</h4></div>"; 
                    $fail = "true"; 
                }
            }
        
            // check duplicit v modu uprava
            if( ( $update_status==1 and (isset($odeslano)) ) )
            {
                //zjisti jestli neni duplicitni dns, ip
                $MSQ_NAZEV = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( nazev LIKE '" . $form_data['nazev'] . "' AND typ = '" . $form_data['typ'] . "' AND id != '$this->form_update_id' ) ");
                $MSQ_FT = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE ( fakturacni_text LIKE '" . $form_data['fakturacni_text'] . "' AND typ = '" . $form_data['typ'] . "' AND id != '$this->form_update_id' ) ");
                
                if($MSQ_NAZEV->num_rows > 0)
                { $error .= "<div style=\"color: #CC0066;\" ><h4>Název (".$form_data['nazev'].") již existuje!!!</h4></div>"; $fail = "true"; }
                
                if($MSQ_FT->num_rows > 0)
                { $error .= "<div style=\"color: #CC0066;\" ><h4>Fakturační text (".$form_data['fakturacni_text'].") již existuje!!!</h4></div>"; $fail = "true"; }
            
            }
            
            //checkem jestli se macklo na tlacitko "OK" :)
            if( preg_match("/OK/",$odeslano) )
            { $output .= ""; }
            else
            { 
                $fail="true"; 
                $error .= "<div ><div class=\"alert alert-info\" role=\"alert\">Data neuloženy, nebylo použito tlačítko ";
                $error .= "\"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</div></div>";
            }
        
            //ulozeni
            if( !( isset($fail) ) ) 
            { 
                // priprava / konverze promennych pred ulozenim ...
                //if ( $dov_net == 2 ) { $dov_net_w ="a"; } else { $dov_net_w="n"; }
                
                if( $update_status =="1" )
                {
            
                    // rezim upravy
                    
                    //prvne stavajici data docasne ulozime                         
                    $vysl4 = $this->conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '". intval($this->form_update_id). "' ");
                
                    if( ( $vysl4->num_rows <> 1 ) )
                    { 
                        $output .= "<div style=\"color: red; padding-top: 5px; padding-bottom: 5px; \" >";
                        $output .= "Chyba! Nelze zjistit puvodni data pro ulozeni do archivu zmen</div>"; 
                    }
                    else  
                    { 
                        $pole_puvodni_data = $vysl4->fetch_assoc();
                        unset($pole_puvodni_data["id"]);
                        unset($pole_puvodni_data["vlozil_kdo"]);
                    } // konec else if radku <> 1
                    
                    //pridavani do pole pro porovnavani z archivu zmen...
                    $fs_upd = $form_data;                
            
                    $affected = DB::table($this->db_table_name)
                                ->where('id', $this->form_update_id)
                                ->update($form_data);
                
                    if($affected == 1){
                        $res = true;
                    }

                    if($res){ $output .= "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
                    else{ $output .= "<br><H3><div style=\"color: red; \" >Chyba! Data v databázi nelze změnit.</div></h3>\n"; }
            
                    $output .= "<div style=\"font-weight: bold; font-size: 18px; \">Změny je třeba dát vědět účetní!</div>";
                    
                    //ted vlozime do archivu zmen (inkrementarne)
                    $this->actionArchivZmenDiff($res, $pole_puvodni_data, $fs_upd);
                    // require("vlastnici2-fs-update-inc-archiv.php");
            
                    $updated="true";
            
                }
                else
                {
                    // rezim pridani
                    $form_data = array_merge($form_data, array("vlozil_kdo" => $this->loggedUserEmail));

                    $res = DB::table($this->db_table_name)->insert($form_data);
            
                    if( $res )
                    { $output .= "<br><H3><div style=\"color: green;\" >Fakturační skupina úspěšně přidána do databáze.</div></H3>\n"; } 
                    else
                    { $output .= "<br><H3><div style=\"color: red;\" >Chyba! Fakturační skupinu nelze přidat.</div></H3>\n"; }	
                
                    // pridame to do archivu zmen
                    $pole = "<b> akce: pridani fakt. skupiny; </b><br>";
                
                    foreach ($form_data as $c => $v) {
                        $pole .= "[$c]=> $v, ";
                    }
    
                    if($res === true){ $vysledek_write="1"; }
                
                    $add = $this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$this->loggedUserEmail','$vysledek_write')");
                    
                    $writed = "true"; 
                    
                    // konec else - rezim pridani
                }
        
            }
            else {} // konec else ( !(isset(fail) ), musi tu musi bejt, pac jinak nefunguje nadrazeny if-elseif
       
        elseif ( isset($send) ): 
            $error = "<div class=\"alert alert-warning\" role=\"alert\">Chybí povinné údaje !!! (aktuálně jsou povinné: Název, Typ, Typ služby) ".
                        "(debug: " . $form_data['nazev'] . ", " . $form_data['typ'] . "," . $form_data['typ_sluzby'] . ")</div>"; 
        endif;
       
        if($update_status==1)
        { $output .= '<h3 align="center" style="padding-top: 15px; " >Úprava fakturační skupiny</h3>'; } 
        else
        { $output .= '<h3 align="center" style="padding-top: 15px; " >Přidání fakturační skupiny</h3>'; }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if ( (isset($error)) or (!isset($send)) ): 
            $output .= $error;

            // $output .= $info;

            // vlozeni vlastniho formu
            $output .= $this->actionForm($form_data);

        elseif ( ( isset($writed) or isset($updated) ) ): 
       
            $output .= '<div style="">
                <a href="vlastnici2-fakt-skupiny.php" >Zpět na "Fakturační skupiny"</a>
            </div>
            
            <br>
            zadané údaje:<br><br>
            
            <b>Název skupiny</b>: ' . $form_data['nazev'] . "<br><br>";
    
            $output .= '<b>Typ</b>: ';
       
           if( $form_data['typ'] == 1 )
           { $output .= "DÚ - domácí uživatel"; }
           elseif( $form_data['typ'] == 2 )
           { $output .= "FÚ - firemní uživatel"; }
           else
           { $output .= "Typ nelze zjistit"; }
            $output .= '<b><br>';
            
            $output .= '<b>Typ služby</b>: ';
       
           if( $form_data['typ_sluzby'] == 0 )
           { $output .= "wifi"; }
           elseif( $form_data['typ_sluzby'] == 1 )
           { $output .= "optika"; }
           else
           { $output .= "nelze zjistit"; }
           
            $output .= '<br><br>';
       
            $output .= '<b>Služba "Internet"</b>: ';
       
           if( $form_data['sluzba_int'] == 0 )
           { $output .= "Ne"; }
           elseif( $form_data['sluzba_int'] == 1 )
           { $output .= "Ano"; }
           else
           { $output .= "Nelze zjistit"; }
           
           $output .= '<br>
                <b>Sluzba internet :: tarif ID</b>: ' . $form_data['sluzba_int_id_tarifu'] .
                '<br><br>';
       
            $output .= '<b>Služba "IPTV"</b>: ';
       
           if( $form_data['sluzba_iptv'] == 0 )
           { $output .= "Ne"; }
           elseif( $form_data['sluzba_iptv'] == 1 )
           { $output .= "Ano"; }
           else
           { $output .= "Nelze zjistit"; }
       
           $output .= '<br>';
            
           $output .= '<b>Sluzba iptv :: tarif</b>: ' .  $form_data['sluzba_iptv_id_tarifu'] .
            '<br><br>';
            
            $output .= '<b>Služba "VoIP"</b>: ';
     
           if( $form_data['sluzba_voip'] == 0 )
           { $output .= "Ne"; }
           elseif( $form_data['sluzba_voip'] == 1 )
           { $output .= "Ano"; }
           else
           { $output .= "Nelze zjistit"; }
     
        endif; 
        $output .= "<br>";

        return $output;
    }

    function actionForm($data)
    {
        $output = "";

        $output .= '<form name="form1" method="post" >
        <input type="hidden" name="send" value="true" >';
        $output .= $this->csrf_html[0];

        $output .= '<input type="hidden" name="update_id" value="' . $this->form_update_id . '" >
        
        <table border="0" width="" cellspacing="5" >
           <tr>
                <td colspan="" >&nbsp;</td>
            </tr>
        
                <tr>
                 <td  width="50px" >Název skupiny: </td>
                 <td><input type="text" name="nazev" size="30" ' . "value=\"" . $data['nazev'] . "\"></td>" .
                
             '<td width="50px" >&nbsp;</td>
             
             <td width="200px" rowspan="5" valign="top" >
             <div style="padding-bottom: 10px; " >Fakturační text:</div>
             
             <textarea name="fakturacni_text" cols="35" rows="5" >' . $data['fakturacni_text'] . '</textarea>
             
             </td>
            </tr>
        
                <tr><td colspan="2" ><br></td></tr>
        
                <tr>
                 <td  width="250px" >Typ: </td>
                  <td>
                    <select name="typ" size="1" >
                        <option value="1" '; if($data['typ'] == 1 or intval($data['typ']) < 1 ){ $output .= " selected "; } $output .= ' >DÚ - domácí uživatel</option>
                        <option value="2" '; if($data['typ'] == 2){ $output .= " selected "; } $output .= ' >FÚ - firemní uživatel</option>
                    </select>
                 </td>
                </tr>
        
                <tr><td colspan="2" ><br></td></tr>
        
                <tr>
                 <td  width="250px" >Typ služby:</td>
                  <td>
                    <select name="typ_sluzby" size="1" >
                        <option value="0" '; if(intval($data['typ_sluzby']) == 0){ $output .= " selected "; } $output .= ' >wifi</option>
                        <option value="1" '; if($data['typ_sluzby'] == 1){ $output .= " selected "; } $output .= ' >optika</option>
                    </select>
                 </td>
                </tr>
        
                <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba internet */
        $output .= '<td>
                <span style="" ><b>Služba "Internet":</b></span>
              </td>
              <td>    
                <select name="sluzba_int" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( $data['sluzba_int'] == 0 or $data['sluzba_int'] == "" ){ $output .= " selected "; } 
            $output .= ' >Ne</option>
                <option value="1" ';
                    if( $data['sluzba_int'] == 1){ $output .= " selected "; }
            $output .= ' >Ano</option>
                </select>
              </td>
            </tr>
            
            <tr>
              <td>
                <span style="" >Služba Internet :: Vyberte tarif:</span>
              </td>
              <td>';
        
               if( $data['sluzba_int'] != 1)
               {
                 $output .= "<span style=\"color: gray; \" >Není dostupné</span>";
                 $output .= "<input type=\"hidden\" name=\"sluzba_int_id_tarifu\" value=\"0\" >";
               }
               else
               {
                  //vypis tarifu
                  $output .= "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
                         
                  $output .= "<option value=\"0\" ";
                    if(intval($data['sluzba_int_id_tarifu']) == 0 ){ $output .= " selected "; }
                  $output .= " style=\"color: gray; \">Nevybráno</option>";
						   
                  $dotaz_tarify_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
                                                
                  while( $data_tarify = $dotaz_tarify_id_tarifu->fetch_array() )
                  {
                      $output .= "<option value=\"".$data_tarify["id_tarifu"]."\" ";
                      if( $data['sluzba_int_id_tarifu'] == $data_tarify["id_tarifu"] ){ $output .= " selected "; }
                      $output .= " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";
                  }
                    $output .= "</select>";
                }// konec else if sluzba_int != 1
               
        $output .= '
              </td>
            </tr>
        
            <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba iptv */
        $output .= '<td>
                <span style="" ><b>Služba "IPTV" (televize):</b></span>
              </td>
              <td>    
                <select name="sluzba_iptv" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( intval($data['sluzba_iptv']) == 0 ){ $output .= " selected "; }
        $output .=	' >Ne</option>
                <option value="1" ';
                    if( $data['sluzba_iptv'] == 1){ $output .= " selected "; }
                $output .= ' >Ano</option>
                </select>
              </td>
            </tr>
            
            <tr>
              <td>
                <span style="" >Služba IPTV :: Vyberte tarif:</span>
              </td>
              <td>';
        
               if( $data['sluzba_iptv'] != 1)
               {
                 $output .= "<span style=\"color: gray; \" >Není dostupné</span>";
                 $output .= "<input type=\"hidden\" name=\"sluzba_iptv_id_tarifu\" value=\"0\" >";
               }
               else
               {
                  //vypis tarifu
                  $output .= "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
                              
                  $output .= "<option value=\"0\" ";
                       if(intval($data['sluzba_iptv_id_tarifu']) == 0){ $output .= " selected "; }
                  $output .= " style=\"color: gray; \">Nevybráno</option>";
                  
                  $dotaz_iptv_id_tarifu = $this->conn_mysql->query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
            
                  while( $data_iptv = $dotaz_iptv_id_tarifu->fetch_array() )
                  {
                     $output .= "<option value=\"".$data_iptv["id_tarifu"]."\" ";
                       if( $data['sluzba_iptv_id_tarifu'] == $data_iptv["id_tarifu"] ){ $output .= " selected "; }
                     $output .= " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
                  }
             
                  $output .= "</select>";
                  
                }// konec else if sluzba_iptv != 1
               
        $output .= '
              </td>
            </tr>
            
            <tr><td colspan="2" ><br></td></tr>
            
            <tr>';
              /* sluzba voip */ 
        $output .= '
              <td>
                <span style="" ><b>Služba "VoIP":</b></span>
              </td>
              <td>    
                <select name="sluzba_voip" size="1" onChange="self.document.forms.form1.submit()" >
                <option value="0" ';
                    if( $data['sluzba_voip'] == 0 or !isset($data['sluzba_voip']) ){ $output .= " selected "; } 
            $output .= ' >Ne</option>
                <option value="1" ';
                    if( $data['sluzba_voip'] == 1 ){ $output .= " selected "; }
            $output .= ' >Ano</option>
                </select>
              </td>
            </tr>';

         $output .= '<tr>
            <td>
              <span style="" >Služba VoIP :: Vyberte tarif:</span>
            </td>
            <td>';
      
        $output .= "<span style=\"color: gray; \" >Není dostupné</span>";
        $output .= "<input type=\"hidden\" name=\"sluzba_voip_id_tarifu\" value=\"0\" >";
    
        $output .= '</td>
        </tr>';

        $output .= '<tr><td colspan="2" ><br></td></tr>
            
           <tr><td colspan="4" ><br></td></tr>
                     
         <tr><td colspan="4" align="center" >
          <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
         </td></tr>
            
        </table>
        </form>';

        return $output;
    }

    function actionArchivZmenDiff($res, $pole_puvodni_data, $fs_upd)
    {
        $pole3 .= "<b>akce: uprava fakturacni skupiny; </b><br>";
        $pole3 .= "[id_fs] => ".$this->form_update_id;
        $pole3 .= " diferencialni data: ";
        
        //novy zpusob archivovani dat
        foreach($pole_puvodni_data as $key => $val)
        {
            if( !($fs_upd[$key] == $val) )
            {
                if( $key == "pozn" )
                {
                $pole3 .= "změna <b>Poznámky</b> z: ";
                $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$fs_upd[$key]."</span>";
                $pole3 .= ", ";
                } //konec key == pozn
            else
                { // ostatni mody, nerozpoznane
                $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                $pole3 .= "na: <span class=\"az-s2\">".$fs_upd[$key]."</span>, ";
                } //konec else
            } // konec if key == val
        } // konec foreach

        $pole .= "".$pole3;
        
        if ($res === true){ $vysledek_write="1"; }
        else{ $vysledek_write="0"; }
        
        $add = $this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . $this->loggedUserEmail ."','$vysledek_write') ");

        return $add;
    }
}
