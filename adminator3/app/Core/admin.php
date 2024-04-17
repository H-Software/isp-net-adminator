<?php

class admin {
	var $conn_mysql;

	function __construct($conn_mysql, $logger) {
		$this->conn_mysql = $conn_mysql;
		$this->logger = $logger;
	}

	function levelListDbQuery()
	{
		try {
			$rs= $this->conn_mysql->query("select * from leveling order by level asc");
		} catch (Exception $e) {
			$this->logger->addInfo("admin\LevelList: levelListDbQuery exception: " . var_export($e->getMessage(), true));
		}
	
		$num_rows = $rs->num_rows;

		if ($num_rows > 0)
		{
			$data = $rs->fetch_all(MYSQLI_ASSOC);
		}

		return array($num_rows, $data);
	}

	function levelListJson()
	{
		$r_data = array();
		$r_status = 418;
		$r_msg = "";

        // $r_data = ['username' => 'leego.sir',  'age' => 18];

		list($q_num_rows, $q_data) = $this->levelListDbQuery();
		// $this->logger->addInfo("admin\LevelList dump q_data: " . var_export($q_data, true));

		if ($q_num_rows==0)
		{
			$r_data = array(0 => "Zadné levely v databazi");
		}
		else
		{
			$r_data = $q_data;
		}

		return array($r_data, $r_status, $r_msg);
	}

	function levelList($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value){

		$output  = "";
		
		if($_POST['search'])
		{
			$search_string = $this->conn_mysql->real_escape_string($_POST['search']);
			$this->logger->addInfo("admin\LevelList search string: " . var_export($search_string, true));
		}

		list($q_num_rows, $q_data) = $this->levelListDbQuery();

		// $this->logger->addInfo("admin\LevelList dump q_data: " . var_export($q_data, true));

		$output .= '<div style="padding-top: 10px; padding-bottom: 5px;" class="fs-5">Výpis levelů stránek</div>';

		if ($q_num_rows==0) $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"padding-top: 5px; padding-bottom: 5px;\">Zadné levely v databazi</div>";
		else
		{						
			// $output .= '<table class="table table-striped fs-6">';
			$output .= '<table
							id="level-list"
							class="table table-striped fs-6"
							data-toggle="table"
							data-pagination="true"
							data-side-pagination="client"
							data-search="true"
							';
							
			$output .= '>';

			$output .= "\n
			<thead>
				<tr class=\"table-light\">
					<th width=\"5%\" scope=\"col\" data-field=\"id\" data-sortable=\"true\">id</th>
					<th width=\"30%\" scope=\"col\" data-field=\"name\">Popis</th>
															
					<th width=\"20%\" scope=\"col\" data-field=\leve\">Level</th>
								
					<th width=\"10%\" scope=\"col\">Úprava</th>
					<th width=\"10%\" scope=\"col\">Smazání</th>
				</tr>
			</thead>
			<tbody id=\"hidden\"> <!-- hidden is used because of jquery duplicates this element -->
			\n";
			
			foreach ($q_data as $d){
				$output .= "<tr>"
							. "<td scope=\"row\">".$d["id"]."</td>\n"
							. "<td >".$d["popis"]."</td>\n"
							. "<td>".$d["level"]."</td>\n"
							. '<td>
								<form method="POST" action="/admin/level-action" >
									<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">
									<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">
									<input type="hidden" name="update_id" value="'.$d['id'].'">
									<input type="submit" value="update">
								</form>'
							 . '</td>'
							. "</tr>";
			}

			$output .= "<tbody></table>";
		}
		return $output;
	}

