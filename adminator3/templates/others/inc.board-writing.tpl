
<div class="row justify-content-md-center">
    <div class="col"></div>
    <div class="col-8">
        <div class="panel panel-default">
            <div class="panel-heading">{$mod_hlaska}</div>
            <!-- <div class="tableheadingwrite typ-zprav" >{$mod_hlaska}</div> -->
        </div>
        
        {if strlen($error) gt 0}
            <div class="alert alert-danger pt-15" role="alert">
                {$error}
            </div>
        {/if}

        <div class="writing-main" >
            <form method="post" name="form-board" >

            {$csrf_html}

            <div class="form-group px-3 pt-15">
                <label for="name">Vaše jméno:</label>
                <input type="text" name="name" placeholder="your name" class="form-control" value="{$nick}">
            </div>

            <div class="form-group px-3">
                <label for="email">Váš e-mail:</label>
                <input type="email" name="email" placeholder="your email" class="form-control" value="{$email}">
            </div>

            <div class="table writing-field-zobr" ><b>Zobrazit od-do</b></div>

            <div class="table writing-field-date" >
                od: <input type="text" name="from_date" class="input" value="{$from_date}">

            {literal}
            <script language="JavaScript">
                new tcal ({'formname': 'form-board','controlname': 'from_date'});
            </script>
            {/literal}
                <div style="padding-top: 5px;" ></div>do:
            <input type="text" name="to_date" class="input" value="{$to_date}">

            {literal}
            <script language="JavaScript">
            var d_selected = new Date();
            d_selected.setDate(d_selected.getDate() + 7);
            var s_selected = f_tcalGenerDate(d_selected);

            new tcal ({'formname':'form-board','controlname':'to_date','selected':s_selected});
            </script>
            {/literal}
                    
            </div>

            <div class="table writing-left2" >Předmět:</div>

            <div class="writing-right1" >
                <input type="text" name="subject" size="30" maxlength="50" class="input" value="{$subject}" >
            </div>

            <div class="table writing-left2" >Text zprávy:</div>

            <div class="writing-right1" >
                <textarea cols="40" rows="7" name="body" class="input">{$body}</textarea>
            </div>

            <div style="text-align: center; padding-top: 10px;" >
                <input type="submit" name="send" value="Odeslat" class="btn btn-default">
            </div>
            
            <input type="hidden" name="sent" value="true" >
            </form>
        </div>
    </div>
    <div class="col"></div>
</div>
