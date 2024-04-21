<?

// if ( ereg("objekty.php",$_SERVER["REQUEST_URI"]) 
//     or ereg("objekty-add.php",$_SERVER["REQUEST_URI"])
//     or ereg("objekty-lite.php",$_SERVER["REQUEST_URI"])
//     )
//    { echo '<span style="background-color: #a2bfc0; " >'; $back = true; }
   	  
echo '<span style="padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; " >Internet  ::</span>

<span style="padding-left: 5px;" ><a href="objekty.php">objekty</a></span>

<span style="padding-left: 5px; ">( <a href="objekty-add.php">přidání</a></span>
,
<span style="padding-left: 5px; padding-right: 10px; ">omezený režim)</span>';

if( $back == true ){ echo "</span>"; }

// if ( ereg("objekty-stb.php",$_SERVER["REQUEST_URI"]) 
//     or ereg("objekty-stb-add.php",$_SERVER["REQUEST_URI"])
// //    or ereg("objekty-lite.php",$_SERVER["REQUEST_URI"])
//     )
//    { echo '<span style="background-color: #a2bfc0; " >'; $back2 = true; }

 echo "<span style=\"padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; \" >IPTV ::</span>";

 echo "<span style=\"padding-left: 5px; padding-right: 10px; \" >
	<!--<a href=\"objekty-stb.php\">-->set-top-boxy<!--</a>--></span>";

 if( $back2 == true ){ echo "</span>"; }

?>

<span style="padding-left: 10px; font-family: georgia; font-style: italic; 
    font-weight: bold; font-size: 16px; " >VoIP ::</span>

<span style="padding-left: 5px;" >čísla</span>

