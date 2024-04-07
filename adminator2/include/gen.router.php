<?php

function vypis_router($id)
{
    include("config.php");
    
    global $mac;
    
    $dotaz_router=mysql_query("SELECT * FROM router_list WHERE id = $id order by id");
    $dotaz_router_radku=mysql_num_rows($dotaz_router);

    if ($dotaz_router_radku <> 1 )
    {
    echo "Chybnej pocet radku";
    $mac="E";
    
    }
    else
    {
     while($data=mysql_fetch_array($dotaz_router))
     {
	$parent_router=$data["parent_router"];
	
	if ( $parent_router == 0)
	{ 
	//erik - nedelat nic
	
	}
	elseif ($parent_router == 1 )
	{
	// konec retezce, vypisem
	
	 if ( ( strlen($mac) <= 0) ){ $mac=$data["mac"]; }
	 // $mac="CCC";
	 //if ( ( strlen($rb_ip) <= 0) ) { $rb_ip=$data["ip_adresa"]; }
									  
	}
	else
	{
	    vypis_router($parent_router);
	}

    } // konec while
   } // konec else
   
} // konec funkce
?>