
<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
            <div class="tableheading typ-zprav">{$mod_hlaska}</div>
            {if $rs == false }
                <div class="alert alert-danger pt-15 text-center" role="alert" >Zprávu se nepodařilo uložit.</div>
                <div class="alert alert-danger pt-15 text-center" role="alert" >{$body}</div>
            {else}
                <div class="alert alert-success pt-15 text-center" role="alert" >Zpráva úspěšně uložena.</div>
            {/if}
    </div>
    <div class="col-2"></div>
</div>
