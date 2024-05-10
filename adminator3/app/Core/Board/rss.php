<?php

namespace App\Board;

use Psr\Container\ContainerInterface;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;

class boardRss
{
    public $conn_mysql;

    public $logger;

    // private $loggedUserEmail;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->logger = $container->get('logger');

        // $this->loggedUserEmail = Sentinel::getUser()->email;
    }

    public function check_login_rss($get_sid)
    {

        if(!(preg_match('/^([[:alnum:]]|_|-)+$/', $get_sid))) {
            return false;
            //exit;
        } else {
            $pocet_vysl = 0;

            try {
                $MSQ_S = $this->conn_mysql->query("SELECT * FROM users");
            } catch (Exception $e) {
                $this->logger->error("rss\check_login_rss mysql_query MSQ_S failed! Caught exception: " . $e->getMessage());
                return false;
            }

            while($data = $MSQ_S->fetch_array()) {
                $login = $data["login"];
                $login_crypt = md5($login);

                if($login_crypt == $get_sid) {
                    $pocet_vysl++;
                }
            }

            if($pocet_vysl == 1) {
                return true;
            } else {
                return false;
            }
        }

    } //konec funkce check_login_rss

    // exportuje posledních 20 článků jako RSS
    public function exportRSS(): false|string
    {
        $output = $this->putHeader();

        try {
            $q = $this->conn_mysql->query("SELECT * FROM board ORDER BY id DESC LIMIT 0,50");
        } catch (Exception $e) {
            $this->logger->error("rss\\exportRSS mysql_query q failed! Caught exception: " . $e->getMessage());
            return false;
        }

        while ($row = $q->fetch_object()) {
            $output .= $this->putItem($row);
        }

        $output .= $this->putEnd();

        return $output;
    }

    // hlavička
    public function putHeader(): string
    {
        $output = "";
        // nastavení typu aplikace XML
        // header("Content-type: text/xml");
        $output .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?> \n ";

        $output .=  '
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

        return $output;
    }

    // musíme odstranit XHTML tagy
    public function encode_xml($data)
    {
        return strip_tags(
            str_replace(
                '</p>',
                "n",
                str_replace(
                    '<br />',
                    "n",
                    $data
                )
            )
        );
    }

    // zapsání jedné položky
    public function putItem($o): string
    {
        $output = "";

        // odstranění tagů..
        $itemtitle = $this->encode_xml($o->subject);

        $itemauthor = $this->encode_xml($o->author);

        $itembody = $this->encode_xml($o->body);

        // $itembody = unhtmlentities($itembody);

        $itembody = Str_Replace("&", "&amp;", $itembody);

        $itemlink = 'http://' . $_SERVER['HTTP_HOST'] . '/others/board?item_id='.$o->id;

        // datum jako Sat, 15 May 2004 01:20:56 +0200
        $itempubdate = $o->from_date;

        $val = $itempubdate;
        $date = explode("-", $val);
        // $time = explode(":",$val[1]);

        $itempubdate = mktime(0, 0, 0, intval($date[1]), intval($date[2]), intval($date[0]));
        $itempubdate = gmdate('D, d M Y H:i:s', $itempubdate).' GMT';

        $output .= "\n<item> \n";
        $output .= "<title>".$itemtitle." [".$itemauthor."]</title> \n";
        $output .= "<link>".$itemlink."</link> \n";
        $output .= "<description>".$itembody."</description> \n";
        $output .= "<pubDate>".$itempubdate."</pubDate> \n";
        $output .= "</item> \n";

        return $output;
    }


    // patička
    public function putEnd(): string
    {
        return "\n</channel> \n"
                . "</rss> \n";
    }

    public function unhtmlentities($string)
    {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }
}
