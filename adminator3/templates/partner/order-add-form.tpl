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

{$f_open}
{$f_csrf}

<div class="row form-group">
    <div class="col-1"></div>
    <div class="col-5" style="padding-top: 15px">

    {$f_input_jmeno_klienta}

    {$f_input_bydliste}

    {$f_input_email}

    {$f_input_tel}

    {$f_input_pozn}

    {$f_input_typ_balicku}

    {$f_input_typ_linky}

    {$f_submit_button}

    </div>
    <div class="col-5"></div>
</div>

{$f_close}

</div>

{include file="base-end.tpl"}
