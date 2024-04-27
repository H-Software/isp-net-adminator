<?
   	  
echo '<span style="padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; " >Internet  ::</span>

<span style="padding-left: 5px;" ><a href="objekty.php">objekty</a></span>

<span style="padding-left: 5px; ">( <!--<a href="objekty-add.php">-->přidání<!--</a>--></span>
,
<span style="padding-left: 5px; padding-right: 10px; ">omezený režim)</span>';

if( $back == true ){ echo "</span>"; }


 echo "<span style=\"padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; \" >IPTV ::</span>";

 echo "<span style=\"padding-left: 5px; padding-right: 10px; \" >
	<!--<a href=\"objekty-stb.php\">-->set-top-boxy<!--</a>--></span>";

 if( $back2 == true ){ echo "</span>"; }

?>

<span style="padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; " >VoIP ::</span>

<span style="padding-left: 5px;" >čísla</span>

