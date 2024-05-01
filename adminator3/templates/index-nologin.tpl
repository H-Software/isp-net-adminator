
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

<title>{$page_title}</title>

</head>

<body>

<p><H3>Nelze obnovit přihlášení do Administračního systému ISP Adminator ! </H3></p>

<p>To může být z několika důvodů:</p>


<ul>
        <li><i><b>Byl(a) jste dlouhou dobu v nečinosti</b></i></li>

        <p>Pokud nějakou dobu v systém nepracujete, dojde na serveru k odhlášení,
  ale na klientském pc zůstanou identifikační údaje</p>
  <p><b>Náprava: </b>Prosím proveďte odhlášení <a href="/index.php?lo=true">zde</a> a poté se znovu přihlašte.</p>
  <div style="color: grey">
  <p><i>Předcházení problému:</i> Pokud v administračním systému
  delší dobu nebudete pracovat, odhlašte se z něj.</p>
        </div>

        <br>
        <li><i><b>Zavřel(a) jste okno prohlížeče bez odhlášení ze systému</i></b></li>

        <p>Pokud zavřete okno prohlížeče bez kliknutí na odkaz odhlášení, dojde na klientském pc
  k odhlášení ale na serveru zůstaly informace o přihlášení.</p>
  <p><b>Náprava: </b>Prosím proveďte re-login ( znovu se přihlašte) <a href="/relogin.php">zde</a>.</p>
        <div style="color: grey">
  <p><i>Předcházení problému:</i> Pro správné a bezpečné odhlášení z administračního systému je
  třeba nejdříve kliknout na "Odhlásit" v hlavním okně a poté zavřít okno prohlížeče.
  </p>
        </div>

        <br>
        <li><i><b>Systém vyhodnotil akci jako "Pokus o HACKING"</i></b></li>

  <p>Adminitrační systém vyhodnotil Vaší poslední akci jako neoprávněný pokus o průnik do systému.</p>

        <div style="color: grey">
   <p>Tip: Pokud nemáte v systému dostatečná práva nestažne se je obejít :)</p>
           </div>


</ul>

<b>Zpráva od systému:</b> {$body}

{include file="inc.footer.tpl"}
