
<div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; ">
    <span style="">
    {if $vlastnici2_pridani_povoleno eq "true"}
        <a href="/vlastnici2/change" >Přidání vlastníka</a>
    {else}
        Přidání vlastníka (N/A)
    {/if}
    
    </span>

    <span style="padding-left: 25px; ">
        {if $vlastnici2_export_povolen eq "true"}
            <a href="\export\vlastnici-sro.xls" >export dat</a>
        {else}
            export dat (N/A)
        {/if}
    </span>

    <span style="padding-left: 25px; ">
        <!--<a href=\"include/export-ucetni.php\" >-->export ucetni<!--</a>-->
    </span>

    <span style="padding-left: 25px; ">
        <!--<a href=\"admin-login-iptv.php\" target=\"_new\" >-->aktivace funkcí IPTV portálu (přihlašení)<!--</a>-->
    </span>

</div>