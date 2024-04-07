<?

 echo "<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
   <tr><td class=\"tableheading\">";

 //zobrazujeme aktuální nebo staré zprávy
       echo "->> Aktuální zprávy";
       $sql = "from_date <= NOW() AND to_date >= NOW()";

 echo "<hr width=\"100%\" size=\"1\" color=\"#7D7642\" noshade>
     </td></tr>
      </table>";

 $view_number = 10; //zprávy budou zobrazeny po ...
 $start = $page*$view_number; //první zpráva, která se zobrazí
 $message = mysql_query("SELECT * FROM board WHERE $sql ORDER BY id DESC LIMIT $start,$view_number") or die($query_error); //vybíráme zprávy - seřazeno podle id

 //vypíšeme tabulky se zprávami
 while ($entry = mysql_fetch_array($message)):
 
    echo ' <table width="600" border="0" cellspacing="0" cellpadding="1" align="center"><tr><td class="tableheading">';
    
    echo "zpráva č. " . $entry["id"];
    
    echo '</td></tr></table>
        <table width="600" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#7D7642"><tr><td>
         <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="#eaead7">
          <tr>
                <td class="table">';
        
                 $from = explode("-", $entry["from_date"]); //od
                 $to = explode("-", $entry["to_date"]); //do

                 if ($entry["email"]!="") echo '<a href="mailto:' . $entry["email"] . '">'; //zadal autor svůj email
                 echo "<b>" . $entry["author"] . "</b>"; //jméno
                 if ($entry["email"]!="") echo '</a>';
                 echo "<br>";
                 echo "<b>" . $entry["subject"] . "</b>" . " [". $from[2] . ". " . $from[1] . ". " . $from[0] . " - " . $to[2] . ". " . $to[1] . ". " . $to[0] . "]"; //předmět [od - do]
                 echo "<br><br>";
                 echo $entry["body"]; //zpráva
                 
        echo ' </td>
          </tr>
         </table>
        </table><br>';
 
 endwhile;

 echo '<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td align="right" class="table">
        <hr width="100%" size="1" color="#7D7642" noshade>
        <b>strana';
        
        //odkazy na starší zprávy (u právě zobrazené zprávy se odkaz nevytvoří)
        $count = mysql_query("SELECT id FROM board WHERE $sql") or die($query_error); //vybíráme zprávy
        $page_count = ceil(mysql_num_rows($count)/$view_number); //počet stran, na kterých se zprávy zobrazí
        for($i=0;$i<$page_count;$i++):
                echo " | ";
                if($page!=$i) echo '<a href="board-main.php?action=view&what=' . $what . '&page=' . $i . '">';
                echo ($i+1);
                if($page!=$i) echo '</a> ';
        endfor;
        //MySQL_Close(); //zavřeme databázi
        
  echo '|</b>
  </td></tr>
 </table>';

?>
