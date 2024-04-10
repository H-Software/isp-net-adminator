
<div style="width: 800px; position: relative; margin-left: auto; margin-right: auto; padding-bottom: 30px; padding-top: 20px; boder: 1px solid grey;" >

    <table border="1" width="800px" align="left" style="font-size: 12px; font-family: Verdana; ">

      <tr>
        <td colspan="{$pocet_bunek}" ><br>
      </tr>

      <form action="{$action}" method="GET" name="form4" >
    
      <tr>
        <td colspan="3" ><span style="font-size: 18px; font-weight: bold; " >
                <a href="opravy-index.php?zobr_vlastnika=0&typ=2&priorita_filtr=99&v_reseni_filtr=99&vyreseno_filtr=99" >Výpis Závad/oprav </a></span>
        </td>
        <td colspan="7" >

          <input type="hidden" name="typ" value="{$typ}" >

          <span style="padding-left: 10px; padding-right: 10px; " >V řešení: </span>

          <select size="1" name="v_reseni_filtr"  onChange="self.document.forms.form4.submit()" >

          {if $vyreseno_filtr == "99" }
            <option value="99" {if $v_reseni_filtr == "99" } selected {/if} class="opravy-form-nevybrano" >Nevybráno</option>
            <option value="0" {if $v_reseni_filtr == "0" } selected {/if} >Ne</option>
              <option value="1" {if $v_reseni_filtr  == "1" } selected {/if} >Ano</option>
          {else}
            <option value="99" class="opravy-form-nevybrano" >Nelze vybrat</option>
          {/if}
    
          </select>

          <span style="padding-left: 20px; padding-right: 10px;" >Vyřešeno: </span>

          <select size="1" name="vyreseno_filtr" onChange="self.document.forms.form4.submit()" >

          {if $v_reseni_filtr == "99" }
              <option value="99" {if $vyreseno_filtr == 99 } selected {/if} class="opravy-form-nevybrano" >Nevybráno</option>
              <option value="0" {if $vyreseno_filtr == 0 } selected {/if} >Ne</option>
              <option value="1" {if $vyreseno_filtr == 1 } selected {/if} >Ano</option>        
          {else}
              <option value="99" class="opravy-form-nevybrano" >Nelze vybrat</option>
          {/if}

          </select>

          <span style="padding-left: 20px; padding-right: 10px;" >Limit</span>
                  <select name="limit" size="1" >
                      <option value="1000" {if $limit == "1000" } selected {/if} class="opravy-form-nevybrano" >Nevybráno</option>
                      <option value="10" {if $limit == "10" } selected {/if} >10</option>
                  </select>

          <span style="padding-left: 20px; padding-right: 10px;" ><input type="submit" value="ok" name="OK" ></span>

        </td>
      </tr>

      </form>

      <tr>
        <td colspan="{$pocet_bunek}" style="border-bottom: 1px solid black;" ><br>
      </tr>

      <tr>
        <td style="border-bottom: 1px dashed black;" ><b>id <br>opravy: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>id předchozí <br> opravy: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>id vlastníka: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>datum vložení: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>priorita: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>v řešení: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>vyřešeno: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>vložil: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>začít řešit: </b></td>
        <td style="border-bottom: 1px dashed black;" ><b>vložit odpověď: </b></td>
      </tr>

      <tr>
        <td colspan="{$pocet_bunek}" style="border-bottom: 1px solid black;" ><b>Text</b></td>
      </tr>
      <tr>
        <td colspan="{$pocet_bunek}" ></td>
      </tr>

      {$content_opravy_a_zavady}

    </table>  
</div>
