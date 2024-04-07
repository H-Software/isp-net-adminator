<?php

include ("include/config.php");
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,28) ) )
{
 // neni level

 include ("nolevelpage.php");
 // header("Location: ".$stranka);
 
 // echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
	  	  
}

?>	 

<frameset rows="155px,*">

    <frame name="top" src="soubory-top.php" scrolling="no" frameborder="0"/>
    <frame name="middle" src="files2/index.php" frameborder="0"/>

</frameset>


<noframes>
        <body>
	    <h1>UH?</h1>
	    <h4>Vas browser nepodporuje ramy (frames). A to je fakt divny :)</h4>
	</body>
</noframes>
