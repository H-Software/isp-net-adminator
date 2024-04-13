{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

{include file="base-end.tpl"}

<div class="container-sm justify-content-md-center">
    <div class="col-md-6 col-md-offset-3">
    <div class="row">
    {$f_open}
        <div class="col-md-12 panel panel-default">
            <div class="panel-heading">Mód:</div>
            <div class="panel-body">Optická síť</div>            
        </div>

        {if strlen($f_messages) gt 0}
        <div class="col-md-12 panel panel-default">
            <div>{$f_messages}</div>
        </div>
        {/if}

        <div class="col-md-6">
            {$f_input_popis}
        </div>

        <div class="col-md-6">
            {$f_input_nod_find}
        </div>

        <div class="col-md-6">
            {$f_input_ip}
        </div>

        <div class="col-md-6">
            <label for="id_nodu" class="form-label">Přípojný bod</label>
            <div class="form-inline">
                <div class="form-group">{$f_input_id_nodu}</div>
                <div class="form-group">{$f_input_nod_find_button}</div>
            </div>
        </div>

        <div class="col-md-6">
            {$f_input_mac}
        </div>

        <div class="col-md-6">
            <div style="padding-top: 22px;">
                {$f_input_gen_button}
            </div>
        </div>

        <div class="col-md-12"></div>

        <div class="col-md-6">
            {$f_input_puk}
        </div>

        <div class="col-md-6">
            {$f_input_pin1}
        </div>

        <div class="col-md-6">
            {$f_input_port_id}
        </div>

        <div class="col-md-6">
            {$f_input_pin2}
        </div>

        <div class="col-md-6">
            {$f_input_pozn}
        </div>

        <div class="col-md-6">
            {$f_input_id_tarifu}
        </div>
        
        <div class="col-md-12 text-center warning" style="padding-top: 22px;">
            {$f_submit_button}
        </div>

    {$f_close}
    </div>
    </div>
</div>
