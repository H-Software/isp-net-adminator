
<div class="row">
    <div class="col-md-6 col-md-offset-3">

        <div class="tableheading typ-zprav" >{$mod_hlaska}</div>
            {if $rs == 1}
            <div class="alert alert-success pt-15 text-center" role="alert" >Zpráva úspěšně uložena.</div>
            {else}
            <div class="alert alert-danger pt-15 text-center" role="alert" >Zprávu se nepodařilo uložit.</div>

            <div class="alert alert-danger pt-15 text-center" role="alert" >{$body}</div>

            {/if}
        </div>

    </div>
</div>