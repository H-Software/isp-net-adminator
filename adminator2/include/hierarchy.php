<?php

function vypis_router($id,$uroven)
{

 global $uroven_max;
 
 $dotaz_router=mysql_query("SELECT * FROM router_list WHERE id = $id order by id");
 $dotaz_router_radku=mysql_num_rows($dotaz_router);
	    
if ( $dotaz_router_radku > 0 )
{
				    
   while($data_router=mysql_fetch_array($dotaz_router))
   {
								
  echo "<tr>";
  
   for ( $j=0;$j<$uroven; $j++){ echo "<td><br></td>"; }
   
   echo "<td align=\"center\">|------> </td>";
   echo "<td>";
			   
    echo " [".$data_router["id"]."] <b>".$data_router["nazev"]."</b>";
						       
    echo " <span style=\"color:grey; \">( ".$data_router["ip_adresa"]." ) </span>";
									   
    echo "</td>";
    
   echo "</tr>";

    //zde rekurze
    $parent_id=$data_router["id"];

    $dotaz_router_parent=mysql_query("SELECT * FROM router_list WHERE parent_router = $id order by id");
    $dotaz_router_parent_radku=mysql_num_rows($dotaz_router_parent);
        
    if ( $dotaz_router_parent_radku > 0 )
    {
    
    $iterace = 1;
    
    while($data_router_parent=mysql_fetch_array($dotaz_router_parent) )
    {
    
    $uroven++;
    
    if ( ($uroven > $uroven_max) ){ $uroven_max = $uroven; }
    
    $id=$data_router_parent["id"];
    
    vypis_router($id,$uroven);
    
    $iterace++;
    
    if ( $iterace > 1){ $uroven--; }
    }
    // else
    // { $uroven--; }
    
    }
    
    //return echo $text;    
    }													  
     
}
else
{ 
  return false; 
}

}

?>