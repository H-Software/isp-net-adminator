{include file="base.tpl"}

<div class="home-main" >

{* vypis poslednich prihlasenych *}
{include file="inc.home.list-logged-users.tpl"}

 {if $opravy_povoleno eq "1"}
 
    {include file="inc.opravy.tpl"}
 
 {/if}

 {* vlozeni modulu pro neuhr. faktury *}
 
 {include file="faktury/faktury-for-home.tpl"}

 <div>{$body}</div>

</div>

{if $nastenka_povoleno eq "1"}
      {include file="others/inc.board-header.tpl"}

      {include file="others/inc.board-listing.tpl"}
{/if}

{include file="base-end.tpl"}
