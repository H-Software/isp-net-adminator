<div class="container text-center">
    <div class="row ">
        <div class="col">
        </div>
        <div class="col-6">
            {if $p_bs_alerts|default:'0' }
                {foreach $p_bs_alerts as $a}
                    <div class="alert alert-{$a@key}" role="alert" >
                    {$a|default: ''}
                    </div>
                {/foreach}
          {/if}
        </div>
        <div class="col">
        </div>
    </div>
</div>
