
function checkedAll () 
{ 

    for (var i = 1; i <= 1000; i++) 
    {  
      var box = eval("document.checkboxform.q" + i); 
      box.checked = true;
    }
}

function uncheckedAll () 
{ 
    for (var i = 1; i <= 1000; i++) 
    {  
      var box = eval("document.checkboxform.q" + i); 
      box.checked = false;
    }
}

function reverseAll () 
{ 
    for (var i = 1; i <= 1000; i++) 
    {  
      var box = eval("document.checkboxform.q" + i); 
      box.checked = ! box.checked;
    }
}

