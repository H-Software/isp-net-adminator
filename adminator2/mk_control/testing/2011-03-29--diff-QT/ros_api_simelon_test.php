<?php

// ! trida pro synchronizaci RouterOS zarízení, co budou delat QoS/marking  trafficu
// ! krz MK API
// !
// ! 2011/2/15
// ! 
// ! created by Patrik "hujer" Majer
// !
// ! 2011/03/01 - v1.0a - alpha version - deployment operation
// !
// ! 2011/03/10 - add handling for guarantee lines & routers (from tolopogy)
// !
// !

class mk_synchro_qos
{
 var $conn;			//objekt pripojeni k API na MK
 
 var $debug; 			//uroven nebo on/off stav debug výpisů
 
 var $objects; 			//pole s objekty, ktere jedou krz tento router
 
 var $getall_mangle; 		//pole pro export dat z /ip/firewall/mangle
 
 var $element_name_dwn; 	//nazev prvku kterej urcuje dst ip adrress, pouziti u parsingu dat
 var $element_name_upl; 	// dtto pro src ip adrress, pouziti u parsingu dat
 
 var $item_ip_dwn;  		//nazev polozky, ktera ma specifikovat dst ip adress, pouzito pri vkladani zaznamu
 var $item_ip_upl;  	     	//nazev polozky, ktera ma specifikovat src ip adress, pouzito pri vkladani zaznamu
 
 var $wrong_firewall_items;  //polozky ve /ip/firewall/mangle, ktere jsou na smazani
 
 var $force_mangle_rewrite;  //stav (0/1) jestli se ma navrdo premazat firewall 
 
 var $arr_global_diff_exc;   //pole s prebyvajicimi prvky v mangle
 var $arr_global_diff_miss;  //pole s chybejicimi prvky v mangle
 
 var $arr_objects_dev_dwn;
 var $arr_objects_dev_upl;
 
 var $agregace_sc; 	     //agragace SmallCity
 
 var $speed_sc_dwn; 	     //rychlosti smallcity linek :)
 var $speed_sc_upl;
 
 //pole pro trideni dle typu linky, pro potreby QT
 var $objects_sc;
 var $objects_mp;
 
 var $objects_garants;	     //pole se seznamem garantovejch trid
 var $objects_garants_used;  //dtto, akorat pouze pouzivane krz definovaný router
   
 var $qt_ar_exc;	     //pri diff. porovnani QT zaznamu
 var $qt_ar_mis;    	     //dtto
 
 var $sc_speed_koef; 	     //koeficient pro nasobeni parent tridy u SC linek

