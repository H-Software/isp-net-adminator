
{include file="inc.header.tpl"}

{include file="inc.head.tpl"}

<title>{$page_title}</title>

<link href="/public/css/style.css" rel="stylesheet" type="text/css" >

<link href="/public/css/style-board.css" rel="stylesheet" type="text/css" >

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.4/dist/bootstrap-table.min.css">

{if $enable_calendar|default:'0' == 1}
 <script language="JavaScript" src="/plugins/tigra_calendar/calendar_eu.js"></script>
 <link rel="stylesheet" href="/plugins/tigra_calendar/calendar.css">
{/if}

{if $enable_calendar2|default:'0' == 1}
  <link rel="stylesheet" type="text/css" href="/plugins/tigra_calendar/tcal.css" />
  <script type="text/javascript" src="/plugins/tigra_calendar/tcal.js"></script>
 <script type="text/javascript" src="/plugins/tigra_calendar/custom-a2-vlastnici2-change.js"></script>
{/if}

{* <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> *}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script src="https://unpkg.com/bootstrap-table@1.22.4/dist/bootstrap-table.min.js"></script>

<script language="JavaScript" src="/public/js/adminator.js" ></script>

</head>

<body>

<div class="container-sm justify-content-md-center">
    <div class="row">
        <div class="col"></div>
        <div class="col-{$bs_layout_main_col_count|default:'10'}">

          <div id="obsah" >
<!-- start of obsah -->

          {*uvodni napis a obrazek, s ip a nickem .. *}
          {include file="inc.intro.banner.tpl"}

          <div style="width: 83%; float: left; " >
            {* uvodni kategorie *}
            {include file="inc.intro.category.tpl"}
          </div>

          {*zde akce prihl. uziv. *}
          {include file="partials/logged.user.actions.tpl"}

          {if $show_se_cat|default: '0' eq "1"}
            <div class="cat-cara-oddelovaci" ></div>
          {/if}

          <div class="obsah-main" >
<!-- start of obsah-main -->
