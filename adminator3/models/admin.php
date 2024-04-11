<?php

class admin {
	var $conn_mysql;

	function __construct($conn_mysql) {
		$this->conn_mysql = $conn_mysql;
	}

	function levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value){

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
				
				$output .= '<td>
					<form method="POST" action="/admin/level-action">
						<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">
						<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">
						<input type="hidden" name="update_id" value="'.$id.'">
						<input type="submit" value="update">
					</form></td>';
				
				$output .= "</tr>";
		
			  endwhile;
		}
		return $output;
	}

	function levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value){

		$output = "";

		if ( ( $_POST["popis_new"] ) )
		{
			//budeme ukladat
			$popis=$_POST["popis_new"];
			$level=$_POST["level_new"];
	
			$output .= "Zadáno do formuláre : <br><br>";
			$output .= "popis stránky: ".$popis."<br>";
			$output .= "level stránky: ".$level."<br>";
			
			$id_new=intval($_POST["new_id"]);
		
			if($id_new > 0 ){
				// update
				$sql = "UPDATE leveling SET popis='$popis', level='$level' where id=".$id_new;
			}
			else{
				// novy zaznam
				$sql = "";
			}

			try {
				$rs = $this->conn_mysql->query($sql);
			} catch (Exception $e) {
				die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
			}
		
			if ($rs) $output .= "<br><br>MySql potvrdilo, takze: <br><H2>Data v databazi upravena.</H2><br><br>";
			else $output .= "Houstone, tento zapis do databaze nevysel :)";
																			
		}
		else
		{
			//zobrazime formular
	
			//nejdrive nacteme predchozi data
			$update_id=intval($_POST["update_id"]);
	
			if($update_id > 0)
			{
				try {
					$vysledek = $this->conn_mysql->query("select * from leveling where id = $update_id ");
				} catch (Exception $e) {
					die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
				}

				$radku = $vysledek->num_rows;
			
				if ($radku==0) $output .= "Zadné levely v db (divny) ";
				else
				{
					while ($zaznam=$vysledek->fetch_array()):
						$id=$zaznam["id"];
						$popis=$zaznam["popis"];
						$level=$zaznam["level"];
					endwhile;	
				}
			}

			$output .= '
				<br><H4>Úprava levelu stránky: </H4><br>
				<form method="POST" action="'.$_SERVER['SCRIPT_URL'].'">
				<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">
				<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">
				';

			if($update_id > 0){
				$output .= "<input type=\"hidden\" name=\"new_id\" value=\" " .$id . "\">";
			}

			$output .= '<table border="0" width="100%" id="table2">
				<tr>
				<td width="25%"><label>Polozky: </label></td>
				<td><input type="text" name="popis_new" size="30" value="'.$popis.'"></td
				</tr>

				<tr>
					<td><label>Level: </label></td>
						<td><input type="text" name="level_new" size="10" value="'.$level.'"></td>
				</tr>

				<tr>
					<td><br></td>
					<td></td>
				</tr>

					<tr>
					<td></td>
					<td><input type="submit" value="OK" name="B1">
						<input type="reset" value="vymazat" name="B2"></td>
					</tr>

				</table>

				</form>';

		}

		return array($output);
	}
}
