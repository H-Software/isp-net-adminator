<?php

class fakturacni{
	var $echo = true;
	var $firma;
	
    function vypis($id_f,$id_v)
    {    
		$output = "";

		$dotaz=pg_query( "SELECT * FROM fakturacni where id='$id_f' ");
		$dotaz_radku=pg_num_rows($dotaz);
			
		if ( $dotaz_radku==0 )
		{
			$output .= "<tr><td> CHYBA! Fakturacni udaje nenalezeny. debug: id=$id_f </td></tr>";
		}
		else
		{
			while( $data=pg_fetch_array($dotaz) ):
				$output .= "<tr>";

				if( $this->firma == 1)
				{ $output .= "<td></td>"; }

				$output .= " <td colspan=\"2\"> <b>Fakturační údaje:</b> <br>".$data["ftitle"]." ".$data["fadresa"]."<br> ";
				$output .= $data["fulice"]." <br> ";

				$output .= $data["fmesto"]." ".$data["fpsc"]."</td>";

				$output .= "<td colspan=\"12\">ičo: ".$data["ico"].", dič: ".$data["dic"];
				$output .= "<br>účet: ".$data["ucet"]." <br> splatnost (dnů): ".$data["splatnost"];
				$output .= "<br> četnost: ".$data["cetnost"]."</td>";
						
			endwhile;										  
		}

		if($this->echo === true){
			echo $output;
		}
		else{
			return $output;
		}
    } // konec funkce vypis
}
