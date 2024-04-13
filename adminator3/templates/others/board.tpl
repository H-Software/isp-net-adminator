{include file="base.tpl"}

{include file="inc.cat.others.tpl"}

{include file="base-end.tpl"}

 <div class="container-sm justify-content-md-center">
      {include file="others/inc.board-header.tpl"}
        
      {if $mod == 1 }    
        {include file="others/inc.board-listing.tpl"}
      {elseif $mod == 2}
        {include file="others/inc.board-writing.tpl"}
      {elseif $mod == 3}
        {include file="others/inc.board-saving.tpl"}
      {/if}
</div>
