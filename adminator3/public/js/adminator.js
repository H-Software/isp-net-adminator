function visible_change(i){

    if (i.style.display=='none'){
	 i.style.display='';
    }
    else{
        i.style.display='none';
    }
}

$(document).ready(function() {
    // fix jquery/bootstrap-table duplicate tbody
    $('[id=hidden]').hide();
    // var th = $(".fixed-table-body > thead");
    // th.hide();
});
