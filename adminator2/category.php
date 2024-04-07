 <tr>
 
  
  <?php 
  
  // echo "<td style=\"background-IMAGE: url(".$cesta."img2/im-adm8.jpg); background-repeat: no-repeat; \" width=\"80%\" >"; 
  
   echo "<td style=\"\" width=\"80%\" >"; 
  ?>

  
    <table border="0" width="100%">
    <tr>
      <?php
       
      $uri=$_SERVER["REQUEST_URI"];
      $uri_replace = str_replace ("adminator2", "", $uri);
      
      if ( ereg("^.+vlastnici.+",$_SERVER["REQUEST_URI"]) or ereg("^.+vlastnici-cat.php+",$_SERVER["REQUEST_URI"]) or ereg("^.+vypovedi",$_SERVER["REQUEST_URI"]) )
        { echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo ' align="center" width="11%"><a class="cat" href="'.$cesta.'vlastnici-cat.php" target="_top">Zákazníci</a></td>'." \n";
      
      if ( ereg("^.+objekty.",$_SERVER["REQUEST_URI"]) or ereg("^.+objekty-subcat.php",$_SERVER["REQUEST_URI"]))
       { echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo ' align="center" width="11%"><a class="cat" href="'.$cesta.'objekty-subcat.php" target="_top">Služby</a></td>'." \n";
      
      if ( ereg("^.+platby.php$",$_SERVER["REQUEST_URI"]) or ereg("^.+platby-subcat.php$",$_SERVER["REQUEST_URI"]) 
      or ereg("^.+faktury.+$",$_SERVER["REQUEST_URI"]) or ereg("^.+fn.+$",$_SERVER["REQUEST_URI"]) )
        { echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo ' align="center" width="11%">  <a class="cat" href="'.$cesta.'platby-subcat.php" target="_top" >Platby</a> </td>'." \n";
     
      if ( ereg("^.+work.php$",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo ' align="center" width="11%"><a class="cat" href="'.$cesta.'work.php" target="_top">Work</a></td>'." \n";
       
      if ( ereg("^.+topology",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo ' align="center" width=""><a class="cat" href="'.$cesta.'topology-nod-list.php" target="_top" >Topologie</a></td>'." \n";
     
      if ( ereg("^.+admin.+$",$uri_replace ) or ereg("^.+admin-subcat.php$",$_SERVER["REQUEST_URI"]) )
        { echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo ' align="center" width=""><a class="cat" href="'.$cesta.'admin-subcat.php" target="_top" >Nastavení </a></td>'." \n";

      if ( ereg("^.+home.php$",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo ' align="center" width=""><a class="cat" href="'.$cesta.'home.php" target="_top" >Úvodní strana</a></td>'." \n";
    	
    echo "</tr> \n";
    
    echo "<tr> \n";

    if ( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo " align=\"center\" colspan=\"1\" width=\"20%\"> <a class=\"cat\" href=\"".$cesta."partner/partner-cat.php\" target=\"_top\" >Partner program </a></td> \n";

    if ( ereg("^.+archiv-zmen.+$",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo " align=\"center\" width=\"15%\" colspan=\"1\" ><a class=\"cat\" href=\"".$cesta."archiv-zmen-cat.php\" target=\"_top\" >Změny</a></td> \n";

    if ( ereg("soubory",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo " align=\"center\" width=\"15%\"> <a class=\"cat\" href=\"".$cesta."soubory.php\" target=\"_top\" >Správce souborů</a></td> \n";

    if ( ereg("^.+board.+$",$_SERVER["REQUEST_URI"]) ){ echo '<td bgcolor="silver" '; }  else { echo '<td '; }
       echo " align=\"center\" colspan=\"1\" width=\"\"> <a class=\"cat\" href=\"".$cesta."board-header.php\" target=\"_top\" >Nástěnka </a></td> \n";

    if ( ereg("^.+others.+$",$_SERVER["REQUEST_URI"]) or ereg("^.+syslog.+$",$_SERVER["REQUEST_URI"]) or ereg("^.+/mail.php$",$_SERVER["REQUEST_URI"]) 
    or ereg("^.+opravy.+$",$_SERVER["REQUEST_URI"]) )
    { echo '<td bgcolor="silver" '; }  else { echo '<td '; }
      echo " align=\"center\" colspan=\"1\" width=\"\"> <a class=\"cat\" href=\"".$cesta."others-subcat.php\" target=\"_top\" >Ostatní</a></td> \n";

    echo "<td><br></td>\n";
    
   if ( ereg("^.+map.+$",$_SERVER["REQUEST_URI"]) or ereg("^.+about.+$",$_SERVER["REQUEST_URI"]) or ereg("^.+map.+$",$_SERVER["REQUEST_URI"]))
   { echo '<td bgcolor="silver" '; }  
   else { echo '<td '; }
      
   echo " align=\"center\" colspan=\"1\" width=\"\" > 
      
      <a class=\"cat\" href=\"".$cesta."about.php\" target=\"_top\" >O programu</a>\n";
	   
   echo "</tr>";
    
    ?>
    <tr>
    <td colspan="7" height="15px" ><hr class="cara-kategorie"></td>
    </tr>
    
    <tr>
     <td align="center" ><div style="font-size: 12px; color: #666666; ">externí odkazy: </div></td>	
     
        <td align="center" ><a class="cat" href="https://pbs.dialtelecom.cz:8444/" target="_new">VoIP Dial PBS</a></td>
	
	<td align="center" width="10%">
	    <a class="cat" href="https://monitoring.simelon.net/" target="_new" >Monitoring / Cacti</a>
	</td>
	
	<td align="center" width="10%" colspan="1">
	
	    <table width="100%" border="0" ><tr><td><b> mail: </b></td>
	    <td><a class="cat" href="http://mail.simelon.net/" target="_new"> index</a></td>
	    <td><a class="cat" href="http://mail.simelon.net/padmin/" target="_new">admin </a></td></tr>
	    </table>
	    
	</td>
	
	<td align="center" colspan="1">
		<table border="0" width="100%"><tr>
		    <td><b>db:</b></td>
		    <td><a class="cat" href="https://trinity.simelon.net/phpmyadmin/" target="_new">mysql</a></td>
		    <td><a class="cat" href="https://trinity.simelon.net/phppgadmin" target="_new">pgsql</a></td>
		    </tr></table>
		    
	</td>
	
	<td align="center" >	     
	    <a class="cat" href="https://trinity.simelon.net" target="_blank">Admin panel</a>
	</td>
	<td align="center" ><a class="cat" href="https://trinity.simelon.net/adminator3/home.php" >Adminator3</a></td>
	
	<td> </td>
	<td></td>	
	
	
    </tr>
    
    </table>
   
   </td>
   
  
  <td align="left" > 
  
  <?php
  
  $MSQ_USER2 = $conn_mysql->query("SELECT * FROM autorizace");

  $MSQ_USER_COUNT = $MSQ_USER2->num_rows;

  echo "<div style=\"color: gray; \">přihlášení uživatelé: ( ".$MSQ_USER_COUNT." )</div>";
  
  // zde vypis prihlasenych useru
      
      //prvne vypisem prihlaseneho
      $MSQ_USER_NICK = $conn_mysql->query("SELECT nick, level FROM autorizace WHERE nick LIKE '".$conn_mysql->real_escape_string($nick)."' ");
      
      if ($MSQ_USER_NICK->num_rows <> 1){ echo "Chyba! Vyber nicku nelze provest."; }
      else
      {
        while ($data_user_nick = $MSQ_USER_NICK->fetch_array() )
        { echo "jméno:  <b>".$data_user_nick["nick"]."</b>, level: <b>".$data_user_nick["level"]."</b><br>"; }
      } // konec else

  // ted najilejeme prihlaseny lidi ( vsecky ) do pop-up okna
  $MSQ_USER2 = $conn_mysql->query("SELECT nick, level FROM autorizace");

  $MSQ_USER_COUNT=$MSQ_USER2->num_rows;
  
  if ( $MSQ_USER_COUNT < 1 ){ $obsah_pop_okna .= "Nikdo nepřihlášen. (divny)"; }
  else
  {
  
   while ($data_user2 = $MSQ_USER2->fetch_array())
   {     
     $obsah_pop_okna .= "jméno:  ".$data_user2["nick"].", level: ".$data_user2["level"].", "; 
   } //konec while  
  
  } // konec if
  
   echo "<div style=\"padding-bottom: 8px; padding-top: 15px; align: center; padding-left: ; \" >
     <a href=\"".$cesta."index.php?lo=true\" target=\"_top\" class=\"cat\" >Odhlásit se</a></div>";

    echo "<input type=\"button\" class=\"buttonalllogin\" name=\"OK2\" value=\"Zobrazit všechny přihlášené ";
    echo "\" onclick=\"showWindow2()\" >";
    
   include("include/js.include.1.php");
   
   // $windowtext2 = "Ostatní přihlášení uživatelé: ".'\n'.$obsah_pop_okna;
   $windowtext2 = $obsah_pop_okna;
   
   // velikost okna
   $windowdelka2 = 170;
   $windowpadding2 = 40;
   
   // pozice okna
   $windowtop2 = 150;
   $windowleft2 = 350;
   
   include("include/window-main2.php");
    
   echo '<div id="windowPlaceholder2"></div>';
    								   
 ?>
								     
  </td>
 </tr>
