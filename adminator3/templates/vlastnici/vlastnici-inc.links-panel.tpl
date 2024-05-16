
<div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; ">
    <span style="">
    {if $vlastnici_pridani_povoleno eq "true"}
        <a href="vlastnici2-change.php?firma_add=2">Přidání vlastníka</a>
    {else}
        <span style="color: grey; font-style: italic">Přidání vlastníka</span>
    {/if}
    
    </span>

    <span style="padding-left: 25px; ">
        {if $vlastnici_export_povolen eq "true"}
            <a href="\export\vlastnici.xls" >export dat</a>
        {else}
            <span style="color: grey; font-style: italic">export dat</span>
        {/if}
    </span>
    
</div>