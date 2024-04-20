<?php

class fakturacni
{

    function vypis($id_f,$id_v)
    {
    // $id="";
    
	$dotaz=pg_query( "SELECT * FROM fakturacni where id='$id_f' ");
	$dotaz_radku=pg_num_rows($dotaz);
    
        
    if ( $dotaz_radku==0 )
    {
    echo "<tr><td> CHYBA! Fakturacni udaje nenalezeny. debug: id=$id_f </td></tr>";
    }
    else
    {
    

	 while( $data=pg_fetch_array($dotaz) ):
			     
	 // echo "<tr><td colspan=\"14\"> <br> </td> </tr>";
	  
	  echo "<tr>";
	  
	  if( $firma == 1)
	  { echo "<td></td>"; }
	  
	  echo " <td colspan=\"2\"> Fakturační údaje: <br>".$data["ftitle"]." ".$data["fadresa"]."<br> ";
	  echo $data["fulice"]." <br> ";
	  
	  echo $data["fmesto"]." ".$data["fpsc"]."</td>";
	  
	  echo "<td colspan=\"12\">ičo: ".$data["ico"].", dič: ".$data["dic"];
	  echo "<br>účet: ".$data["ucet"]." <br> splatnost (dnů): ".$data["splatnost"];
	  echo "<br> četnost: ".$data["cetnost"]."</td>";
			  
	endwhile;
															  
     }
     
    } // konec funkce vypis


}
