{include file="base.tpl"}

 {include file="inc.cat.others.tpl"}

 <div style="padding-left: 5px; padding-top: 10px; height: 100%" >
 <form method="POST" action="{$action}" >

    <div style="font-size: 24px; font-weight: bold; padding-bottom: 30px;" >Sekce pro TISK dokumentů</div>

    <div style="clear: both; " >&nbsp;</div>

    <div style="float: left; width: 250px;" >Smlouva - optika/wifi (formát PDF)
        <a href="print/smlouva-pdf.php" target="_new" >zde</a>
    </div>
    <div style="float: left; padding-left: 20px; padding-right: 20px; " align="center" >
        <a href="img2/print/2011-10-24-smlouva_small.jpg" >
            <img src="img2/print/2011-10-24-smlouva_thumb.jpg" alt="smlouva nova" border="0">
        </a>
    </div>

    <div style="float: left; width: 250px; " >Registrační formlulář - optika/wifi<br> (formát PDF)
     <a href="print/reg-form-pdf.php">zde</a></div>
    <div style="padding-left: 20px; padding-right: 20px; width: 250px; float: left; " >
        <a href="img2/print/2009_technicka_sekce_small.jpg" >
            <img src="img2/print/2009_technicka_sekce_thumb.jpg" alt="reg. formular nový" border="0">
        </a>
    </div>

    <div style="clear: both; width: 100%;" >&nbsp;</div>

    <div style="width: 400px; padding-bottom: 20px; float: left;" >

        <div style="float: left; width: 200px; padding-bottom: 2px;" >Výpis vygenerovaných souborů:</div>

        <div style="float: left; width: 70px; padding-left: 105px; padding-bottom: 2px;" >
            <input type="submit" name="od3" value="Zobrazit" >
        </div>

        <div>
         <select size="10" name="soubory" style="width: 370px;" >

          {section name="kat_prvek" loop=$soubory_smlouvy_new}
            <option value="{$soubory_smlouvy_new[kat_prvek]}" >{$soubory_smlouvy_new[kat_prvek]}</option>
          {sectionelse}
            <option value="0" class="select-nevybrano" >Žádný soubor pro tento typ dokumentu nenalezen</option>
          {/section}

         </select>
        </div>
    </div>

    <div style="width: 400px; padding-bottom: 20px; float: left;" >

        <div style="float: left; width: 200px; padding-bottom: 2px;" >Výpis vygenerovaných souborů:</div>

        <div style="float: left; width: 70px; padding-left: 105px; padding-bottom: 2px;" >
            <input type="submit" name="od4" value="Zobrazit" >
        </div>

        <div>
         <select size="10" name="soubory" style="width: 370px;" >

          {section name="kat_prvek" loop=$soubory_regform_new}
            <option value="{$soubory_regform_new[kat_prvek]}" >{$soubory_regform_new[kat_prvek]}</option>
          {sectionelse}
            <option value="0" class="select-nevybrano" >Žádný soubor pro tento typ dokumentu nenalezen</option>
          {/section}

         </select>
        </div>
    </div>

    <div style="float: left; width: 250px;" >Smlouva - nová (2012-05) (formát PDF)
        <a href="print/smlouva-2012-05.php" target="_new" >zde</a>
    </div>
    <div style="float: left; padding-left: 20px; padding-right: 20px; " align="center" >
        <a href="img2/print/2012-05-31-smlouva_small.jpg" >
            <img src="img2/print/2012-05-31-smlouva_thumb.jpg" alt="smlouva nova2" border="0">
        </a>
    </div>

    <div style="float: left; width: 250px; " >Registrační formlulář - nový (2012-05) <br> (formát PDF)
	<a href="others-print-reg-form-2012-05.php">zde</a></div>
    <div style="padding-left: 20px; padding-right: 20px; width: 250px; float: left; " >
        <a href="img2/print/2012-05-form-small.jpg" >
            <img src="img2/print/2012-05-form-thumb.jpg" alt="reg. formular nový 3" border="0">
        </a>
    </div>

    <div style="clear: both; width: 100%;" >&nbsp;</div>

    <div style="width: 400px; padding-bottom: 20px; float: left;" >

        <div style="float: left; width: 200px; padding-bottom: 2px;" >Výpis vygenerovaných souborů:</div>

        <div style="float: left; width: 70px; padding-left: 105px; padding-bottom: 2px;" >
            <input type="submit" name="od3" value="Zobrazit" >
        </div>

        <div>
         <select size="10" name="soubory" style="width: 370px;" >

          {section name="kat_prvek" loop=$soubory_smlouva_v3}
            <option value="{$soubory_smlouva_v3[kat_prvek]}" >{$soubory_smlouva_v3[kat_prvek]}</option>
          {sectionelse}
            <option value="0" class="select-nevybrano" >Žádný soubor pro tento typ dokumentu nenalezen</option>
          {/section}

         </select>
        </div>
    </div>

    <div style="width: 400px; padding-bottom: 20px; float: left;" >

        <div style="float: left; width: 200px; padding-bottom: 2px;" >Výpis vygenerovaných souborů:</div>

        <div style="float: left; width: 70px; padding-left: 105px; padding-bottom: 2px;" >
            <input type="submit" name="od3" value="Zobrazit" >
        </div>

        <div>
         <select size="10" name="soubory" style="width: 370px;" >

          {section name="kat_prvek" loop=$soubory_reg_form_2012_05}
            <option value="{$soubory_reg_form_2012_05[kat_prvek]}" >{$soubory_reg_form_2012_05[kat_prvek]}</option>
          {sectionelse}
            <option value="0" class="select-nevybrano" >Žádný soubor pro tento typ dokumentu nenalezen</option>
          {/section}

         </select>
        </div>
    </div>

 </form>
 </div>

{include file="base-end.tpl"}

