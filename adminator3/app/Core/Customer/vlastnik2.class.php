<?php

use Psr\Container\ContainerInterface;

class vlastnik2 {
	var $conn_mysql;

	var $logger;

	var $listItemsContent;

	var $listMode; // original local variable "co"

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

		$list=$_GET["list"];

		// $delka_find_id=strlen($find_id);
		if( ( strlen($find_id) > 0 ) ) 
		{ $listMode=3; /* hledani podle id_cloveka */   $sql=intval($find_id);  }
		elseif( ( strlen($find) > 0 ) )
		{ $listMode=1;  /* hledani podle cehokoli */  $sql = $find;  }
		else
		{ /* cokoli dalsiho */ }

		if($listMode == 1)
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
		elseif($listMode == 3){
				
			$this->dotaz_source = "SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f, ".
					" to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f ".
					" FROM vlastnici WHERE ( archiv = 0 or archiv is null ) AND id_cloveka = '$sql' "; 
		}
		else
		{ 	
			$this->listItemsContent = '<div class="alert alert-warning" role="alert" style="margin-right: 10px" >zadejte výraz k vyhledání....</div>'; 	
		}

	}

	public function listItems()
	{
		$this->listPrepareVars();

		$vlastnik = new vlastnik2_a2;
		$vlastnik->level = $level;
		$vlastnik->conn_mysql = $this->conn_mysql;

		// without find search we dont do anything
		if(strlen($this->listItemsContent) > 0){
			return $this->listItemsContent;
		}

		$vlastnik->vypis_tab(1);

	}

} //konec tridy vlastnik2