	function levelAction($csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value){

		$output = "";

		if ( ( $_POST["popis_new"] ) )
		{
			//budeme ukladat
			$popis = $this->conn_mysql->real_escape_string($_POST["popis_new"]);
			$level = intval($_POST["level_new"]);
	
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
				$sql = "INSERT INTO leveling (popis, level) VALUES ('$popis','$level')";
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
				<td width="25%"><label>Popis: </label></td>
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

	function tarifList()
	{

		$output = "";

		$update_id = $_GET["update_id"];
		$erase_id = $_GET["erase_id"];
		
		$output .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; font-size: 16px; \">
		  Nastavení tarifů";
		  
		if( isset($update_id) ){ $output .= "  - Úprava"; }
		elseif( isset($erase_id) ){ $output .= "  - Smazání"; }
		else{ $output .= "  - Výpis"; }
		
		$output .= "</div>";
		
		//kontrola promennych zde ...
		
		if( isset($update_id) )
		{
		  if( !( preg_match('/^([[:digit:]])+$/',$update_id) ) )
		  { $error .= "<div>Chyba! Update id není ve správném formátu. </div>"; }
		}
	  
		if( isset($erase_id) )
		{
		  if( !( preg_match('/^([[:digit:]])+$/',$erase_id) ) )
		  { $error .= "<div>Chyba! Erase id není ve správném formátu. </div>"; }
		}
		
		if( isset($update_id) )
		{
		  if( isset($send) )
		  {
		   //budeme ukladat ..
		   $output .= "budeme ukladat ... T.B.A.";
		  
		  }
		  else
		  {
			//zobrazeni formu pro update ...
			$output .= "zobrazeni formu pro update .... T.B.A.";
		  
		  }
		  
		} //konec if isset update_id
		elseif( isset($erase_id) )
		{
		  if( isset($send) )
		  {
		   //budeme ukladat ..
		   $output .= "budeme mazat ...";
		  
		  }
		  else
		  {
			//zobrazeni formu pro erase ...
			$output .= "zobrazeni formu pro erase ....";
		  
		  }
		  
		} //konec if isset erase_id
		else
		{
			//mod vypis ...
			
			$output .= "<table border=\"0\" width=\"1000px\" >";
			
			$style1 = "border-bottom: 2px solid black; border-right: 1px dashed gray; ";
			$style2 = "border-bottom: 1px solid gray; border-right: 1px dashed gray; ";
		
			$output .= "
			<tr>
				<td style=\"".$style1."\"><b>id tarifu</b></td>
				<td style=\"".$style1."\"><b>zkratka</b></td>
				<td style=\"".$style1."\"><b>název</b></td>
				<td style=\"".$style1."\"><b>typ</b></td>
				<td style=\"".$style1."\"><b>garant</b></td>
				
				<td style=\"".$style1."\"><b>cena bez DPH</b></td>
				<td style=\"".$style1."\"><b>cena s DPH</b></td>
				
				<td style=\"".$style1."\"><b>Rychlost<br> download</b></td>
				<td style=\"".$style1."\"><b>Rychlost<br> upload</b></td>
			
				<td style=\"".$style1."\"><b>Agregace</b></td>
				<td style=\"".$style1."\"><b>Agregace<br> smluvní</b></td>
			
				<td style=\"".$style1."\"><b>Počet <br>klientů</b></td>
				
				<td style=\"".$style1."\"><b>úprava</b></td>
				<td style=\"".$style1."\"><b>smazat</b></td>
		
			</tr>
			";
			
			$output .= "<tr><td colspan=\"14\" ><br></td></tr>";
		
			if( ( preg_match('/^([[:digit:]]+)$/',$_GET["id_tarifu"]) ) )
			{
				$id_tarifu = $_GET["id_tarifu"];
				
				try {
					$dotaz_tarify = $this->conn_mysql->query(" SELECT * FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ORDER BY id_tarifu");
					$dotaz_tarify_radku = $dotaz_tarify->num_rows;
				} catch (Exception $e) {
					die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
				}
		
			}
			else
			{
		
				try {
					$dotaz_tarify = $this->conn_mysql->query(" SELECT * FROM tarify_int ORDER BY id_tarifu");
					$dotaz_tarify_radku = $dotaz_tarify->num_rows;
				} catch (Exception $e) {
					die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
				}
			}
		
			if( $dotaz_tarify_radku == 0 )
			{
				$output .= "
				<tr>
					<td colspan=\"6\" >Žádné záznamy v databázi</td>
				</tr>
				";
			}
			else
			{
			
				while( $data = $dotaz_tarify->fetch_array() )
				{
					$output .= "
					<tr >
						<td style=\"".$style2."\" colspan=\"\" >".$data["id_tarifu"]."</td>
						<td style=\"".$style2."\" colspan=\"\" >".$data["zkratka_tarifu"]."</td>
						<td style=\"".$style2."\" colspan=\"\" >".$data["jmeno_tarifu"]."</td>
							
						<td style=\"".$style2."\" colspan=\"\" >";
						
						if ( $data["typ_tarifu"] == 0 )
						{ $output .= "wifi tarif"; }
						elseif ( $data["typ_tarifu"] == 1 )
						{ $output .= "optický tarif"; }
						else
						{ $output .= $data["typ_tarifu"]; }
						
						$output .= "</td>
						
						<td style=\"".$style2."\" colspan=\"\" >";
						
						if ( $data["garant"] == 1 )
						{ $output .= "Ano"; }
						elseif ( $data["garant"] == 0 )
						{ $output .= "Ne"; }
						else
						{ $output .= $data["garant"]; }
						
						$output .= "</td>
						
						<td style=\"".$style2."\" colspan=\"\" >".$data["cena_bez_dph"]."</td>
						<td style=\"".$style2."\" colspan=\"\" >".$data["cena_s_dph"]."</td>
				
						<td style=\"".$style2."\" colspan=\"\" >".$data["speed_dwn"]."</td>
						<td style=\"".$style2."\" colspan=\"\" >".$data["speed_upl"]."</td>
				
						<td style=\"".$style2."\" colspan=\"\" >".$data["agregace"]."</td>
						<td style=\"".$style2."\" colspan=\"\" >".$data["agregace_smlouva"]."</td>
						
						<td style=\"".$style2."\" colspan=\"\" >";
						
						//zjisteni poctu lidi
						$id_tarifu = $data["id_tarifu"];
						
						$dotaz_lidi = pg_query("SELECT * FROM objekty WHERE id_tarifu = '". intval($id_tarifu). "' ");
						$dotaz_lidi_radku = pg_num_rows($dotaz_lidi);
						
						$output .= $dotaz_lidi_radku;
						
						$output .= "</td>
						
						<td style=\"".$style2."\" colspan=\"\" >
						<a href=\"".$_SERVER["SCRIPT_URL"]."?update_id=".$data["id_tarifu"]."\" >upravit</a>
						</td>
						<td style=\"".$style2."\" colspan=\"\" >
						<a href=\"".$_SERVER["SCRIPT_URL"]."?erase_id=".$data["id_tarifu"]."\" >smazat</a>
						</td>
				
					</tr>
					"; 
				}
			
			} //konec else if radku == 1
		
			$output .= "</table>";
		
	   } // konec hlavniho else ..

	   return array($output);
	}

}
