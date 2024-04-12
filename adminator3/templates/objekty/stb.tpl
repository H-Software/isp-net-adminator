{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

<div class="global-cat-body2" >

    <div style="padding-top: 15px; padding-bottom: 15px; " >
        <span style=" padding-left: 5px; 
        font-size: 16px; font-weight: bold; " >
        .:: Výpis Set-Top-Boxů ::. </span>
        
        <span style="padding-left: 25px; " >
            <a href="/objekty/stb/action" >přidání nového stb objektu</a>
        </span>
        
        <span style="padding-left: 5px;" >
            <!--<a href="objekty-stb-add-portal.php" >-->
            <img src="/img2/Letter-P-icon-small.png" alt="letter-p-small" width="20px" >
            <!--</a>-->
        </span>
        
        <span style="padding-left: 25px; " >
            <a href="#" onclick="visible_change(objekty_stb_filter)" >filtr/hledání</a>
        </span>
        
        <span style="padding-left: 25px; " >
            <!-- <a href="/admin-login-iptv.php" target="_new" >--> aktivace funkcí IPTV portálu (přihlašení)<!--</a>-->
        </span>
        
    </div>

{$body}

</div>

{include file="base-end.tpl"}
