<?php

use Psr\Container\ContainerInterface;

class vlastnik2 {
	var $conn_mysql;

	var $logger;

	var $listItemsContent;

	var $listMode; // original local variable "co"

	var $listSql; // original local variable "sql"

	var $istFindId;

	var $dotaz_source;

	function __construct(ContainerInterface $container) {
		$this->conn_mysql = $container->connMysql;
		$this->logger = $container->logger;
	}

	private function listPrepareVars()
	{
		// TODO: fix perms for actions/links
		//
		// if( check_level($level,40) ) { 
		// 	$vlastnik->pridani_povoleno="true";
		// }
					
		// if( check_level($level,63) ){ 
		// 	$vlastnik->export_povolen="true"; 
		// }
		
		// // tafy generovani exportu
		// if( $vlastnik->export_povolen )
		// {     
		// 	$vlastnik->export();		
		// }

		//promena pro update objektu
		// if( check_level($level,29) ) { $update_povolen="true"; }
		// if( check_level($level,33) ) { $mazani_povoleno="true"; }
		// if( check_level($level,34) ) { $garant_akce="true"; }
		
		// // promeny pro mazani, zmenu vlastniku
		// if( check_level($level,45) ) { $vlastnici_erase_povolen="true"; }
		// if( check_level($level,30) ) { $vlastnici_update_povolen="true"; }
		
		// // odendani objektu od vlastnika
		// if( check_level($level,49) ) { $odendani_povoleno="true"; }

		$find_id = $_GET["find_id"];
		$find    = $_GET["find"];

		// $delka_find_id=strlen($find_id);
		if( ( strlen($find_id) > 0 ) ) 
		{ $this->listMode=3; /* hledani podle id_cloveka */   $sql=intval($find_id);  }
		elseif( ( strlen($find) > 0 ) )
		{ $this->listMode=1;  /* hledani podle cehokoli */  $sql = $find;  }
		else
		{ /* cokoli dalsiho */ }

		if($this->listMode == 1)
		{
			$sql="%".$sql."%";
			$select1 = " WHERE (firma is not NULL) AND ( archiv = 0 or archiv is null ) AND ";
			$select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
			$select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
			
			$select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
			$select2 .= " OR vs LIKE '$sql' ) ";
				 
			if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
			if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
			if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
			if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
			
			if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
			if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
			if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
			if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
		
			if ( $_GET["razeni"] == 1){ $select4=" order by id_cloveka "; }
			if ( $_GET["razeni"] == 3){ $select4=" order by jmeno "; }
			if ( $_GET["razeni"] == 4){ $select4=" order by prijmeni "; }
			if ( $_GET["razeni"] == 5){ $select4=" order by ulice "; }
			if ( $_GET["razeni"] == 6){ $select4=" order by mesto "; }
			if ( $_GET["razeni"] == 14){ $select4=" order by vs "; }
			if ( $_GET["razeni"] == 15){ $select4=" order by k_platbe "; }
							 
			if ( $_GET["razeni2"] == 1){ $select5=" ASC "; }
			if ( $_GET["razeni2"] == 2){ $select5=" DESC "; }
		
			if ( $_GET["fakt_skupina"] > 0){ $select6=" AND fakturacni_skupina_id = ".intval($_GET["fakt_skupina"])." "; }
												  
			if ( (strlen($select4) > 1 ) ){ $select4=$select4.$select5; }
											 
			$this->dotaz_source = " SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f,
							to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f 
						 FROM vlastnici ".$select1.$select2.$select3.$select6.$select4;
		}
		elseif($this->listMode == 3){
				
			$this->dotaz_source = "SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f, ".
					" to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f ".
					" FROM vlastnici WHERE ( archiv = 0 or archiv is null ) AND id_cloveka = '$sql' "; 
		}
		else
		{ 	
			$this->listItemsContent = '<div class="alert alert-warning" role="alert" style="margin-right: 10px" >zadejte výraz k vyhledání....</div>'; 	
		}

		$this->listFindId = $find_id;
		$this->listSql = $sql;
	}

	public function listItems()
	{
		$this->listPrepareVars();

		$vlastnik = new vlastnik2_a2;
		$vlastnik->conn_mysql = $this->conn_mysql;
		$vlastnik->logger = $this->logger;
		$vlastnik->echo = false;

		// without find search we dont do anything
		if(strlen($this->listItemsContent) > 0){
			return $this->listItemsContent;
		}

		$this->listItemsContent .= '<div class="vlastnici2-table" style="padding-right: 5px; ">';
		$this->listItemsContent .= $vlastnik->vypis_tab(1);

		$poradek="find=".$find."&find_id=".$this->listFindId."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".
					$_GET["razeni"]."&razeni2=".$_GET["razeni2"]."&fakt_skupina=".$_GET["fakt_skupina"];

		if(strlen($_GET["list"]) > 0){
			$list = intval($_GET["list"]);
		}

		$listovani = new c_listing_vlastnici2("/vlastnici2?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $this->dotaz_source);

		if(($list == "")||($list == "1")){    //pokud není list zadán nebo je první
			$bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
		}
		else{
			$bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
		}

		$interval = $listovani->interval;

		if(intval($interval) > 0 and intval($bude_chybet) > 0){
			$dotaz_final = $this->dotaz_source . " LIMIT " . intval($interval) . " OFFSET " . intval($bude_chybet) . " ";
		}
		else{
			$dotaz_final = $this->dotaz_source;
		}

		$this->logger->debug("vlastnik2\listItems: dump dotaz_final: " . var_export($dotaz_final, true));

		//	  $listovani->listInterval();
		$this->listItemsContent .= $listovani->listPart(false);

		$this->listItemsContent .= $vlastnik->vypis($this->listSql,$this->listMode,$dotaz_final);

		$this->listItemsContent .= $vlastnik->vypis_tab(2);
		$this->listItemsContent .= '</div>';

		$this->listItemsContent .= $listovani->listPart(false);

		return $this->listItemsContent;
	}

} //konec tridy vlastnik2
