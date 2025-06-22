{include file="base.tpl"}

<div class="row justify-content-md-center">
    <div class="col"></div>
    <div class="col-8">

        <div class="alert alert-danger fs-3" role="alert">Nelze zobrazit požadovanou stránku!</div>
          
        <div class="alert alert-danger">Selhala kontrola CSRF tokenu.</div>
        <br><br>	

        <br>
          Vraťte se na předchozí <a href="javascript:history.back(1)">stránku</a>.
        <br><br>	    
        
        <b>Zpráva od systému:</b> {$body}

      </div>
    <div class="col"></div>
</div>

{include file="base-end.tpl"}
