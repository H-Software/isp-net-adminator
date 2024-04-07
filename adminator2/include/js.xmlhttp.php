<?php

echo "

<script type=\"text/javascript\" >
/** Odeslání XMLHttp požadavku
* @param function state_change funkce zajišťující obsluhu při změně stavu požadavku, dostane parametr s XMLHttp objektem
* @param string method GET|POST|...
* @param string url URL požadavku
* @param string [content] tělo zprávy
* @param object [headers] předané hlavičky ve tvaru { 'hlavička': 'obsah' }
* @return bool true v případě úspěchu, false jinak
*/
function send_xmlhttprequest(state_change, method, url, content, headers) {
    var xmlhttp = (window.XMLHttpRequest ? new XMLHttpRequest() : (window.ActiveXObject ? new ActiveXObject(\"Microsoft.XMLHTTP\") : false));
    if (!xmlhttp) {
        return false;
    }
    xmlhttp.open(method, url);
    xmlhttp.onreadystatechange = function () {
        state_change(xmlhttp);
    };
    if (headers) {
        for (var key in headers) {
            xmlhttp.setRequestHeader(key, headers[key]);
        }
    }
    xmlhttp.send(content);
    return true;
}

function anketa_hlasovat(hlas) 
{
    // odeslání požadavku na aktualizaci dat
    if(!send_xmlhttprequest(anketa_obsluha, 'GET', 'faktury_rpc.php?anketa=' + hlas)) 
    {
        return false;
    }
    
    // document.getElementById('pocet' + hlas).innerHTML++; // zobrazení hlasu u klienta
    // znemožnění opětovného hlasování smazáním odkazů
    ";
    
    /*
    document.getElementById('pocet' + hlas).innerHTML++; // zobrazení hlasu u klienta
    // znemožnění opětovného hlasování smazáním odkazů
    for (var key in document.getElementById('odpoved').getElementsByTagName('div')) {
            var val = document.getElementById('odpoved').getElementsByTagName('div')[key];
            if (val.className == 'odpoved') {
                val.innerHTML = val.firstChild.innerHTML;
        }
    }
    */
    echo "
    document.getElementById('odpoved').innerHTML = 'work ...';
    
    document.getElementById('stav-anketa').innerHTML = 'Ukládá se';
    
    return true;
}

function anketa_obsluha(xmlhttp) 
{
    if (xmlhttp.readyState == 4) 
    {
        // aktualizace odpovědí na základě aktuálního stavu
    var odpovedi = xmlhttp.responseXML.getElementsByTagName('odpoved');
    
    for (var i=0; i < odpovedi.length; i++) 
    {
        document.getElementById('odpoved').innerHTML = odpovedi[i].firstChild.data;
    }

    document.getElementById('stav-anketa').innerHTML = '<b>Uloženo</b>';}
}

</script>

";

?>
