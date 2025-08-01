<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class board
{
    // private $container;

    public ?\PDO $pdoMysql;

    public \mysqli|\PDO $conn_mysql;

    public \PgSql\Connection|\PDO|null $conn_pgsql;

    public \Monolog\Logger $logger;

    protected $settings;

    protected $sentinel;

    protected $loggedUserEmail;

    public $what;
    public $action;
    public $page;

    public $send;
    public $sent;

    public $author;
    public $email;

    public $from_date;
    public $to_date;
    public $subject;
    public $body;

    public $error;
    public $view_number;
    public $sql;

    public $query_error;

    public $write; //jestli opravdu budem zapisovat, ci zobrazime form pro opraveni hodnot

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        $this->logger = $container->get('logger');
        $this->settings = $container->get('settings');
        $this->pdoMysql = $container->get('pdoMysql');
        $this->sentinel = $container->get('sentinel');

        $this->loggedUserEmail = $this->sentinel->getUser()->email;
    }

    public function load_vars(ServerRequestInterface $request)
    {
        foreach ($request->getQueryParams() as $i => $v) {
            if (preg_match('/^(what|action|page|send)$/', $i) and strlen($v) > 0) {
                $this->$i = $request->getQueryParams()[$i];
            }
        }

        foreach ($request->getParsedBody() as $i => $v) {
            if (preg_match('/^(sent|author|email|to_date|from_date|subject|body)$/', $i) and strlen($v) > 0) {
                $this->$i = $request->getParsedBody()[$i];
            }
        }
    }

    public function prepare_vars()
    {
        if (!isset($this->author)) {
            if (is_object($this->sentinel)) {
                $this->author = $this->sentinel->getUser()->email;
            }
        }

        if (((!isset($this->action)) and (!isset($this->send)))) {
            $this->action = "view"; //ještě není zinicializována proměnná $action
        }
        if (!isset($this->what)) {
            $this->what = "new"; //ještě není zinicializována proměnná $what
        }
        if (!isset($this->page)) {
            $this->page = 0; //ještě není zinicializována proměnná $page
        }

        return true;
    }

    public function show_messages(): array
    {
        $zpravy = array();

        if ($this->what == "new") {
            $this->sql = $this->settings['db']['driver'] === 'sqlite' ?
                " from_date <= date('now') AND to_date >= date('now') " :
                " from_date <= NOW() AND to_date >= NOW() ";
        } else {
            $this->sql = $this->settings['db']['driver'] === 'sqlite' ?
                " to_date < date(\"Y-m-s H:i:s\", time()) " :
                " to_date < NOW() ";
        }

        $sql_date1 = $this->settings['db']['driver'] === 'sqlite' ?
            'strftime("%d.%m.%Y", from_date) as from_date2' :
            'date_format(from_date, "%d.%m.%Y") as from_date2';

        $sql_date2 = $this->settings['db']['driver'] === 'sqlite' ?
            'strftime("%d.%m.%Y", to_date) as to_date2' :
            'date_format(to_date, "%d.%m.%Y") as to_date2';

        $sql_base = "SELECT *," . $sql_date1."," . $sql_date2;

        $start = $this->page * $this->view_number; //první zpráva, která se zobrazí

        $sql = $sql_base." FROM board WHERE ".$this->sql." ORDER BY id DESC LIMIT ".$start.",".$this->view_number;

        $this->logger->debug("board\show_messages: SQL dump: " . var_export($sql, true));

        try {
            $message = $this->pdoMysql->query($sql);
            // $this->query_error = "Board messages debug: <br>SQL DUMP: " . var_export($sql, true);

        } catch (Exception $e) {
            $this->logger->error("board\show_messages: db query failed! (Error: " . var_export($e->getMessage(), true) . ")");
            $this->query_error = "Board messages listing error! <br>db query failed: " . var_export($e->getMessage(), true);

            return $zpravy;
        }

        $zpravy = $message->fetchAll();

        return $zpravy;
    }

    public function show_pages()
    {
        //odkazy na starší zprávy (u právě zobrazené zprávy se odkaz nevytvoří)
        $stranek = array();

        try {
            $count = $this->pdoMysql->query("SELECT id FROM board WHERE ".$this->sql); //vybíráme zprávy
        } catch (Exception $e) {
            $this->logger->error("board\show_pages: Database query failed! Caught exception: " . $e->getMessage());
            return $stranek;
        }

        $count_num_rows = count($count->fetchAll());

        $page_count = ceil($count_num_rows / $this->view_number); //počet stran, na kterých se zprávy zobrazí

        for ($i = 0;$i < $page_count;$i++) {
            $stranek[] = array("what" => $this->what, "i" => $i, "i2" => ($i + 1), "i_akt" => $this->page);
        }

        return $stranek;
    }

    public function check_vars()
    {
        if (strlen($this->from_date) > 0) {
            // TODO: check date format
            list($from_day, $from_month, $from_year) = explode("-", $this->from_date);
            if (mktime(0, 0, 0, $from_month, $from_day, $from_year) < mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
                $this->error .= 'Datum OD nesmí být menší než dnešní datum.';
            }
        } else {
            $d = strtotime("today");
            $this->from_date = date("d-m-Y", $d);
        }

        if (strlen($this->to_date) > 0) {
            // TODO: check date format
            list($to_day, $to_month, $to_year) = explode("-", $this->to_date);
            if (mktime(0, 0, 0, $from_month, $from_day, $from_year) > mktime(0, 0, 0, $to_month, $to_day, $to_year)) { //zkontrolujeme data od-do
                $this->error .= 'Datum OD nesmí být větší než datum DO.';
            }
        } else {
            $d = strtotime("+7 Days");
            $this->to_date = date("d-m-Y", $d);
        }

        if ($this->author == "" || $this->subject == "" || $this->body == "") {  //byly vyplněny všechny povinné údaje?
            $this->error .= 'Musíte vyplnit všechny povinné údaje - označeny tučným písmem.';
        }

        if (strlen($this->error) < 1) {
            $this->write = true; //provedeme zápis
        }
    }

    public function convert_vars()
    {
        //odstraníme nebezpečné znaky
        $this->author = htmlspecialchars($this->author);
        $this->email = htmlspecialchars($this->email);
        $this->subject = htmlspecialchars($this->subject);

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": body lenght: " . strlen($this->body));

        $this->body = substr($this->body, 0, 1500);         //bereme pouze 1500 znaků
        $this->body = trim($this->body);                            //odstraníme mezery ze začátku a konce řetězce
        $this->body = htmlspecialchars($this->body);        //odstraníme nebezpečné znaky
        $this->body = str_replace("\r\n", " <BR> ", $this->body);    //nahradíme konce řádků na tagy <BR>

        //$body = wordwrap($body, 90, "\n", 1); //rozdělíme dlouhá slova

        //vytvoříme odkazy
        // TODO: fix zero-ing body variable
        // $this->body = preg_replace("/(http://[^ ]+\.[^ ]+)/i", " <a href=\1>\1</a>", $this->body);
        // $this->body = preg_replace("/[^/](www\.[^ ]+\.[^ ]+)/i", " <a href=http://\1>\1</a>", $this->body);

        //povolíme tyto tagy - <b> <u> <i>, možnost přidat další
        $tag = array("b", "u", "i");

        for ($y = 0;$y < count($tag);$y++):
            // TODO: fix zero-ing body variable
            // $this->body = preg_replace("/&lt;/i" . $tag[$y] . "&gt;", "<" . $tag[$y] . ">", $this->body);
            // $this->body = preg_replace("/&lt;\//i" . $tag[$y] . "&gt;", "</" . $tag[$y] . ">", $this->body);
        endfor;

        //prevedeni datumu
        list($from_day, $from_month, $from_year) = explode("-", $this->from_date);
        list($to_day, $to_month, $to_year) = explode("-", $this->to_date);

        $this->from_date = date("Y-m-d", mktime(0, 0, 0, $from_month, $from_day, $from_year)); //od
        $this->to_date = date("Y-m-d", mktime(0, 0, 0, $to_month, $to_day, $to_year));//do

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": body lenght: " . strlen($this->body));
    }

    public function insert_into_db()
    {
        try {
            $add = $this->pdoMysql->query(
                "INSERT INTO board (author, email, from_date, to_date, subject, body) "
                . "VALUES ('$this->author', '$this->email', '$this->from_date',
			'$this->to_date', '$this->subject', '$this->body')"
            );
        } catch (Exception $e) {
            $this->logger->error("board\\insert_into_db: query failed: Caught Exception: " . var_export($e->getMessage(), true));
            $this->error .= "Caught Exception: ". $e->getMessage();
            return false;
        }

        return $add;
    }
}
