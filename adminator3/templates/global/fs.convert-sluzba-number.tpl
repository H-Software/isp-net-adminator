{if $tpl_sluzba_number eq '1'}
    <span style="color: green; font-weight: bold; " >Ano</span>
{elseif $tpl_sluzba_number eq '0' }
    <span style="color: #CC6666; " >Ne</span>
{else}
    <span>N/A ({$tpl_sluzba_number})</span>
{/if}
