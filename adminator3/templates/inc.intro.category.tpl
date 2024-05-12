{* prvni radka kategorii *}
<div class="cat-main-1-line" >

      {section name="kat_prvek" loop=$kategorie}

      <div style="width: 135px; float: left; background-color: {$kategorie[kat_prvek].barva}; text-align: {$kategorie[kat_prvek].align}; " >
            <a class="cat" href="{$kategorie[kat_prvek].url}" target="_top" >{$kategorie[kat_prvek].nazev}</a>
      </div>
      {* *}
      {sectionelse}
        {* Tato cast se provede v pripade prazdneho pole *}
        <div class="cat-no-records" >Chyba! Žádné kategorie nenalezeny ..</div>
      {/section}
</div>

{* druhy radek kategorii... *}
<div class="cat-main-2-line" >

      {section name="kat_prvek2" loop=$kat_2radka}

      <div style="width: 135px; float: left; background-color: {$kat_2radka[kat_prvek2].barva}; text-align: {$kat_2radka[kat_prvek2].align};" >
           <a class="cat" href="{$kat_2radka[kat_prvek2].url}" target="_top" >{$kat_2radka[kat_prvek2].nazev}</a>
      </div>
      
      {sectionelse}
        {* Tato cast se provede v pripade prazdneho pole *}

        <div class="cat-no-records" >Chyba! Žádné kategorie nenalezeny ..</div>

      {/section}

      <div class="cat-select-odkazy" >
        <form name="form2" method="POST" >
          {$kat_csrf_html}
          <select name="show_se_cat" size="1" onChange="self.document.forms.form2.submit()" style="font-size: 10px; " >
           {html_options values=$show_se_cat_values selected=$show_se_cat_selected output=$show_se_cat_output}
          </select>
        </form>
     </div>

</div>

<div class="cara-kategorie" ></div>

  {if $show_se_cat eq "1"}
    {include file="inc.intro.category-ext.tpl"}
  {else}
    <!--<div>form data show_se_cat: {$show_se_cat}</div>-->
  {/if}
