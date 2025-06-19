<?php

// ! trida pro synchronizaci RouterOS zarízení, co budou delat QoS/marking  trafficu
// ! krz MK API
// !
// ! 2010/2/15
// ! 
// ! created by Patrik "hujer" Majer
// !
// !

class mk_synchro_qos
{
 var $conn;
 
 var $debug; //uroven nebo on/off stav debug výpisů
 
 var $objects; //pole s objekty, ktere jedou krz tento router
 
 var $getall_mangle; //pole pro export dat z /ip/firewall/mangle
 
 var $element_name_dwn; //nazev prvku kterej urcuje dst ip adrress, pouziti u parsingu dat
 var $element_name_upl; // dtto pro src ip adrress, pouziti u parsingu dat
 
 var $item_ip_dwn;  //nazev polozky, ktera ma specifikovat dst ip adress, pouzito pri vkladani zaznamu
 var $item_ip_upl;  //nazev polozky, ktera ma specifikovat src ip adress, pouzito pri vkladani zaznamu
 
 var $wrong_firewall_items; //polozky ve /ip/firewall/mangle, ktere jsou na smazani
 
 var $force_mangle_rewrite; //stav (0/1) jestli se ma navrdo premazat firewall 
 
 var $arr_global_diff_exc; //pole s prebyvajicimi prvky v mangle
 var $arr_global_diff_miss; //pole s chybejicimi prvky v mangle
 
 var $agregace_sc; //agragace SmallCity
 
 var $speed_sc_dwn; //rychlosti smallcity linek :)
 var $speed_sc_upl;
 
 //pole pro trideni dle typu linky, pro ptoreby QT
 var $objects_sc;
 var $objects_mp;
  
 var $sc_speed_koef; //koeficient pro nasobeni parent tridy u SC linek


 function multidimensional_array_diff($a1,$a2) 
 { 
   $r = array(); 
  
   foreach ($a2 as $key => $second) 
   { 
      foreach ($a1 as $key => $first) 
      { 
              
            if (isset($a2[$key])) 
            { 
                foreach ($first as $first_value) 
                { 
                    foreach ($second as $second_value) 
                    { 
                        if ($first_value == $second_value) 
                        { 
                            $true = true; 
                            break;    
                        } 
                    } 
                    if (!isset($true)) 
                    { 
                        
                        $r[$key][] = $first_value; 
                    } 
                    unset($true); 
                } 
            } 
            else 
            { 
                $r[$key] = $first; 
            } 
      } 
  }
   
  return $r; 
 
 } //end of function multidimensional array diff

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
  $rs_routers = $conn_mysql->query("SELECT id, parent_router, nazev FROM router_list ORDER BY id");
  $num_rs_routers = mysql_num_rows($rs_routers);

