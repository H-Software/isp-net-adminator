{include file="base.tpl"}

 {include file="inc.cat.others.tpl"}

 <div style="padding-top: 10px; padding-bottom: 20px; padding-left: 10px; font-size: 18px; text-decoration: underline; ">
    Informace z odeslaných formulářů z webu spolecnosti
 </div>
 
{if $alert_type|count_characters > 1}
   {include file="partials/bootstrap-alert-with-columns.tpl"}
{/if}

{include file="base-end.tpl"}
