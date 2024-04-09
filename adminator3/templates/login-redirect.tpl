{include file="inc.header.tpl"}
 
{include file="inc.head.tpl"}
 
{if $lp_on == 1}
 <meta http-equiv="refresh" content="2;url={$last_page}" >
{else}
 <meta http-equiv="refresh" content="2;url=/home">
{/if}
 
 <title>{$page_title}</title>

 </head>
 
<body>
 
 <p>Jste bezpečně přihlašováni do administračního systému ISP Adminator ...</p>
 <p>
 
 <form name="hours">
   <table border="0" cellpadding="0" cellspacing="0" width="700">
   
   <tr>
     <td width="232">Pokud nebudete přihlášeni do <b>
      <input type="text" size="22" name="time" style="display: none">
      <input type="text" size="20" name="elapsed" style="display: none">
      <input type="text" size="10" name="timetojump" style="width: 25px; text-align: right; border: 0px;">
			      
      </b> sekund ,klepněte 
      
      {if $lp_on == 1}
        <a href="{$last_page}" >sem</a>  
      {else}
        <a href="home.php" >sem</a>
      {/if}
    </td>
   </tr>
					    
  </table>
 </form>

 <script src="include/js/login_time.js"></script>

 </p>
  					       
{include file="inc.footer.tpl"}
