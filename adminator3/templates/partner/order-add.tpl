{include file="base.tpl"}

{include file="partner/inc.order.cat.tpl"}

<div style="margin: 5px; ">

<div class="row mt-3 mb-3 ml-3">
    <div class="col " style="font-size: 18px;">
    Vložení žádosti 
    </div>
    <div class="col"></div>
    <div class="col-6"></div>
</div>

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
