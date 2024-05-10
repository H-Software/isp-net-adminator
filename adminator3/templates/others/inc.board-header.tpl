
{* <div class="head-main" >
      <div class="heading " ></div> 
</div> *}

<div class="row">
    <div class="col"></div>
    <div class="col-10" style="padding-top: 15px">
            <div class="head-main-napis">Bulletin Board - Nástěnka ver. 2.0</div>
    </div>
    <div class="col"></div>
</div>

<div class="row">
    <div class="col"></div>
    <div class="col-10">
        <div class="row head-menu-main" style="margin-left: 0px; margin-right: 0px;">

            <div class="col-md-1">&nbsp;
            </div>
            <div class="col-md-2">
                <div class="tableheading head-menu-polozky"><a href="/others/board?action=post">PŘIDAT ZPRÁVU</a></div>
            </div>
            <div class="col-md-2">
                <div class="tableheading head-menu-polozky"><a href="/others/board?action=view&what=new">AKTUÁLNÍ ZPRÁVY</a></div> 
            </div> 
            <div class="col-md-2">
                <div class="tableheading head-menu-polozky"><a href="/others/board?action=view&what=old">STARÉ ZPRÁVY</a></div>
            </div> 
            <div class="col-md-2">
                <div class="head-menu-polozky" >
                {$datum}
                </div>
            </div>
            <div class="col-md-2 text-start">
                <span class="board-rss"><a href="/board/rss?token={$token|default:'' }" >RSS 2.0</a></span>
            </div>
        </div>
    </div>
    <div class="col"></div>
</div>

