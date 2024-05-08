{include file="base.tpl"}

{include file="partner/inc.order.cat.tpl"}

<div class="row mt-3 mb-1 ml-3">
    <div class="col2" style="font-size: 18px; padding-left: 20px;">
        Změna stavu připojení
    </div>
    <div class="col">
        {* <a href="/partner/order/add"> *}
        {* Vložení žádosti *}
        {* </a> *}
    </div>
    <div class="col-7"></div>
</div>

{if strlen($alert_type) gt 0}
    {include file="partials/bootstrap-alert-with-columns.tpl"}
{/if}

{include file="partials/bootstrap-table-window-header.tpl"}
{$body|default: ''}
{include file="partials/bootstrap-table-window-footer.tpl"}


{include file="base-end.tpl"}
