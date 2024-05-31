<div class="row">
    <div class="col"></div>
    <div class="col-8">


        <div class="tableheading typ-zprav" >{$mod_hlaska|default:''}</div>

        {if strlen($query_error|default:'') gt 0}
          <div class="alert alert-danger pt-15" role="alert">
              {$query_error}
          </div>
       {/if}

        {section name="entry" loop=$zpravy}
          <div class="tableheading" style="padding-top: 2px; " >zpráva č. {$zpravy[entry].id}</div>

          <div class="table zprava-main" >
            <a href="mailto:{$zpravy[entry].email}" ><b>{$zpravy[entry].author}</b></a>
            <br>
            <b>{$zpravy[entry].subject}</b> [{$zpravy[entry].from_date} - {$zpravy[entry].to_date}]
            <div class="board-message-body">{$zpravy[entry].body}</div>
          </div>
        {/section}

        <div style="text-align: right; border-top: 1px solid #7D7642; padding-top: 5px; " class="table" id="board-list-pagging" >
          <b>strana

          {section name="page" loop=$strany}
            |
          {if $strany[page].i == $strany[page].i_akt}
            {$strany[page].i2}
          {else}
          <a href="/others/board?action=view&what={$strany[page].what}&page={$strany[page].i}">
            {$strany[page].i2}
                </a>
          {/if}
          {/section}

          |</b>

        </div>

    </div>
    <div class="col"></div>
</div>
