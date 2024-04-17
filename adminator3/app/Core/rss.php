<?php

class rss_wrong_login
{
    var $subject,$body, $author;

    function rss_wrong_login($subject,$body,$author) 
    {
      $this->subject = $subject;
      $this->body = $body;
      $this->author = $author;
    }
		
}

class rss 
{

    var $conn_mysql;

    var $logger;

    function __construct($conn_mysql, $logger) {
        $this->conn_mysql = $conn_mysql;
        $this->logger = $logger;
    }
    
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
            
            try {
                $MSQ_S = $this->conn_mysql->query("SELECT * FROM users");
            } catch (Exception $e) {
                $this->logger->addError("rss\check_login_rss mysql_query MSQ_S failed! Caught exception: " . $e->getMessage());
                return false;
            }

            while( $data = $MSQ_S->fetch_array() )
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
    function exportRSS()
    {
        $this->putHeader();

        try {
            $q = $this->conn_mysql->query("SELECT * FROM board ORDER BY id DESC LIMIT 0,50");
        } catch (Exception $e) {
            $this->logger->addError("rss\\exportRSS mysql_query q failed! Caught exception: " . $e->getMessage());
            return false;
        }

        while ($row=$q->fetch_object()) 
            $this->putItem($row);

        $this->putEnd();
    }

    // hlavička
    function putHeader()
    {
        // nastavení typu aplikace XML
        header ("Content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?> \n ";
        
        echo '
        <rss version="2.0">
        <channel>
        <title>ISP Adminator3 :: Nástěnka 2.0 :: RSS 2.0</title>
        <link>https://adminator.local.net/</link>
        <description>Administrační systém ISP Adminator</description>
        <language>cs</language>
        <generator>Adminator3</generator>
        <copyright>(c) Patrik Majer</copyright>
        <category>Networking</category>
        ';
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
        $itemtitle=$this->encode_xml($o->subject);
        
        $itemauthor=$this->encode_xml($o->author);
        
        $itembody=$this->encode_xml($o->body);
        
        // $itembody = unhtmlentities($itembody);
        
        $itembody = Str_Replace("&","&amp;",$itembody);
        
        $itemlink='http://' . $_SERVER['HTTP_HOST'] . '/others-board.php?item_id='.$o->id;
        
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
}
