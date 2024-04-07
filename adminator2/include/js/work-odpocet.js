
var timerID = null; 
var timerRunning = false; 

function showtime() 
{ 

    today = new Date(); 

    minutes = today.getMinutes();
    
    year = today.getFullYear();

    hour = today.getHours();
    
    month = (1 + today.getMonth());
    
    day = (today.getDay());
    //day = today.getDay();
        
    if(minutes > 45 && minutes <= 59) 
    { 
	minutes_restart = 00; 
	hour = (hour + 1);
    }   
    else if (minutes > 30 && minutes <= 44) { minutes_restart = 45; }
    else if (minutes > 15 && minutes <= 29) { minutes_restart = 30; }
    else if (minutes > 0 && minutes <= 14) { minutes_restart = 15; }
    else
    { minutes_restart = (minutes + 1); }
    
    Prodej = new Date(month+" "+day+", "+year+", "+hour+":"+minutes_restart);
    		  
    msPerDay = 24 * 60 * 60 * 1000 ; 
    timeLeft = (Prodej.getTime() - today.getTime()); 
    e_daysLeft = timeLeft / msPerDay; 
    daysLeft = Math.floor(e_daysLeft); 
    e_daysLeft = timeLeft / msPerDay; 
    daysLeft = Math.floor(e_daysLeft); 
    e_hrsLeft = (e_daysLeft - daysLeft)*24; 
    hrsLeft = Math.floor(e_hrsLeft); 
    minsLeft = Math.floor((e_hrsLeft - hrsLeft)*60); 
    hrsLeft = hrsLeft; 
    e_minsLeft = (e_hrsLeft - hrsLeft)*60; 
    secLeft = Math.floor(e_hrsLeft); 
    secLeft = Math.floor((e_minsLeft - minsLeft)*60); 
    secLeft = secLeft; 

    //Temp3=''+daysLeft+' dní, '+hrsLeft+' hodin, '+minsLeft+' minut, '+secLeft+' sekund.' 
    Temp3='0 hodin, '+minsLeft+' minut, '+secLeft+' sekund.'; 
    
    //document.odpocet.zbyva.value=Temp3;
    
    document.getElementById('autorestart').innerHTML = Temp3;
      
    timerID = setTimeout("showtime()",1000); 
    timerRunning = true; 
} 

var timerID = null; 
var timerRunning = false; 

function stopclock () { 

    if(timerRunning) 
    clearTimeout(timerID); 
    timerRunning = false; 
} 

function stopClock() { 
    stopclock(); 
    return; 
} 

function startclock () { 
    stopclock(); 
    showtime(); 
}


