<?php

class vlastnici2pridani
{
    
    public static function checknick ($nick2)
    {
	global $fail, $error;
	
        $nick_check=preg_match('/^([[:alnum:]]|_|-)+$/',$nick2);
	if( !($nick_check) ) {
	    $fail="true";    
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick2.") není ve správnem formátu!!! (Povoleno alfanumerické znaky, dolní podtržítko, pomlčka)</H4></div>";
	}
	
	if( (strlen($nick2) > 20) ) {
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick2.") je moc dlouhý! (Maximální délka je 20 znaků)</H4></div>";	        
	}
				
    } // konec funkce check nick

    public static function checkvs ($vs)
    {
		$vs_check=preg_match('/^([[:digit:]]+)$/',$vs);
		if( !($vs_check) )
		{
			global $fail;      $fail="true";
			global $error;
			$error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Variabilní symbol ( ".$vs." ) není ve správnem formátu!!! (Pouze čísla)</H4></div>";
		}
    } // konec funkce check vs															    

    public static function check_k_platbe ($k_platbe)
    {
		$platba_check=preg_match('/^([[:digit:]]|\.)+$/',$k_platbe);
	   
		if ( !($platba_check) )
		{
			global $fail;      $fail="true";
			global $error;
			$error .= "<div class=\"vlasnici-add-fail-nick\"><H4>K_platbe ( ".$k_platbe." ) není ve správnem formátu !!! </H4></div>";
		}
	
    } // konec funkce check rra    

    public static function check_uc_index($ucetni_index)
    {
	   $ui_check=preg_match('^([[:digit:]]|\.)+$',$ucetni_index);
	   
	   if( !($ui_check) )
	   {
	         global $fail;      
		 $fail="true";
	         global $error;
	         $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) není ve správnem formátu (Povoleny pouze čísla)!!! </H4></div>";
	   }
    
	   $ui_check2 = strlen($ucetni_index);
	     
	   if( $ui_check2 > 5 )
	   {
	         global $fail;      
		 $fail="true";
	         global $error;
	         $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) překračuje povolenou délku (5 znaků) !!! </H4></div>";
	   }
    
    } //konec funkce check_uc_index
    
    public static function check_splatnost($number)
    {
    	if ( !(preg_match('/^([[:digit:]])+$/',$number)) )
	{
	    global $fail;      $fail="true";
	    global $error;
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Splatnost (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
	}
    
    } //end of function check_splatnost

    public static function check_icq($number)
    {
    	if ( !(preg_match('/^([[:digit:]])+$/',$number)) )
		{
			global $fail;      $fail="true";
			global $error;
			$error .= "<div class=\"vlasnici-add-fail-nick\"><H4>ICQ (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
		}
    
    } //end of function check_icq
    
    public static function check_email($email)
    {
    	if ( !(Aglobal::check_email($email)) )
		{
			global $fail;      $fail="true";
			global $error;
			$error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Emailová adresa (".$email.") není ve správnem formátu!</H4></div>";
		}
    } //end of function check_icq
    
    function check_tel($number)
    {
	global $fail, $error;
	
    	if( !(ereg('^([[:digit:]])+$',$number)) )
	{
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Telefon (".$number.") není ve správnem formátu! (pouze číslice)</H4></div>";
	}
    
	if( strlen($number) <> 9 ){
	
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole Telefon (".$number.") musí obsahovat 9 číslic!</H4></div>";
	}
    } //end of function check_tel
    
    public static function check_datum($date, $desc)
    {
		global $fail, $error;
		
		$a_date = explode('.', $date);
			
		$day =   intval($a_date["0"]);
		$month = intval($a_date["1"]);
		$year =  intval($a_date["2"]);
		
		if( !checkdate($month,$day,$year) )
		{
			$fail="true";
			$error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Datum ".$desc." (".$date.") není ve správném formátu! (dd.mm.rrrr)</H4></div>";
		}
	
    } //end of function check_datum
    
    function check_b_reason($reason)
    {
    	if( (strlen($reason) > 30) )
	{
	    global $fail, $error;
	          
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole \"Důvod pozastavení\" je moc dlouhé! Maximální počet je 30 znaků.</H4></div>";
	}
    
    } //end of function check_b_reason
    
}
