{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

{include file="base-end.tpl"}

<div class="container-sm justify-content-md-center">
    <div class="col-md-6 col-md-offset-3">
    {$f_open}
    <div class="row">
        <div class="col-md-12 panel panel-default">
            <div class="panel-heading">Mód:</div>
            <div class="panel-body">Optická síť</div>            
        </div>

        <div class="col-md-6">
            {$f_input_popis}
        </div>

        <div class="col-md-6">
            {$f_input_nod_find}
        </div>

        <div class="col-12">
            <label for="inputAddress" class="form-label">Address</label>
            <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
        </div>

        <div class="col-12">
            <label for="inputAddress2" class="form-label">Address 2</label>
            <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
        </div>
        
        <div class="col-md-6">
            <label for="inputCity" class="form-label">City</label>
            <input type="text" class="form-control" id="inputCity">
        </div>
        
        <div class="col-md-4">
            <label for="inputState" class="form-label">State</label>
            <select id="inputState" class="form-control">
            <option selected>Choose...</option>
            <option>...</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="inputZip" class="form-label">Zip</label>
            <input type="text" class="form-control" id="inputZip">
        </div>

        <div class="col-12 text-center">
            {$f_submit_button}
        </div>

    </div>
    {$f_close}
    </div>
</div>
