<?php

// ! trida pro synchronizaci RouterOS zarízení, co budou delat omezeni typu povoleni/zakazani inetu/sikana
// ! krz MK API
// !
// ! 2010/2/15
// ! 
// ! created by Patrik "hujer" Majer (hujer@simelon.net)
// !
// !

class mk_net_n_sikana
{
 var $conn_mysql;

 var $conn;
 
 var $debug = 0; //uroven nebo on/off stav debug výpisů
 
 var $objects_net_n = array(); //pole s objekty, ktere maji NetN
 
 var $objects_sikana = array(); //pole s objekty, ktere maji Sikanu
 
 var $wrong_items = array(); //pole pro spatne objekty (zakazane)
 
 var $getall; //pole pro export dat z /ip/firewall/address-list
 
 var $device_items = array(); //pole pro objekty net_n v zarizeni
 
 var $arr_diff_exc = array();
 
 var $arr_diff_mis = array();
 
 var $rs_objects;
 
 function find_root_router($id_routeru, $ip_adresa_routeru)
 {
    $rs = $this->conn_mysql->query("SELECT parent_router, ip_adresa FROM router_list WHERE id = '$id_routeru'");

    while( $d = $rs->fetch_array() )
    { $parent_router = $d["parent_router"]; }

    $rs2 = $this->conn_mysql->query("SELECT parent_router, ip_adresa FROM router_list WHERE id = '$parent_router'");

    while( $d2 = $rs2->fetch_array() )
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
    $routers = array();

    //1. zjistit routery co jedou pres reinhard-fiber
    $rs_routers = $this->conn_mysql->query("SELECT id, parent_router, nazev FROM router_list ORDER BY id");
    $num_rs_routers = $rs_routers->num_rows;

    if($num_rs_routers < 1){
      echo "mk_net_n_sikana\\find_obj: query failed: no router found! <br>\n";
      return false;
    }

    while($data_routers = $rs_routers->fetch_array())
    {
      $id_routeru = $data_routers["id"];
      if( $this->find_root_router($id_routeru,$ip) === true)
      { $routers[] = $id_routeru; }
    }

    if (count($routers) < 1){
      echo "mk_net_n_sikana\\find_obj: Error: no downstream/connected router found! <br>\n";
      return false;
    }
    else{
      echo "mk_net_n_sikana\\find_obj: INFO: found " . count($routers) . " router(s)<br>\n";
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

  $rs_nods = $this->conn_mysql->query($sql);
  $num_rs_nods = $rs_nods->num_rows;

  while($data_nods = $rs_nods->fetch_array())
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

  $sql_obj = "SELECT ip, dov_net, sikana_status 
		FROM objekty 
	      WHERE (
	       id_nodu IN (".$sql_obj_where.") 
	       AND
	       (
	        objekty.dov_net = 'n'::bpchar
	        OR
		      objekty.sikana_status ~~ '%a%'::text
	       )
	      )
	      ORDER BY id_komplu";
  //print $sql_obj."\n";

  $this->rs_objects = pg_query($sql_obj);
  if($this->rs_objects === false){
    echo "mk_net_n_sikana\\find_obj: Error: Pg_query failed! <br>\n";
    echo pg_last_error() . "<br>\n";
    return false;
  }

  $num_rs_objects = pg_num_rows($this->rs_objects);

  while( $data = pg_fetch_array($this->rs_objects))
  {
    
    if( $data["dov_net"] == "n")
    { $this->objects_net_n[] = $data["ip"]; }
    elseif( $data["sikana_status"] == "a" )
    { $this->objects_sikana[] = $data["ip"]; }
    else
    { echo "  ERROR: wrong item selected (IP: ".$data["ip"].") \n"; }
  }

  print " number of restricted IP addresses: ".$num_rs_objects;
  if($this->debug == 1)
  { echo ", array objects counts: ".count($this->objects_net_n)." ".count($this->objects_sikana); }
  
  echo "\n";

 } //end of function

 function remove_wrong_items($wrong_items)
 {
   $item_del_ok=0;
   $item_del_err=0;

   //print_r($wrong_items);

  $del = $this->conn->remove("/ip/firewall/address-list", $wrong_items);

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

 function detect_diff_and_repaid($mod)
 {
  if( !( ($mod == "sikana") or ($mod == "net-n") ))
  { 
    echo "ERROR: wrong mode in function \"detect_diff\" \n";
    exit;
  }
  
  $this->wrong_items = array();
  $this->device_items = array();
  
  $this->arr_diff_exc = array();
  $this->arr_diff_mis = array();
  
  if($mod == "net-n")
  { $system_items = $this->objects_net_n; }
  else
  { $system_items = $this->objects_sikana; }
  
  $this->getall = $this->conn->getall(array("ip", "firewall", "address-list") );

  foreach ($this->getall as $key => $value) {
  
   if( $this->getall["$key"]["list"] == "$mod")
   {
     $id = $this->getall["$key"][".id"];
     
     if( $this->getall["$key"]["disabled"] == "true" )
     { $this->wrong_items[] = $id; }
     else
     { $this->device_items[$id] = $this->getall["$key"]["address"]; }
     
     //print_r($this->getall["$key"]);
   }
  
  } //end of foreach getall 

  echo " $mod: number of records : device: ".count($this->device_items).", system: ".count($system_items)."\n";

  
  $this->arr_diff_exc = array_diff($this->device_items, $system_items);
  $this->arr_diff_mis = array_diff($system_items, $this->device_items);

  //print_r($this->arr_diff_exc); 
  //print_r($system_items);
    
 if( ((count($this->arr_diff_exc) == 0) and (count($this->arr_diff_mis) == 0) and (count($this->wrong_items) == 0) ) )
 { echo "  $mod: records OK \n"; }
 else
 {
     foreach($this->arr_diff_exc as $key => $value)
     { $this->wrong_items[] = $key; }
     
     echo "  $mod: number of records : excess: ".count($this->wrong_items).", missing: ".count($this->arr_diff_mis)."\n";
    
     //print_r($this->wrong_items);
     if( (count($this->wrong_items) > 0) )
     { $this->remove_wrong_items($this->wrong_items); }
 
     if( (count($this->arr_diff_mis) > 0 ) )
     $this->add_items($mod);
 }
  

} //end of function detect_diff_records

 function add_items($mod)
 {
  if( !( ($mod == "sikana") or ($mod == "net-n") ))
  { 
    echo "ERROR: wrong mode in function \"add_items\" \n";
    exit;
  }
  
  $item_err_added=0;
  $item_suc_added=0;
  
  foreach($this->arr_diff_mis as $key => $ip)
  {
 
    $add_data = array ("address" => $ip, "list" => $mod);
    $add_item = $this->conn->add("/ip/firewall/address-list", $add_data);

    if( ereg('^\*([[:xdigit:]])*$',$add_item) )
    {
      if($this->debug > 0){ echo "    Item ".$add_item." successfully added \n"; }
      $item_suc_added++;
    }
    else
    {
      if($this->debug > 0){ echo "    ERROR: ".print_r($add_item)."\n"; }
      $item_err_added++;
    }
  
  
  } //end of foreach
  
  echo "  $mod add items ok: ".$item_suc_added.", error: ".$item_err_added."\n";
    
 
 } //end of function add_items

 function zamek_lock()
 {
    $rs = $this->conn_mysql->query("UPDATE workzamek SET zamek = 'ano' WHERE id = 1");
 }
	
 function zamek_unlock()
 {
    $rs = $this->conn_mysql->query("UPDATE workzamek SET zamek = 'ne' WHERE id = 1");
 }
		
 function zamek_status()
 {
    $rs = $this->conn_mysql->query("SELECT zamek FROM workzamek WHERE id = 1");
			
    while( $data = $rs->fetch_array() )
    { $zamek_status = $data["zamek"]; }
				
    if( $zamek_status == "ano" )
    {
        print "  Nelze provést AKCI, jiz se nejaka provadi (LOCKED). Ukončuji skript. \n";
	      exit;
    }
    							    
 } //end of function zamek_status
							     
} //end of class mk_synchro_qos



?>