 var $id_tarifu_routers=2;   //id tarifu pro routery (idealne garant)

 
 function __construct()
 {
    //vytvorit pole pro garanty
    $q = $conn_mysql->query("SELECT id_tarifu, zkratka_tarifu, speed_dwn, speed_upl
			FROM tarify_int 
			WHERE (typ_tarifu = '0' AND garant = '1') 
			ORDER BY id_tarifu");
    
    while($data = mysql_fetch_array($q))
    { 
	$id = "objects_g_".$data["id_tarifu"];
	$this->objects_garants[$id] = $data["speed_dwn"].":".$data["speed_upl"]; 
    }
  
 } //end of function contsruct

 function array_serialize($array) {
 
    $aReturn = array();
    
    foreach ($array as $key => $value) 
    { 
	if( isset($value["limit-at"]))
	{ $value["limit-at"] = floatval($value["limit-at"]); }
	
	if(isset($value["max-limit"]))
	{ $value["max-limit"] = floatval($value["max-limit"]); }
	
	$serValue = serialize($value);	
	
	$aReturn[$key] = $serValue;
    }
    
    return $aReturn;
    
 } //end of function array_serialize 
 
 
 function arrayDiff($aArray1, $aArray2) { 
 
    //priprava promenych
    $aReturn = array(); 
        
    $serArray1 = $this->array_serialize($aArray1);    
    $serArray2 = $this->array_serialize($aArray2);    

    $diffArray = array_diff($serArray1,$serArray2);

// echo "--- serArray1 --- \n".print_r($serArray1);
// echo "--- diffArray --- \n".print_r($diffArray)." \n";

    foreach($diffArray as $key => $value) 
    { $aReturn[$key] = ""; }
    
    return $aReturn; 
    
 } //end of function arrayRecursiveDiff

 function find_root_router($id_routeru, $ip_adresa_routeru)
 {
    $rs = $conn_mysql->query("SELECT parent_router, ip_adresa FROM router_list WHERE id = '$id_routeru'");

    while( $d = mysql_fetch_array($rs) )
    { $parent_router = $d["parent_router"]; }

    $rs2 = $conn_mysql->query("SELECT parent_router, ip_adresa FROM router_list WHERE id = '$parent_router'");

    while( $d2 = mysql_fetch_array($rs2) )
    { $ip_adresa_2 = $d2["ip_adresa"]; }

    if($ip_adresa_2 == $ip_adresa_routeru)
    { //dosahlo se reinhard-fiber, tj. zaznam CHCEME
        return true;
    }
    elseif($parent_router == "0")
    { //dosahlo se reinhard-wifi, takze zaznam nechceme
    }
    else
    { //ani jedno predchozi, rekurze .. :)
        if( $this->find_root_router($parent_router, $ip_adresa_routeru) == true)
        { return true; }
    }
    
 } //end of function find_root_router

 function find_obj($ip)
 {

  //1. zjistit routery co jedou pres reinhard-fiber
  $rs_routers = $conn_mysql->query("SELECT id, parent_router, nazev, ip_adresa FROM router_list ORDER BY id");
  $num_rs_routers = mysql_num_rows($rs_routers);

  while($data_routers = mysql_fetch_array($rs_routers))
  {
   $id_routeru = $data_routers["id"];
   $ip_adresa = $data_routers["ip_adresa"];
   
   if( $this->find_root_router($id_routeru,$ip) === true)
   { 
    $routers[] = $id_routeru; 
    $routers_ip[] = $ip_adresa;
   }
  }

  //2. zjistit nody
  $i=0;
  foreach ($routers as $key => $id_routeru) {

    //print "router: ".$id_routeru.", \t\t  selected \n";
    if($i == 0)
    { $sql_where .= "'$id_routeru'"; }
    else
    { $sql_where .= ",'$id_routeru'"; }

    $i++;
  }

  $sql = "SELECT id, jmeno FROM nod_list WHERE router_id IN (".$sql_where.") ORDER BY id";
  //print $sql."\n";

  $rs_nods = $conn_mysql->query($sql);
  $num_rs_nods = mysql_num_rows($rs_nods);

  while($data_nods = mysql_fetch_array($rs_nods))
  { $nods[] = $data_nods["id"]; }

  //3. zjistit lidi
  $i=0;

  foreach($nods as $key => $id_nodu ){
    //print "nods: ".$id_nodu." \n";

    if($i == 0)
    { $sql_obj_where .= "'$id_nodu'"; }
    else
    { $sql_obj_where .= ",'$id_nodu'"; }

    $i++;
  }

  $sql_obj = "SELECT ip, id_tarifu, client_ap_ip FROM objekty WHERE id_nodu IN (".$sql_obj_where.") ORDER BY id_komplu";
  //print $sql_obj."\n";

  $rs_objects = pg_query($sql_obj);
  $num_rs_objects = pg_num_rows($rs_objects);

  while( $data = pg_fetch_array($rs_objects))
  {
    $ip = $data["ip"];
    $client_ap_ip = $data["client_ap_ip"];
    
    $this->objects[$ip] = $data["id_tarifu"];
  
    if( (strlen($client_ap_ip) > 4) )
    { //vyplnena ip adresa apcka
    
      //zjistit zda-li uz neni 
      if( !(array_key_exists($client_ap_ip, $this->objects)) )
      { $this->objects[$client_ap_ip] = $this->id_tarifu_routers; }    
    }
  }

  //k seznamu ip adres pridame routery, taky chtej inet :)
  foreach($routers_ip as $key => $ip) {

    //zjistit zda uz IP adresa neni v objektu
    if( (array_key_exists($ip, $this->objects)) )
    { /* echo "  object ".$ip." exists \n"; */  }
    else
    { $this->objects[$ip] = $this->id_tarifu_routers; }    

  }
  
  print " number of IP addresses via this router: ".count($this->objects);
	
  if($this->debug == 1){ echo ", count of array objects: ".count($this->objects)." "; }
  echo "\n";

 } //end of function

 function remove_wrong_items($wrong_items)
 {
   $item_del_ok=0;
   $item_del_err=0;

   //print_r($wrong_items);

  $del = $this->conn->remove("/ip/firewall/mangle", $wrong_items);

  if( $del == "1" )
  {
    if($this->debug > 0){ echo "    Wrong Item(s) successfully deleted (".count($wrong_items).")\n"; }
    $item_del_ok = count($wrong_items);
  }
  else
  {
    if($this->debug > 0){ echo "    ERROR: ".print_r($del)."\n"; }
    $item_del_err++;
  }

  print "  Deleted wrong items: ".$item_del_ok.", error(s): ".$item_del_err."\n";

 } //end of function remove_wrong_items

 function detect_diff_in_mangle()
 {

   $this->getall_mangle = $this->conn->getall(array("ip", "firewall", "mangle") );

/* 
   if( count($this->getall_mangle) > 0 ) { 
      print_r($this->getall_mangle); }
   else { 
      echo " Array \"getall_mangle\" is empty \n"; }
*/

  //priprava zaznamu v routeru
  foreach ($this->getall_mangle as $key => $value) {

    $ip_dwn = $this->getall_mangle["$key"]["$this->element_name_dwn"];
    $ip_upl = $this->getall_mangle["$key"]["$this->element_name_upl"];

    if( isset($this->getall_mangle[$key]["$this->element_name_dwn"]) )
    {
        //definice pole, jak ma zaznam vypadat :)
        $mangle_muster = array();
        $mangle_muster[".id"] = $this->getall_mangle["$key"][".id"];
        $mangle_muster["chain"] = $this->chain; //[chain] => prerouting
        $mangle_muster["action"] = "mark-packet"; //[action] => mark-packet
        $mangle_muster["new-packet-mark"] = $this->getall_mangle[$key]["$this->element_name_dwn"]."_dwn";
        $mangle_muster["passthrough"] = "false";
        $mangle_muster["$this->element_name_dwn"] = $this->getall_mangle[$key]["$this->element_name_dwn"];
        $mangle_muster["invalid"] = "false";
        $mangle_muster["dynamic"] = "false";
        $mangle_muster["disabled"] = "false";
        $mangle_muster["comment"] = "";

        //print_r($mangle_muster);
        //print_r($value);

        $diff1 = array_diff($mangle_muster, $value);
        $diff2 = array_diff($value, $mangle_muster);

        if( (empty($diff1) and empty($diff2)) )
        { $this->arr_objects_dev_dwn[$ip_dwn] = $this->getall_mangle["$key"][".id"]; }
        else
        {
            echo " ERROR: Item id: ".$this->getall_mangle["$key"][".id"]." does not match the muster item. \n";
            //print_r($diff1); print_r($diff2);
            $this->wrong_firewall_items[] = $this->getall_mangle["$key"][".id"];
        }

      //echo "adding: $ip -- $key\n";
    }
    elseif( isset($this->getall_mangle[$key]["$this->element_name_upl"]) )
    {

        //definice pole, jak ma zaznam vypadat :)
        $mangle_muster = array();
        $mangle_muster[".id"] = $this->getall_mangle["$key"][".id"];
        $mangle_muster["chain"] = $this->chain; //[chain] => prerouting
        $mangle_muster["action"] = "mark-packet"; //[action] => mark-packet
        $mangle_muster["new-packet-mark"] = $this->getall_mangle[$key]["$this->element_name_upl"]."_upl";
        $mangle_muster["passthrough"] = "false";
        $mangle_muster["$this->element_name_upl"] = $this->getall_mangle[$key]["$this->element_name_upl"];
        $mangle_muster["invalid"] = "false";
        $mangle_muster["dynamic"] = "false";
        $mangle_muster["disabled"] = "false";
        $mangle_muster["comment"] = "";

        //print_r($mangle_muster);
        //print_r($value);

        $diff1 = array_diff($mangle_muster, $value);
        $diff2 = array_diff($value, $mangle_muster);

        if( (empty($diff1) and empty($diff2)) )
        { $this->arr_objects_dev_upl[$ip_upl] = $this->getall_mangle["$key"][".id"]; }
        else
        {
            echo " ERROR: Item id: ".$this->getall_mangle["$key"][".id"]." does not match the muster item. \n";
            //print_r($diff1); print_r($diff2);
            $this->wrong_firewall_items[] = $this->getall_mangle["$key"][".id"];
        }
    }
    else
    {
        echo " WARNING: Nalezeno jiné pravidlo/nelze parsovat. (id: ".$this->getall_mangle["$key"][".id"].") \n";

        //zde udelat seznam pravidel pro smazani :)
        $this->wrong_firewall_items[] = $this->getall_mangle["$key"][".id"];

    } //end of else if
 }

 if( (count($this->wrong_firewall_items) > 0 ) && ($this->force_mangle_rewrite != 1))
 { $this->remove_wrong_items($this->wrong_firewall_items); }

 //print_r($arr_objects_dev_dwn);
 //print_r($arr_objects_dev_upl);
 if( !(is_array($this->arr_objects_dev_upl)) )
 { $this->arr_objects_dev_upl = array(); }

 if( !(is_array($this->arr_objects_dev_dwn)) )
 { $this->arr_objects_dev_dwn = array(); }

 $arr_obj_dev_diff = array_diff_key($this->arr_objects_dev_dwn, $this->arr_objects_dev_upl);
 //print_r($arr_obj_dev_diff);

 $arr_obj_dev_diff2 = array_diff_key($this->arr_objects_dev_upl, $this->arr_objects_dev_dwn);
 //print_r($arr_obj_dev_diff2);

 if( (count($arr_obj_dev_diff) > 0) or ( count($arr_obj_dev_diff2) > 0) )
 {
    echo " ERROR: Rozdilny pocet zaznamu pro DWN a UPL. Forcing a full sync... \n";
    $this->force_mangle_rewrite = 1;
 }
 else
 {
    echo " number of records : device: ".count($this->arr_objects_dev_dwn).", system: ".count($this->objects)."\n";

    $this->arr_global_diff_exc = array_diff_key($this->arr_objects_dev_dwn, $this->objects);
    $this->arr_global_diff_mis = array_diff_key($this->objects, $this->arr_objects_dev_dwn);
 }

} //end of function detect_diff_in_mangle

function erase_mangle(){

  $items_suc_del = 0;
  $items_err_del = 0;

  foreach ($this->getall_mangle as $key => $value) {

    /*
    if(asi nejake vyjimky)
    {}
    else
    */
    {
        $erase[] = $this->getall_mangle[$key][".id"];
        //print "erasing id: ".$key."\n";
    }

  } //end of forearch

  $del = $this->conn->remove("/ip/firewall/mangle", $erase);

  if( $del == "1" )
  {
    if($this->debug > 0){ echo "    Item(s) successfully deleted (".count($erase).")\n"; }
    $items_suc_del++;
  }
  else
  {
    if($this->debug > 0){ echo "    ERROR: ".print_r($del)."\n"; }
    $items_err_del++;
  }

  print "  count of force deleted items: ok: ".$items_suc_del.", error: ".$items_err_del."\n";

  //print_r($erase)."\n";

} //end of function erase_mangle

function synchro_mangle_force()
{
 //reseni asi smazat vse a pak pustit synchro_mangle
 $this->erase_mangle();

 $this->detect_diff_in_mangle();

 $this->synchro_mangle();

} //end of function synchro_mangle_force

function synchro_mangle()
{

 $items_suc_added = 0;
 $items_err_added = 0;

 foreach ($this->arr_global_diff_mis as $ip => $value) {

   $add_par_r = array ("chain" => $this->chain, "action" => "mark-packet", "disabled" => "no", "new-packet-mark" => $ip."_dwn",
                     "$this->item_ip_dwn" => "$ip", "passthrough" => "no");
   $add = $this->conn->add("/ip/firewall/mangle", $add_par_r);

   if( ereg('^\*([[:xdigit:]])*$',$add) )
   {
      if($debug > 0){ echo "    Item ".$add." successfully added \n"; }
      $items_suc_added++;
   }
   else
   {
      if($debug > 0){ echo "    ERROR: ".print_r($add)."\n"; }
      $items_err_added++;
   }

   $add_par_r2 = array ("chain" => $this->chain, "action" => "mark-packet", "disabled" => "no", "new-packet-mark" => $ip."_upl",
                     "$this->item_ip_upl" => "$ip", "passthrough" => "no");
   $add2 = $this->conn->add("/ip/firewall/mangle", $add_par_r2);

   if( ereg('^\*([[:xdigit:]])*$',$add2) )
   {
      if($debug > 0){ echo "    Item ".$add." successfully added \n"; }
      $items_suc_added++;
   }
   else
   {
      if($debug > 0){ echo "    ERROR: ".print_r($add2)."\n"; }
      $items_err_added++;
      print_r($add_par_r2);
   }

 } //end of foreach $arr_global_diff_mis

 print "  count of added items: ok: ".$items_suc_added.", error: ".$items_err_added."\n";

 $items_suc_del = 0;
 $items_err_del = 0;

 foreach ($this->arr_global_diff_exc as $ip => $value) {

   $index = $this->arr_objects_dev_dwn["$ip"];
   $index2 = $this->arr_objects_dev_upl["$ip"];

   //echo " deleted: ".$ip.", v1: $index, v2: $index2 \n";

   $del = $this->conn->remove("/ip/firewall/mangle", array("$index","$index2") );

   if( $del == "1" )
   {
      if($debug > 0){ echo "    Item(s) successfully deleted (".$index.",".$index2.")\n"; }
      $items_suc_del++;
   }
   else
   {
      if($debug > 0){ echo "    ERROR: ".print_r($del)."\n"; }
      $items_err_del++;
   }

 } //end of foreach $arr_global_diff_mis

 print "  count of deleted items: ok: ".$items_suc_del.", error: ".$items_err_del."\n";


} //end of function synchro_mangle

function qt_global() {

  //zjisteni agregace SC
  $rs_agreg = $conn_mysql->query("SELECT agregace, speed_dwn, speed_upl FROM tarify_int WHERE id_tarifu = '1'");

    while( $d_agreg = mysql_fetch_array($rs_agreg) )
    {
        $this->agregace_sc = $d_agreg["agregace"];
        $this->speed_sc_dwn = $d_agreg["speed_dwn"];
        $this->speed_sc_upl = $d_agreg["speed_upl"];
    }

   foreach ($this->objects as $ip => $linka) {
    
    if($linka == 1) {
        $this->objects_sc[] = $ip;
    } 
    elseif($linka == 0) {
        $this->objects_mp[] = $ip;
    }
    else
    {
    	if(array_key_exists("objects_g_".$linka, $this->objects_garants)) {
	    //echo "   WARNING: garant: "."objects_g_".$linka.", ip> $ip \n";
	    $this->{"objects_g_".$linka}[] = $ip;
	}
	else { 
	 //$this->objects_garants[]
    	    echo "   WARNING: Neznámá linka (".$linka.") u objektu: ".$ip."\n";
        }
    }
  }

  //zredukovat pole objects_garants, dle vyuziti
  foreach ($this->objects_garants as $key => $value) {
	
    if( (count($this->{$key}) > 0 )) {
	$this->objects_garants_used[$key] = $value;
    }
    
  }
  
  //print_r($this->objects_g_2);
  //print_r($this->objects_garants_used);

  echo "  qt(global) number of records tariff: sc: ".count($this->objects_sc).", mp: ".count($this->objects_mp)."\n";

}

function qt_delete_all() {

  $qt_suc_del=0;
  $qt_err_del=0;

  $qt_del_all = $this->conn->getall(array("queue","tree"));

  foreach ($qt_del_all as $key => $value) {

    $qt_del_all_id[] = $qt_del_all[$key][".id"];

  }

  $qt_del = $this->conn->remove("/queue/tree", $qt_del_all_id );

   if( $qt_del == "1" ) {
      if($this->debug > 0){ echo "    QT Item(s) successfully deleted (".count($qt_del_all_id).")\n"; }
      $qt_suc_del++;
   }
   else {
      if($this->debug > 0){ echo "    QT ERROR: ".print_r($qt_del_all_id)."\n"; }
      $qt_err_del++;
   }

 print "  qt: deleted items ".count($qt_del_all_id).", ok: ".$qt_suc_del.", error: ".$qt_err_del."\n";

 // print_r($qt_del_all_id);

}

function erase_qt($array){

  $items_suc_del = 0;
  $items_err_del = 0;

  $del = $this->conn->remove("/queue/tree", $array);

  if( $del == "1" )
  {
    if($this->debug > 0){ echo "    Item(s) successfully deleted (".count($erase).")\n"; }
    $items_suc_del++;
  }
  else
  {
    if($this->debug > 0){ echo "    ERROR: ".print_r($del)."\n"; }
    $items_err_del++;
  }

  echo "  qt: count of deleted items: ".count($array).", status: ";

  if( $items_suc_del == 1){ echo " OK "; }
  elseif( $items_err_del == 1){ echo " error "; }
  else
  { echo "unknown (ok: ".$items_suc_del.", error: ".$items_err_del.") "; }
  
  echo "\n";

  //print_r($array)."\n";

} //end of function erase_qt

function detect_diff_queues() {
 
 //
 //1. zjistime co je v zarizeni
 //
 
 //$qt_dump = $this->conn->getall( array("queue","tree"), "", "", ".id" );
 $qt_dump = $this->conn->getall( array("queue","tree"));

 $qt_dump_trim = $qt_dump;

 //vymazeme .id, jinak nelze pole porovnat
 foreach ($qt_dump_trim as $key => $value) {

     unset($qt_dump_trim["$key"][".id"]);

 } //end of foreach qt_dump_trim

 //2. zjistime, co je v adminatoru
  
  //
  // 2.1 SmallCity Linky
  //
  
  $sc_group=1;
  $sc_count=0;

  $limit_at_sc_dwn = ($this->speed_sc_dwn / $this->agregace_sc)*1000;
  $limit_at_sc_upl = ($this->speed_sc_upl / $this->agregace_sc)*1000;

  foreach ($this->objects_sc as $key => $ip) {

    if($sc_count == 0)
    { //zresetovan citac sc, tj. vytvorime globalni skupinu
    
      //2.1.1 - agregacni SC tridy
      
      $limit = ($this->speed_sc_dwn * $this->sc_speed_koef)*1000;
					      
      $qt_system[] = array ("name" => "q-dwn-sc-".$sc_group, "parent" => "global-out", "limit-at" => $limit,
    			      "priority" => "1", "max-limit" => $limit, "burst-limit" => "0",
			      "burst-threshold" => "0", "burst-time" => "00:00:00", "invalid" => "false",
			      "disabled" => "false" );

      $limit = ($this->speed_sc_upl * $this->sc_speed_koef)*1000;

      $qt_system[] = array ("name" => "q-upl-sc-".$sc_group, "parent" => "global-out", "limit-at" => $limit,
    			      "priority" => "1", "max-limit" => $limit, "burst-limit" => "0",
			      "burst-threshold" => "0", "burst-time" => "00:00:00", "invalid" => "false",
			      "disabled" => "false");
    }
    
    //2.1.2 - jednotlive IP adresy
    
    $qt_system[] = array ("name" => "q-dwn-sc-".$ip, "parent" => "q-dwn-sc-".$sc_group, "packet-mark" => $ip."_dwn",
			  "limit-at" => $limit_at_sc_dwn, "queue" => "wireless-default", "priority" => "1", 
			  "max-limit" => ($this->speed_sc_dwn)*1000, "burst-limit" => "0", "burst-threshold" => "0", 
			  "burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");

    $qt_system[] = array ("name" => "q-upl-sc-".$ip, "parent" => "q-upl-sc-".$sc_group, "packet-mark" => $ip."_upl", 
			  "limit-at" => $limit_at_sc_upl, "queue" => "wireless-default", "priority" => "1", 
			  "max-limit" => ($this->speed_sc_upl)*1000, "burst-limit" => "0", "burst-threshold" => "0", 
			  "burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");

    //konec cyklu
    $sc_count++;

    if( $sc_count == $this->agregace_sc)
    {
      $sc_count=0;
      $sc_group++;
    }
  } //end of foreach array objects_sc

  //
  // 2.2 - MP linky
  //
  
  //2.2.1 - globalni MP skupiny
  $qt_system[] = array ("name" => "q-dwn-mp-global", "parent" => "global-out", "limit-at" => "0", "priority" => "1",
			"max-limit" => "100000000", "burst-limit" => "0", "burst-threshold" => "0", 
			"burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");

  $qt_system[] = array ("name" => "q-upl-mp-global", "parent" => "global-out", "limit-at" => "0", "priority" => "1",
			"max-limit" => "100000000", "burst-limit" => "0", "burst-threshold" => "0", 
			"burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");
			
  
  //2.2.2 - klientske linky/skupiny

  foreach ($this->objects_mp as $key => $ip) {

      $qt_system[] = array ("name" => "q-dwn-mp-".$ip, "parent" => "q-dwn-mp-global", "packet-mark" => $ip."_dwn",
    			     "limit-at" => "100000", "queue" => "wireless-default", "priority" => "1", 
			     "max-limit" => "10000000", "burst-limit" => "0", "burst-threshold" => "0", 
			     "burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");
			      
      $qt_system[] = array ("name" => "q-upl-mp-".$ip, "parent" => "q-upl-mp-global", "packet-mark" => $ip."_upl",
    			    "limit-at" => "100000", "queue" => "wireless-default", "priority" => "1", 
			    "max-limit" => "10000000", "burst-limit" => "0", "burst-threshold" => "0", 
			    "burst-time" => "00:00:00", "invalid" => "false", "disabled" => "false");
  }
   
  //
  // 2.3 - Garanti, routery atd
  //
  foreach( $this->objects_garants_used as $garant_id => $speeds)
  {
    list($speed_dwn, $speed_upl) = explode(":",$speeds);

    //parent tridy pro garanty        
    $qt_system[] = array ("name" => "q-dwn-".$garant_id, "parent" => "global-out", "limit-at" => $speed_dwn."000",
			  "priority" => "1", "max-limit" => $speed_dwn."000", "burst-limit" => "0", 
			  "burst-threshold" => "0", "burst-time" => "00:00:00", "invalid" => "false",
			  "disabled" => "false" );

    $qt_system[] = array ("name" => "q-upl-".$garant_id, "parent" => "global-out", "limit-at" => $speed_upl."000",
			  "priority" => "1", "max-limit" => $speed_upl."000", "burst-limit" => "0", 
			  "burst-threshold" => "0", "burst-time" => "00:00:00", "invalid" => "false",
			  "disabled" => "false" );

    foreach( $this->{$garant_id} as $id => $ip)
    {

	$qt_system[] = array ("name" => "q-dwn-q-".$ip, "parent" => "q-dwn-".$garant_id, "packet-mark" => $ip."_dwn",
			      "limit-at" => "0", "queue" => "wireless-default", "priority" => "1", "max-limit" => "0",
			      "burst-limit" => "0", "burst-threshold" => "0", "burst-time" => "00:00:00", 
			      "invalid" => "false", "disabled" => "false" );
			    
	$qt_system[] = array ("name" => "q-upl-q-".$ip, "parent" => "q-upl-".$garant_id, "packet-mark" => $ip."_upl",
			      "limit-at" => "0", "queue" => "wireless-default", "priority" => "1", "max-limit" => "0",
			      "burst-limit" => "0",  "burst-threshold" => "0", "burst-time" => "00:00:00", 
			      "invalid" => "false", "disabled" => "false");
    
    }
      
  }
    
  //
  // 3. porovname pole 
  //

  /* pole se zaznamama */
  //echo "--- printing qt_dump_trim --- \n"; print_r($qt_dump_trim);
  //echo "--- printing qt_system --- \n"; print_r($qt_system);

  $this->qt_ar_exc = $this->arrayDiff($qt_dump_trim, $qt_system);
  $this->qt_ar_mis = $this->arrayDiff($qt_system, $qt_dump_trim);

  echo "  qt: count of classess: excess: ".count($this->qt_ar_exc).", missing: ".count($this->qt_ar_mis)." \n";
  
  if( (count($this->qt_ar_exc) > 0 ) or (count($this->qt_ar_mis) > 0 ) )
  { 
    //neco nesedi .. 
    //echo "--- printing qt_ar_exc --- \n"; print_r($this->qt_ar_exc); 
    //echo "--- printing qt_ar_mis --- \n"; print_r($this->qt_ar_mis); 
  
    //prebejvajici zaznamy smazat
    
    foreach($this->qt_ar_exc as $key => $val) 
    {
        $item_id = $qt_dump[$key][".id"];
	//print "  add erase number: ".$key.", item_id: ".$item_id." \n";
	
	if( (strlen($item_id) > 0 ) )
	{ $qt_delete_items[] = $item_id; }
    } //end of foreach this->qt_ar_exc
    
    if( ( is_array($qt_delete_items) and (count($qt_delete_items) > 0) ) )
    { 
	//print "--- qt_delete_items --- \n"; print_r($qt_delete_items);
	$this->erase_qt($qt_delete_items); 
    }
    else
    { echo "  qt: no excess items \n"; }
  
    //pridame chybejici zaznamy
    if( ( is_array($this->qt_ar_mis) and (count($this->qt_ar_mis) > 0) ) )
    { 
      //print_r($this->qt_ar_mis);    
      
      foreach($this->qt_ar_mis as $key => $val) 
      {
        //echo $key." => "; print_r($qt_system[$key])." \n";
    	$array = $qt_system[$key];
	
	unset($array["invalid"]);
        $this->qt_add_single($array);
      }
      
    }
    else
    { echo "  qt: no missing items \n"; }
    
    //a... hotovo :-)

  }
  else
  {
   //zaznamy sedi, nemam co delat
   echo "  qt: no excess or missing items (OK) \n";
  
  }
  
   
} //end of function datect_diff_queues

function qt_add_single($input_array)
{
      $qt_items_suc_added = 0;
      $qt_items_err_added = 0;

      $add_qt = $this->conn->add("/queue/tree", $input_array);

      if( ereg('^\*([[:xdigit:]])*$',$add_qt) )
      {
        if($this->debug > 0){ echo "    QT Item ".$add_qt." successfully added \n"; }
        $qt_items_suc_added++;
      }
      else
      {
        //if($this->debug > 0)
	{ echo "    ERROR: ".print_r($add_qt)."\n"; }
        $qt_items_err_added++;
      }

} //end of qt_add_single

function synchro_qt_force()
{

  //for testing, erasing arrays
  //$this->objects_sc = array();
  //$this->objects_mp = array();
  
  echo " qt - force rewriting ... \n";
  $this->qt_delete_all();

  echo " tarif info: SmallCity: agregace: ".$this->agregace_sc.", speed dwn: ".$this->speed_sc_dwn."k, upl: ".$this->speed_sc_upl."k \n";

  echo "  qt number of records tariff: sc: ".count($this->objects_sc).", mp: ".count($this->objects_mp)."\n";
  echo "  qt number of records tariff: garants: ".count($this->objects_garants_used)."\n";

  $sc_group=1;
  $sc_count=0;

  $limit_at_sc_dwn = ($this->speed_sc_dwn / $this->agregace_sc)*1000;
  $limit_at_sc_upl = ($this->speed_sc_upl / $this->agregace_sc)*1000;

  $qt_ip_suc_added=0;
  $qt_ip_err_added=0;

  //muster queues pro SC
  ///queue tree
  //add burst-limit=0 burst-threshold=0 burst-time=0s disabled=no limit-at=1024k max-limit=1024k name=\
  // q-dwn-sc-1 parent=global-in priority=1

  // 
  //  QT - SMallCity
  //
  foreach ($this->objects_sc as $key => $ip) {

    if($sc_count == 0)
    { //zresetovan citac sc, tj. vytvorime globalni skupinu

      $limit = ($this->speed_sc_dwn * $this->sc_speed_koef)*1000;

      $qt_items_suc_added=0;
      $qt_items_err_added=0;

      $add_qt_data = array ("disabled" => "false", "limit-at" => $limit, "max-limit" => $limit,
                     "name" => "q-dwn-sc-".$sc_group, "parent" => "global-out", "priority" => "1",
		     "queue" => "wireless-default");
      $add_qt = $this->conn->add("/queue/tree", $add_qt_data);

      if( ereg('^\*([[:xdigit:]])*$',$add_qt) )
      {
        if($this->debug > 0){ echo "    QT Item ".$add_qt." successfully added \n"; }
        $qt_items_suc_added++;
      }
      else
      {
        if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt)."\n"; }
        $qt_items_err_added++;
      }

      $limit = ($this->speed_sc_upl * $this->sc_speed_koef)*1000;

      $add_qt_data = array ("disabled" => "false", "limit-at" => $limit, "max-limit" => $limit,
                     "name" => "q-upl-sc-".$sc_group, "parent" => "global-out", "priority" => "1",
		     "queue" => "wireless-default");
      $add_qt = $this->conn->add("/queue/tree", $add_qt_data);

      if( ereg('^\*([[:xdigit:]])*$',$add_qt) )
      {
        if($this->debug > 0){ echo "    QT Item ".$add_qt." successfully added \n"; }
        $qt_items_suc_added++;
      }
      else
      {
        if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt)."\n"; }
        $qt_items_err_added++;
      }


