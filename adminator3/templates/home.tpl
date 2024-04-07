{include file="base.tpl"}

<div class="home-main" >

{* vypis poslednich prihlasenych *}
 <div class="home-vypis-useru-napis" >Poslední přihlašení uživatelé: </div>

 {section name="users" loop=$logged_users}

   <div class="home-vypis-useru-main" >
     uživatel: <span class="home-vypis-useru-font1" >{$logged_users[users].nick}</span>, 
     přihlášen dne: <span class="home-vypis-useru-font2" >{$logged_users[users].datum}</span>
     , z ip adresy: <span class="home-vypis-useru-font2" >{$logged_users[users].ip}</span>
   </div>
   
 {* *}
 {sectionelse}
     {* Tato cast se provede v pripade prazdneho pole *}
						 
  <div class="" >Chyba! Žádní přihlášení uživatelé ..</div>
						 
 {/section}

 {if $opravy_povoleno eq "1"}
 
    {include file="inc.opravy.tpl"}
 
 {/if}

 {* vlozeni modulu pro neuhr. faktury *}
 
 {include file="faktury/faktury-for-home.tpl"}

 {if $nastenka_povoleno eq "1"}
 
    {include file="others/inc.board-header.tpl"}

    {include file="others/inc.board-listing.tpl"}

 {/if}
 
 <div>{$body}</div>

</div>

{include file="base-end.tpl"}
