
{include file="base-bs.tpl" tpl_include_subcat = 'objekty/inc.subcat'}

<div style="background-color: #EEEEEE; ">
<div style="margin: 5px; ">

    {$f_open}

    {if strlen($f_input_update_id) gt 0}
        {$f_input_update_id}
    {/if}

    <div class="row g-3">
            <div class="col-md-12 card">
                <div class="card-header bg-secondary text-white">Mód:</div>
                <div class="card-body text-white" style="background-color: #99C4D2;">Optická síť</div>            
            </div>

            {if strlen($f_messages) gt 0}
            <div class="col-md-12">
                <div>{$f_messages}</div>
            </div>
            {/if}

            {if strlen($f_messages_validation) gt 0}
                <div class="col-md-12">
                    <div>{$f_messages_validation}</div>
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

            {* 
                special formatting because of "inline"
                https://getbootstrap.com/docs/5.3/forms/layout/#inline-forms
            *}
            <div class="col-md-6">
                <label for="id_nodu" class="form-label">Přípojný bod</label>
                <div class="row row-cols-lg-auto g-2 align-items-center">
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

    </div>
    {$f_close}

</div>
</div>
{include file="base-bs-end.tpl"}
