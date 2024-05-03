{include file="base.tpl"}

{include file="partner/inc.order.cat.tpl"}

<div style="margin: 5px; ">

{include file="partner/inc.order.add.banner.tpl"}

{if $form_error_message|count_characters > 1}
<div class="row">
    <div class="col-2"></div>
    <div class="col-8 alert alert-danger" role="alert" style="padding-top: 15px">
    {$form_error_message}
    </div>
    <div class="col-2"></div>
</div>
{/if}

{$body}

</div>

{include file="base-end.tpl"}
