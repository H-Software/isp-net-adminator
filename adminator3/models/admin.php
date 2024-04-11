<?php

class admin {
	var $conn_mysql;

	function __construct($conn_mysql) {
		$this->conn_mysql = $conn_mysql;
	}

	function levelList(){

		$output  = "";

		try {
			$vysledek = $this->conn_mysql->query("select * from leveling order by level asc");
		} catch (Exception $e) {
			die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}
	
		$radku=$vysledek->num_rows;
		
		if ($radku==0) $output .= "Zadné levely v db (divny) ";
		else
		{
			$output .= '<br><br>Výpis levelů stránek: <BR><BR>';
						
			$output .= '<table border="1" width="100%" >';
								
			$output .= "\n<tr>
			<td width=\"5%\"><b>id:</b></td>
			<td width=\"30%\"><b>Popis: </b></td>
													
			<td width=\"20%\"><b>Level: </b></td>
						
			<td width=\"10%\"><b>Úprava: </b></td>
			<td width=\"10%\"><b>Smazání: </b></td>
			</tr>\n";
								
			$output .= "\n";
		
			while ($zaznam=$vysledek->fetch_array()):
				$id=$zaznam["id"];
				
				$output .= "<tr><td>".$zaznam["id"]."</td>\n";
				$output .= "<td>".$zaznam["popis"]."</td>\n";
				
				$output .= "<td>".$zaznam["level"]."</td>\n";
				
				$output .= '<td><form method="POST" action="admin-level-update.php">
					<input type="hidden" name="update_id" value="'.$id.'">
				<input type="submit" value="update">
				</form></td>';							
				
				$output .= "</tr>";
		
			  endwhile;
		}
		return $output;
	}

}
