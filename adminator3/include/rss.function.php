<?

function check_login_rss($get_sid)
{

 if( !(ereg('^([[:alnum:]]|_|-)+$',$get_sid)) )
 {
    return false;
    //exit;
 }
 else
 {
  $pocet_vysl = 0;
  
  $MSQ_S = mysql_query("SELECT * FROM users");
  
  while( $data = mysql_fetch_array($MSQ_S) )
  {
    $login = $data["login"];
    $login_crypt = md5($login);
    
    if( $login_crypt == $get_sid)
    { $pocet_vysl++; }
  }

  if( $pocet_vysl == 1 )
  { return true; } 
  else
  { return false; }
 }
 
} //konec funkce check_login_rss

// exportuje posledních 20 článků jako RSS
function exportRSSS()
{
 putHeader();
 $q = mysql_query("SELECT * FROM board ORDER BY id DESC LIMIT 0,50");

 while ($row=mysql_fetch_object($q)) 
  putItem($row);

 putEnd();
}

// hlavička
function putHeader()
{
 // nastavení typu aplikace XML
 header ("Content-type: text/xml");
 echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?> \n ";
 
?>
<rss version="2.0">
<channel>
<title>Simelon Adminator3 :: Nástěnka 2.0 :: RSS 2.0</title>
<link>https://trinity.simelon.net/adminator3/</link>
<description>Administrační systém sítě SIMELON</description>
<language>cs</language>
<generator>Simelon Adminator3</generator>
<copyright>(c) SIMELON, s.r.o.</copyright>
<category>Simelon Networking</category>
<?php
}

// musíme odstranit XHTML tagy
function encode_xml($data)
{
 return strip_tags( str_replace(
   '</p>',
   "n",
   str_replace(
     '<br />',
     "n",
     $data)));
}

// zapsání jedné položky
function putItem($o) 
{
 // odstranění tagů..
 $itemtitle=encode_xml($o->subject);
 
 $itemauthor=encode_xml($o->author);
 
 $itembody=encode_xml($o->body);
 
 // $itembody = unhtmlentities($itembody);
 
 $itembody = Str_Replace("&","&amp;",$itembody);
 
 $itemlink='https://trinity.simelon.net/adminator3/others-board.php?item_id='.$o->id;
 
 // datum jako Sat, 15 May 2004 01:20:56 +0200
 $itempubdate = $o->from_date;

 $val = $itempubdate;
 $date = explode("-",$val);
// $time = explode(":",$val[1]);
   
 $itempubdate = mktime(0,0,0,$date[1],$date[2],$date[0]);    
 $itempubdate = gmdate('D, d M Y H:i:s', $itempubdate ).' GMT';
 
 echo "\n<item> \n";
 echo "<title>".$itemtitle." [".$itemauthor."]</title> \n";
 echo "<link>".$itemlink."</link> \n";
 echo "<description>".$itembody."</description> \n";
 echo "<pubDate>".$itempubdate."</pubDate> \n";
 echo "</item> \n";

}


// patička
function putEnd() 
{
 echo "\n</channel> \n";
 echo "</rss> \n";
}

function unhtmlentities ($string) 
{
  $trans_tbl = get_html_translation_table (HTML_ENTITIES);
  $trans_tbl = array_flip ($trans_tbl);
  return strtr ($string, $trans_tbl);
}

?>
