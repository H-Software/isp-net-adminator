
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

<title>{$page_title}</title>

<link href="/style.css" rel="stylesheet" type="text/css" >
<link href="/style-board.css" rel="stylesheet" type="text/css" >

<!-- Latest compiled and minified CSS -->
<!--
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">


{if $enable_calendar == 1}
 <script language="JavaScript" src="/plugins/tigra_calendar/calendar_eu.js"></script>
 <link rel="stylesheet" href="/plugins/tigra_calendar/calendar.css">
{/if}

{if $enable_work == 1}
 <script language="JavaScript" src="/include/js/work.js" ></script>
{/if}

{if $enable_fn_check == 1}
 <script language="JavaScript" src="/include/js/fn_check.js" ></script>
{/if}

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</head>

<body>

 <div id="obsah" >
  
  {*uvodni napis a obrazek, s ip a nickem .. *}
  {include file="inc.intro.banner.tpl"}

  <div style="width: 83%; float: left; " >

    {* uvodni kategorie *}
    {include file="inc.intro.category.tpl"}

  </div>
   
  {*zde vypis prihl. uziv. *}
  {include file="inc.intro.logged.users.tpl"}

  {if $show_se_cat eq "1"}
    <div class="cat-cara-oddelovaci" ></div>
  {/if}

  {if $subcat_select eq "1"}   
   <div class="subcat-main" >
       {$subkategorie}
   </div>
  {/if}
  
  <div class="obsah-main" >
   {* zde ZACATEK vlastniho obsahu ... *}
