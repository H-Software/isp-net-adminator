{include file="base.tpl"}

{include file="topology/inc.category.tpl"}

    <div class="global-cat-body2" >

    {if $p_bs_alerts|default:'0' }
        {include file="partials/bootstrap-alert-with-columns-array.tpl"}
    {/if}

    {$body}

    </div>

{include file="base-end.tpl"}
