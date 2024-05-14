<form method="POST" action="/print/redirect">
<div>
{$csrf_html}
<input type=hidden" name="soubory" value="{$file_name}">
   Vygenerovaný soubor "{$file_name}" <input type="submit" name="odeslat" value="Zobrazit" >
</div>
</form>