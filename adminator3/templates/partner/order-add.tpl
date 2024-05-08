{include file="base.tpl"}

{include file="partner/inc.order.cat.tpl"}

<div style="margin: 5px; ">

{include file="partner/inc.order.add.banner.tpl"}

{if $alert_type|count_characters > 1}
    {include file="partials/bootstrap-alert-with-columns.tpl"}
{/if}

<div class="row justify-content-center">
<div class="col-8 pb-2 fs-5 alert alert-primary" role="alert">
    Vložené data
</div>
</div>

{foreach $insertedData as $item}
<div class="row justify-content-center">
    <div class="col-4">
    {$item@key}
    </div>

    <div class="col-4">
    {$item}
    </div>
</div>

{/foreach}

</div>

{include file="base-end.tpl"}
