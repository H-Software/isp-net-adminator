
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

{if $lp_on == 1}
 <meta http-equiv="refresh" content="2;url=index.php?lp={$last_page}" >
{else}
  <meta http-equiv="refresh" content="2;url=index.php" >
{/if}

<title>{$page_title}</title>

</head>

<body>

<div style="text-align: center;" >

 <img alt="logo" src="img2/logo.png" border="0" >
 <br><br>
  <img alt="logo 2" src="img2/im-adm10b.jpg" width="400" >
 <br><br><br>

 <H2>Byl(a) jste odhlášen(a)!</H2>
      
 {if $lp_on == 1}
   <br><br>Přihlášení: <a href="index.php?lp={$last_page}">zde</a>
 {else}
   <br><br>Přihlášení: <a href="index.php">zde</a>
 {/if}
 
 <div style="color: grey;" ><br><br>debug info: <br> delka session: {$delka},
 vysledek mazani: {$rs_delete}</div>

 {$body}
 
</div>

{include file="inc.footer.tpl"}