      print "  add QT Group Sc No. ".$sc_group.", items ok: ".$qt_items_suc_added.", error: ".$qt_items_err_added."\n";

    }

    // add burst-limit=0 burst-threshold=0 burst-time=0s disabled=no limit-at=128k max-limit=1024k name=\
    // q-dwn-sc-10.2.2.2.2 packet-mark=10.52.5.14_dwn parent=q-dwn-sc-1 priority=1 queue=\
    // wireless-default


    $add_qt_data = array ("disabled" => "false", "limit-at" => $limit_at_sc_dwn, "max-limit" => (($this->speed_sc_dwn)*1000),
                     "name" => "q-dwn-sc-".$ip, "parent" => "q-dwn-sc-".$sc_group, "priority" => "1",
                     "packet-mark" => $ip."_dwn", "queue" => "wireless-default");

    $add_qt = $this->conn->add("/queue/tree", $add_qt_data);

    if( ereg('^\*([[:xdigit:]])*$',$add_qt) )
    {
      if($this->debug > 0){ echo "    QT Item ".$add_qt." successfully added \n"; }
      $qt_ip_suc_added++;
    }
    else
    {
      if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt)."\n"; }
      $qt_ip_err_added++;
    }

    $add_qt_data = array ("disabled" => "false", "limit-at" => $limit_at_sc_upl, "max-limit" => (($this->speed_sc_upl)*1000),
                     "name" => "q-upl-sc-".$ip, "parent" => "q-upl-sc-".$sc_group, "priority" => "1",
                     "packet-mark" => $ip."_upl", "queue" => "wireless-default");
    $add_qt = $this->conn->add("/queue/tree", $add_qt_data);

    if( ereg('^\*([[:xdigit:]])*$',$add_qt) )
    {
      if($this->debug > 0){ echo "    QT Item ".$add_qt." successfully added \n"; }
      $qt_ip_suc_added++;
    }
    else
    {
      if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt)."\n"; }
      $qt_ip_err_added++;
    }

    //konec cyklu
    $sc_count++;

    if( $sc_count == $this->agregace_sc)
    {
      $sc_count=0;
      $sc_group++;
    }
  }

  print " qt: count of added items: ok: ".$qt_ip_suc_added.", error: ".$qt_ip_err_added."\n";

  //
  // QT Force - MP linky
  //
  $qt_mp_items_suc_added=0;
  $qt_mp_items_err_added=0;

  //globalni tridy pro MP
  
  $add_qt_mp_global_data = array ("disabled" => "false", "limit-at" => "0", "max-limit" => "100000k",
                     "name" => "q-dwn-mp-global", "parent" => "global-out", "priority" => "1",
                     "queue" => "wireless-default");
		     
  $add_qt_mp_global = $this->conn->add("/queue/tree", $add_qt_mp_global_data);

  if( ereg('^\*([[:xdigit:]])*$',$add_qt_mp_global) )
  {
    if($this->debug > 0){ echo "    QT Item ".$add_qt_mp_global." successfully added \n"; }
    $qt_mp_items_suc_added++;
  }
  else
  {
    if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_mp_global)."\n"; }
    $qt_mp_items_err_added++;
  }

  $add_qt_mp_global_data2 = array ("disabled" => "false", "limit-at" => "0", "max-limit" => "100000k",
                     "name" => "q-upl-mp-global", "parent" => "global-out", "priority" => "1",
                     "queue" => "wireless-default");
		     
  $add_qt_mp_global2 = $this->conn->add("/queue/tree", $add_qt_mp_global_data2);

  if( ereg('^\*([[:xdigit:]])*$',$add_qt_mp_global2) )
  {
    if($this->debug > 0){ echo "    QT Item ".$add_qt_mp_global2." successfully added \n"; }
    $qt_mp_items_suc_added++;
  }
  else
  {
    if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_mp_global2)."\n"; }
    $qt_mp_items_err_added++;
  }
        
  foreach ($this->objects_mp as $key => $ip) {

     $add_qt_data_mp = array ("disabled" => "false", "limit-at" => "100k", "max-limit" => "10000k",
                     "name" => "q-dwn-mp-".$ip, "parent" => "q-dwn-mp-global", "priority" => "1",
                     "packet-mark" => $ip."_dwn", "queue" => "wireless-default");
		     
      $add_qt_mp = $this->conn->add("/queue/tree", $add_qt_data_mp);

      if( ereg('^\*([[:xdigit:]])*$',$add_qt_mp) )
      {
        if($this->debug > 0){ echo "    QT Item ".$add_qt_mp." successfully added \n"; }
        $qt_mp_items_suc_added++;
      }
      else
      {
        if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_mp)."\n"; }
        $qt_mp_items_err_added++;
      }

     $add_qt_data_mp2 = array ("disabled" => "false", "limit-at" => "100k", "max-limit" => "10000k",
                     "name" => "q-upl-mp-".$ip, "parent" => "q-upl-mp-global", "priority" => "1",
                     "packet-mark" => $ip."_upl", "queue" => "wireless-default");
		     
      $add_qt_mp2 = $this->conn->add("/queue/tree", $add_qt_data_mp2);

      if( ereg('^\*([[:xdigit:]])*$',$add_qt_mp2) )
      {
        if($this->debug > 0){ echo "    QT Item ".$add_qt_mp2." successfully added \n"; }
        $qt_mp_items_suc_added++;
      }
      else
      {
        if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_mp2)."\n"; }
        $qt_mp_items_err_added++;
      }

  }

  print " qt: number of records with MP: added items: ok: ".$qt_mp_items_suc_added.", error: ".$qt_mp_items_err_added."\n";
  
  //
  // QT Force - Garanty
  //
  $qt_g_items_suc_added = 0;
  $qt_g_items_err_added = 0;
  
  foreach( $this->objects_garants_used as $garant_id => $speeds)
  {
    list($speed_dwn, $speed_upl) = explode(":",$speeds);
    
    print " qt-force :: garants :: ".$garant_id.", speed_dwn: ".$speed_dwn.", speed_upl: ".$speed_upl."\n";
    
    $add_qt_data_g = array ("disabled" => "false", "limit-at" => $speed_dwn."k", "max-limit" => $speed_dwn."k",
                 "name" => "q-dwn-".$garant_id, "parent" => "global-out", "priority" => "1", "queue" => "wireless-default");
		     
    $add_qt_g = $this->conn->add("/queue/tree", $add_qt_data_g);

    if( ereg('^\*([[:xdigit:]])*$',$add_qt_g) )
    {
        if($this->debug > 0){ echo "    QT Item (garant parent dwn) ".$add_qt_g." successfully added \n"; }
        $qt_g_items_suc_added++;
    }
    else
    {
	if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_q)."\n"; }
    	$qt_g_items_err_added++;
    }
    
    $add_qt_data_g2 = array ("disabled" => "false", "limit-at" => $speed_upl."k", "max-limit" => $speed_upl."k",
                     "name" => "q-upl-".$garant_id, "parent" => "global-out", "priority" => "1", "queue" => "wireless-default");
	     
    $add_qt_g2 = $this->conn->add("/queue/tree", $add_qt_data_g2);

    if( ereg('^\*([[:xdigit:]])*$',$add_qt_g2) )
    {
        if($this->debug > 0){ echo "    QT Item (garant parent upl) ".$add_qt_g2." successfully added \n"; }
        $qt_g_items_suc_added++;
    }
    else
    {
        if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_q2)."\n"; }
        $qt_g_items_err_added++;
    }
    
    foreach( $this->{$garant_id} as $id => $ip)
    {

	$add_qt_data_g_dwn = array ("disabled" => "false", "name" => "q-dwn-q-".$ip, "parent" => "q-dwn-".$garant_id,
				    "priority" => "1", "packet-mark" => $ip."_dwn", "queue" => "wireless-default");
		     
	$add_qt_g_dwn = $this->conn->add("/queue/tree", $add_qt_data_g_dwn);

	if( ereg('^\*([[:xdigit:]])*$',$add_qt_g_dwn) )
	{
    	    if($this->debug > 0){ echo "    QT Item (garant ".$ip." dwn) ".$add_qt_g_dwn." successfully added \n"; }
    	    $qt_g_items_suc_added++;
	}
	else
	{
	    if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_q_dwn)."\n"; }
    	    $qt_g_items_err_added++;
	}
    
	$add_qt_data_g_upl = array ("disabled" => "false", "name" => "q-upl-q-".$ip, "parent" => "q-upl-".$garant_id,
				     "priority" => "1", "packet-mark" => $ip."_upl", "queue" => "wireless-default");
	     
	$add_qt_g_upl = $this->conn->add("/queue/tree", $add_qt_data_g_upl);

	if( ereg('^\*([[:xdigit:]])*$',$add_qt_g_upl) )
	{
    	    if($this->debug > 0){ echo "    QT Item (garant ".$ip." upl) ".$add_qt_g_upl." successfully added \n"; }
    	    $qt_g_items_suc_added++;
	}
	else
	{
    	    if($this->debug > 0){ echo "    ERROR: ".print_r($add_qt_q2)."\n"; }
    	    $qt_g_items_err_added++;
	}
	

    } //end of FOREACH $this->{$garant_id}
  
  } //end of FOREACH objects_garants_used
  
  print " qt: number of records with GARANT: added items: ok: ".$qt_g_items_suc_added.", error: ".$qt_g_items_err_added."\n";
  
} //end of function synchro_qt_force


} //end of class mk_synchro_qos



?>
