
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

<title>{$page_title}</title>

<link href="/style.css" rel="stylesheet" type="text/css" >
<link href="/style-board.css" rel="stylesheet" type="text/css" >

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

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
