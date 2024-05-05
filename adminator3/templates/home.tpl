{include file="base.tpl"}

<div class="home-main" >

{* slim-flash messages *}

{if $flash_messages|default:'0' }
      {foreach $flash_messages as $m}
            {if $m@key eq 'info'}
                  {foreach $m as $i}
                        <div class="alert alert-info">
                        {$i}
                        </div>
                  {/foreach}
            {/if}
      {/foreach}
{/if}

{* vypis poslednich prihlasenych *}
{include file="partials/inc.home.list-logged-users.tpl"}

 {if $opravy_povoleno|default:'0' eq "1"}
 
    {include file="inc.opravy.tpl"}
 
 {/if}

 {* vlozeni modulu pro neuhr. faktury *}
 
 {include file="faktury/faktury-for-home.tpl"}

 <div>{$body|default:''}</div>

</div>

{if $nastenka_povoleno|default:'0' eq "1"}
      {include file="others/inc.board-header.tpl"}

      {include file="others/inc.board-listing.tpl"}
{/if}

{include file="base-end.tpl"}
