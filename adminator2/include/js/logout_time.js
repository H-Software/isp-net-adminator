// JavaScript Document
var Arrived = new Date(0,0,0,0,0,0)
var toJump  = new Date(0,0,0,0,3,0)
var zeroDate = new Date(0,0,0,0,0,0)
var fJumped = false



function SetDate( objDate, field, fShowDate ){
var hours=objDate.getHours()
var minutes=objDate.getMinutes()
var seconds=objDate.getSeconds()
var days = objDate.getDate()
var months = objDate.getMonth()
var years = objDate.getYear()

	if (years < 1000) {
		years = years + 1900
	}

var strDate = days + "." + (months+1) + "." + years
	
	if (hours==0)
		hours="0"+hours
	if (minutes<=9)
		minutes="0"+minutes
	if (seconds<=9)
		seconds="0"+seconds

	field.value= ( fShowDate ? ( strDate + ",  " ) : "" )  +minutes+" min "+seconds+" sec " 


}


function Clock(){
var currentDate = new Date()

	// Inkrementujeme cas od vstupu o sekundu
	Arrived.setTime( Arrived.getTime() + 1000 );

	// dekrementujeme cas do zacatku akce o sekundu
	toJump.setTime( toJump.getTime() - 1000 );

	SetDate( currentDate, document.hours.time, 1 );
	SetDate( Arrived, document.hours.elapsed, 0 );
	if ( !fJumped )
		SetDate( toJump, document.hours.timetojump, 0 );
	else
		document.hours.timetojump.value = "už proběhla"
 
	if ( toJump.getTime() == zeroDate.getTime() ) {
		window.location.href="index.php?lo=true";
		fJumped = true
	}

	setTimeout("Clock()",1000)

}

Clock()
