{include file="base.tpl"}

  {include file="inc.cat.others.tpl"}

 <div style="padding-left: 170px; padding-top: 20px; " >
  
  {include file="others/inc.board-header.tpl"}
    
  {if $mod == 1 }    
    {include file="others/inc.board-listing.tpl"}
  {elseif $mod == 2}
    {include file="others/inc.board-writing.tpl"}
  {elseif $mod == 3}
    {include file="others/inc.board-saving.tpl"}
    
  {/if}
  
 </div>

{include file="base-end.tpl"}
