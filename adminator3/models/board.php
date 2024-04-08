<?php

class board
{
    var $what;
    var $action;
    var $page;
    
    var $send;
    var $sent;
    
    var $author; 
    var $email;
    
    var $from_date;
    var $to_date;
    var $subject;
    var $body;
    
    var $error;
    var $view_number;
    var $sql;
    
    var $write; //jestli opravdu budem zapisovat, ci zobrazime form pro opraveni hodnot
    
    function prepare_vars($nick)
    {
      if( !isset($this->author) )
      { $this->author=$nick; }
      
      if ( ( (!isset($this->action)) and (!isset($this->send)) ) ) $this->action = "view"; //ještě není zinicializována proměnná $action
      if (!isset($this->what)) $this->what = "new"; //ještě není zinicializována proměnná $what
      if (!isset($this->page)) $this->page = 0; //ještě není zinicializována proměnná $page
	  
      return true;
    }
    
    function show_messages()
    {
 
	if( $this->what == "new" )
	{ $this->sql = " from_date <= NOW() AND to_date >= NOW() "; }
	else
	{ $this->sql = " to_date < NOW() "; }

	$sql_base = "SELECT *,DATE_FORMAT(from_date, '%d.%m.%Y') as from_date2";
	$sql_base .= ",DATE_FORMAT(to_date, '%d.%m.%Y') as to_date2 ";


	$start = $this->page*$this->view_number; //první zpráva, která se zobrazí
	$message = mysql_query($sql_base." FROM board WHERE ".$this->sql." ORDER BY id DESC LIMIT ".$start.",".$this->view_number);
	//    or die($this->query_error.", sql: "); //vybíráme zprávy - seřazeno podle id

	//vypíšeme tabulky se zprávami
	while($entry = mysql_fetch_array($message))
	{
	    $zpravy[] = array("id" => $entry["id"],"author" => $entry["author"],
                        "email" => $entry["email"], "subject" => $entry["subject"],
                        "body" => $entry["body"], "from_date" => $entry["from_date2"],
                        "to_date" => $entry["to_date2"] );
	}
        
	return $zpravy;
	
    } //konec funkce show_messages
    
    function show_pages()
    {
      //odkazy na starší zprávy (u právě zobrazené zprávy se odkaz nevytvoří)
      $count = mysql_query("SELECT id FROM board WHERE ".$this->sql); //vybíráme zprávy
      $page_count = ceil(mysql_num_rows($count)/$this->view_number); //počet stran, na kterých se zprávy zobrazí
	   
      $stranek = array();
  
      for($i=0;$i<$page_count;$i++)
      {
         $stranek[] = array("what" => $this->what, "i" => $i, "i2" => ($i+1), "i_akt" => $this->page);
      }

      return $stranek;			   
    
    } //konec funkce show_pages

    function check_vars()
    {
        list($from_day, $from_month, $from_year) = explode("-",$this->from_date);
	list($to_day, $to_month, $to_year) = explode("-",$this->to_date);
		  
	//byl odeslán formulář?
	if($this->author=="" || $this->subject=="" || $this->body==""):  //byly vyplněny všechny povinné údaje?
	     $this->error .= 'Musíte vyplnit všechny povinné údaje - označeny tučným písmem.';
	elseif(mktime(0,0,0,$from_month,$from_day,$from_year) > mktime(0,0,0,$to_month,$to_day,$to_year)): //zkontrolujeme data od-do
	     $this->error .= 'Datum OD nesmí být větší než datum DO.';
	elseif(mktime(0,0,0,$from_month,$from_day,$from_year) < mktime(0,0,0, date("m"), date("d"), date("Y"))):
	     $this->error .= 'Datum OD nesmí být menší než dnešní datum.';
	else:
	     $this->write = true; //provedeme zápis
	endif;
	
    } //konec funkce check_vars
    
    function convert_vars()
    {
	//odstraníme nebezpečné znaky
        $this->author = htmlspecialchars($this->author);
	$this->email = htmlspecialchars($this->email);
	$this->subject = htmlspecialchars($this->subject);
		 
	$this->body = substr($this->body, 0, 1500);         //bereme pouze 1500 znaků
	$this->body = trim($this->body);                            //odstraníme mezery ze začátku a konce řetězce
	$this->body = htmlspecialchars($this->body);        //odstraníme nebezpečné znaky
	$this->body = str_replace("\r\n"," <BR> ", $this->body);    //nahradíme konce řádků na tagy <BR>
				 
	//$body = wordwrap($body, 90, "\n", 1); //rozdělíme dlouhá slova
	     
	//vytvoříme odkazy
	$this->body = eregi_replace("(http://[^ ]+\.[^ ]+)", " <a href=\\1>\\1</a>", $this->body);
	$this->body = eregi_replace("[^/](www\.[^ ]+\.[^ ]+)", " <a href=http://\\1>\\1</a>", $this->body);
						 
	//povolíme tyto tagy - <b> <u> <i>, možnost přidat další
	$tag = array("b", "u", "i");
	
	for($y=0;$y<count($tag);$y++):
	    $this->body = eregi_replace("&lt;" . $tag[$y] . "&gt;", "<" . $tag[$y] . ">", $this->body);
	    $this->body = eregi_replace("&lt;/" . $tag[$y] . "&gt;", "</" . $tag[$y] . ">", $this->body);
	endfor;

	//prevedeni datumu
        list($from_day, $from_month, $from_year) = explode("-",$this->from_date);
	list($to_day, $to_month, $to_year) = explode("-",$this->to_date);
    									 
	$this->from_date = date("Y-m-d", mktime(0,0,0,$from_month,$from_day,$from_year)); //od
	$this->to_date = date("Y-m-d", mktime(0,0,0,$to_month,$to_day,$to_year));//do	
										 
    
    } //konec funkce convert_vars
    
    function insert_into_db()
    {
	$add = mysql_query("INSERT INTO board VALUES ('', '$this->author', '$this->email', '$this->from_date',
				 '$this->to_date', '$this->subject', '$this->body')");
    
	
	if( $add == 1 )
	{ return $add; }
	else
	{
	  $this->error .= "<div>Došlo k chybě při zpracování SQL dotazu v databázi!</div>";	  
          // $this->error .= mysql_error();
	  
	  return $add;
	}
    }
    
} //konec tridy opravy