  while($data_routers = mysql_fetch_array($rs_routers))
  {
   $id_routeru = $data_routers["id"];
   if( $this->find_root_router($id_routeru,$ip) === true)
   { $routers[] = $id_routeru; }
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

  foreach ($nods as $key => $id_nodu ){
    //print "nods: ".$id_nodu." \n";

    if($i == 0)
    { $sql_obj_where .= "'$id_nodu'"; }
    else
    { $sql_obj_where .= ",'$id_nodu'"; }

    $i++;
  }

  $sql_obj = "SELECT ip, id_tarifu, dns_jmeno FROM objekty WHERE id_nodu IN (".$sql_obj_where.") ORDER BY id_komplu";
  //print $sql_obj."\n";

  $rs_objects = pg_query($sql_obj);
  $num_rs_objects = pg_num_rows($rs_objects);

  while( $data = pg_fetch_array($rs_objects))
  {
    $ip = $data["ip"];
    $this->objects[$ip] = $data["id_tarifu"];
  }

  print " number of IP addresses via this router: ".$num_rs_objects;
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
        { $arr_objects_dev_dwn[$ip_dwn] = $this->getall_mangle["$key"][".id"]; }
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
        { $arr_objects_dev_upl[$ip_upl] = $this->getall_mangle["$key"][".id"]; }
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
 if( !(is_array($arr_objects_dev_upl)) )
 { $arr_objects_dev_upl = array(); }

 if( !(is_array($arr_objects_dev_dwn)) )
 { $arr_objects_dev_dwn = array(); }

 $arr_obj_dev_diff = array_diff_key($arr_objects_dev_dwn, $arr_objects_dev_upl);
 //print_r($arr_obj_dev_diff);

 $arr_obj_dev_diff2 = array_diff_key($arr_objects_dev_upl, $arr_objects_dev_dwn);
 //print_r($arr_obj_dev_diff2);

 if( (count($arr_obj_dev_diff) > 0) or ( count($arr_obj_dev_diff2) > 0) )
 {
    echo " ERROR: Rozdilny pocet zaznamu pro DWN a UPL. Forcing a full sync... \n";
    $this->force_mangle_rewrite = 1;
 }
 else
 {
    echo " number of records : device: ".count($arr_objects_dev_dwn).", system: ".count($this->objects)."\n";

    $this->arr_global_diff_exc = array_diff_key($arr_objects_dev_dwn, $this->objects);
    $this->arr_global_diff_mis = array_diff_key($this->objects, $arr_objects_dev_dwn);
 }

} //end of function detect_diff_in_mangle

function erase_mangle(){

  $items_suc_del = 0;
  $items_err_del = 0;

  foreach ($this->getall_mangle as $key => $value) {

    /*
    if(asi nejake yjimky)
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

   $index = $arr_objects_dev_dwn["$ip"];
   $index2 = $arr_objects_dev_upl["$ip"];

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
    } elseif($linka == 0) {
        $this->objects_mp[] = $ip;
    }

    //ehm zde garanti este

    else
    {
        echo " WARNING: Neznámá linka (".$linka.") u objektu: ".$ip."\n";
    }
  }

  echo "  qt(global) number of records tariff: sc: ".count($this->objects_sc).", mp: ".count($this->objects_mp)."\n";

}

function qt_delete_all(){

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

function detect_diff_queues() {

 //1. zjistime co je v zarizeni
 //$qt_dump = $this->conn->getall( array("queue","tree"), "", "", ".id" );
 $qt_dump = $this->conn->getall( array("queue","tree"));

 // print_r($qt_dump);
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

/*
      //muster
      [name] => q-dwn-sc-1
      [parent] => global-out
      [limit-at] => 2252800
      [priority] => 1
      [max-limit] => 2252800
      [burst-limit] => 0
      [burst-threshold] => 0
      [burst-time] => 00:00:00
      [invalid] => false
      [disabled] => false
*/
    
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
  
  //
  // 2.3 - Garanti
  //
  
  
  //
  // 3. porovname pole 
  //

  print_r($qt_dump_trim);
  print_r($qt_system);

/*
  if( (count($qt_dump_trim) != count($qt_system) ))
  {
    echo " qt - does not match the number of elements - forcing rewrite ..";
    
    $this->synchro_qt_force();    
  }
  else
  {
   echo " qt check excess records  -- \n";
  
   foreach ($qt_dump_trim as $id => $arr_values) {
    
    $arr2 = array(0);
    $arr2 = $qt_system["$id"];
    
    $arr_tmp_exc = array_diff($arr_values, $arr2);
  
    if( (count($arr_tmp_exc) > 0) )
    { 
	$arr_qt_dev_exc[] = $id; 
	print_r($arr_tmp_exc);
    }
    
  } //end of foreach $qt_dump_trim

  echo " qt check missing records  -- \n";
  
  foreach ($qt_system as $id => $arr_values) {
 
    $arr_tmp_miss = array_diff($arr_values, $qt_dump_trim["$id"]);
  
    if( (count($arr_tmp_miss) > 0) )
    { 
	$arr_qt_dev_miss[] = $id; 
	print_r($arr_tmp_miss);
    }
    
    
  } //end of foreach

  //$arr_qt_dev_exc = $this->multidimensional_array_diff($qt_dump_trim, $qt_system);

  //$arr_qt_dev_exc = array_diff($qt_dump_trim, $qt_system);
  //print_r($arr_qt_dev_exc);

  //$arr_qt_dev_miss = $this->multidimensional_array_diff($qt_system, $qt_dump_trim);

  //$arr_qt_dev_miss = array_diff($qt_system, $qt_dump_trim);
  //print_r($arr_qt_dev_miss);

  echo " qt: number of records : excess: ".count($arr_qt_dev_exc).", missing: ".count($arr_qt_dev_miss)."\n";

 } //end of else
*/
 
} //end of function datect_diff_queues

function synchro_qt_force()
{

  echo " qt - force rewriting ... \n";
  $this->qt_delete_all();

  echo " tarif info: SmallCity: agregace: ".$this->agregace_sc.", speed dwn: ".$this->speed_sc_dwn."k, upl: ".$this->speed_sc_upl."k \n";

  echo "  qt number of records tariff: sc: ".count($this->objects_sc).", mp: ".count($this->objects_mp)."\n";

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
  // MP linky
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
  
} //end of function synchro_qt_force


} //end of class mk_synchro_qos



?>
