<?php
/*/////////////////////////////////////////////////////////////////////////////////////////////////
	
	DATE FUNCTIONS - Designed to make my life easier!
	
	$filter follows the same rules as the php DATE function. ie: GetNextDay("Y-m-d",$ourtime);
	If you leave filter empty, ie: GetNextDay("",$ourtime);  the function will return the time in seconds
	
	DATE MANIPULATION - Returns date
	
	GetNextDay($filter[,$time]);								= Gets the date of tomorrow
	GetPrevDay($filter[,$time]);								= Gets the date of yesterday

	GetNextMonth($filter[,$time]);								= Gets the date of next month
	GetPrevMonth($filter[,$time]);								= Gets the date of prev month

	GetStartOfNextMonth($filter[,$time]);						= Gets the date of the first of next month
	GetEndOfNextMonth($filter[,$time]);							= Gets the date of the last day of next month

	GetStartOfThisMonth($filter[,$time]);						= Gets the date of the first of this month
	GetEndOfThisMonth($filter[,$time]);							= Gets the date of the last day of this month
	
	GetStartOfLastMonth($filter[,$time]);						= Gets the date of the first of last month
	GetEndOfLastMonth($filter[,$time]);							= Gets the date of the last day of last month
	
	GetStartOfNextWeek($filter[,$dayofweek,$time]);				= Gets the date of the first of next Week, where $dayofweek is the first day of the week: 0 for sunday, 6 for saturday
	GetEndOfNextWeek($filter[,$dayofweek,$time]);				= Gets the date of the last day of next Week

	GetStartOfThisWeek($filter[,$dayofweek,$time]);				= Gets the date of the first of this Week
	GetEndOfThisWeek($filter[,$dayofweek,$time]);				= Gets the date of the last day of this Week

	GetStartOfLastWeek($filter[,$dayofweek,$time]);				= Gets the date of the first of last Week
	GetEndOfLastWeek($filter[,$dayofweek,$time]);				= Gets the date of the last day of last Week

	
	AddDays($filter,$days[,$time]);								= Adds $days (integer) to the date
	SubDays($filter,$days[,$time]);								= Subtracts $days (integer) from the date
	AddWeeks($filter,$weeks[,$time]);							= Adds $weeks (integer) to the date
	SubWeeks($filter,$weeks[,$time]);							= Subtracts $weeks (integer) from the date
	AddMonths($filter,$months[,$time]);							= Adds $months (integer) to the date
	SubMonths($filter,$months[,$time]);							= Subtracts $months (integer) from the date
	AddYears($filter,$years[,$time]);							= Adds $years (integer) to the date
	SubYears($filter,$years[,$time]);							= Subtracts $years (integer) from the date
	AddMins($filter,$minutes[,$time]);							= Adds $minutes (integer) to the time
	SubMins($filter,$minutes[,$time]);							= Subtracts $minutes (integer) from the time
	AddHours($filter,$hours[,$time]);							= Adds $hours (integer) to the time
	SubHours($filter,$hours[,$time]);							= Subtracts $hours (integer) from the time
	
	DATE DIFFERENCES - Returns whole Integers, second date is assumed as today if not set. It makes no difference
	which order the dates are entered.
	
	GetDateDiffDays($date1[,$date2]);							= Gets the difference between the two dates in days
	GetDateDiffMins($date1[,$date2]);							= Gets the difference between the two dates in minutes
	GetDateDiffHours($date1[,$date2]);							= Gets the difference between the two dates in hours
	GetDateDiffWeeks($date1[,$date2]);							= Gets the difference between the two dates in weeks
	
/*/////////////////////////////////////////////////////////////////////////////////////////////////



function GetNextMonth($filter,$time=0) {
	//this function gets the next valid day date from the one supplied.
	if ($time == 0) {
		$time = time();
	}
	
	$day = date("d",$time);
	$month = date("m",$time);
	$year = date("Y",$time);
	
	
		$n_month = $month + 1;
		if (checkdate($n_month,$day,$year)) {
			//month was not on the end of the year, so go to the next month and leave the year the same
			$n_year = $year;
		} else {
			//month was on the end of the year, so increase the year aswell
			$n_month = 1;
			$n_year = $year + 1;
		}
	$nextmonth = mktime(0,0,0,$n_month,$day,$n_year);
	return FormatDateTime($filter,$nextmonth);
}








function GetPrevMonth($filter,$time=0) {
	//this functions gets the previous valid day date from the one supplied.
	if ($time == 0) {
		$time = time();
	}
	
	$day = date("d",$time);
	$month = date("m",$time);
	$year = date("Y",$time);
		
		if ($month > 1) {  //if so, then only decrease the month and thats it, then find the last day in that month below
			$p_month = $month - 1;
			$p_year = $year;
		} else { //decrease the year aswell!
			$p_month = 12;
			$p_year = $year - 1;
		}

	$prevmonth = mktime(0,0,0,$p_month,$day,$p_year);
	return FormatDateTime($filter,$prevmonth);	
}








function GetNextDay($filter,$time=0) {
	//this function gets the next valid day date from the one supplied.
	if ($time == 0) {
		$time = time();
	}
	
	$day = date("d",$time);
	$month = date("m",$time);
	$year = date("Y",$time);
	
	$n_day = $day + 1;
	if (checkdate($month,$n_day,$year)) {
		//day was not on the end of the month, so just increase the day and leave the month and year the same
		$n_month = $month;
		$n_year = $year;
	} else {
		//day was on the end of the month
		$n_day = 1;
		$n_month = $month + 1;
		if (checkdate($n_month,$n_day,$year)) {
			//month was not on the end of the year, so go to the next month and leave the year the same
			$n_year = $year;
		} else {
			//month was on the end of the year, so increase the year aswell
			$n_month = 1;
			$n_year = $year + 1;
		}
	}
	$nextday = mktime(0,0,0,$n_month,$n_day,$n_year);
	return FormatDateTime($filter,$nextday);
}









function GetPrevDay($filter,$time=0) {
	//this functions gets the previous valid day date from the one supplied.
	if ($time == 0) {
		$time = time();
	}
	
	$day = date("d",$time);
	$month = date("m",$time);
	$year = date("Y",$time);
		
	$p_day = $day - 1;  //decrease the date
	if (checkdate($month,$p_day,$year)) {  //check if that is all that needs to be done
		$p_month = $month;
		$p_year = $year;
	} else { //do some more changes
		if ($month > 1) {  //if so, then only decrease the month and thats it, then find the last day in that month below
			$p_month = $month - 1;
			$p_year = $year;
		} else { //decrease the year aswell!
			$p_month = 12;
			$p_year = $year - 1;
		}
		//find the last day in the month, could be 30,31 or 28 or 29...
		$p_day = 32;
		do {
			$p_day--;
		} while (!checkdate($p_month,$p_day,$p_year));
	}
	$prevday = mktime(0,0,0,$p_month,$p_day,$p_year);
	return FormatDateTime($filter,$prevday);	
}








function GetStartOfNextMonth($filter,$time=0) {
	//this function gets the 1st of the next month from the date supplied.
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$next = GetNextMonth("",$time);
//	echo '<hr />Next month: ' . date("Y-m-d",$next) . '<hr />';
	$first = mktime(0,0,0,date("m",$next),1,date("Y",$next));
//	echo '<hr />Next month1: ' . date("Y-m-d",$first) . '<hr />';
	return FormatDateTime($filter,$first);
	
}

function GetEndOfNextMonth($filter,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$next = GetNextMonth("",$time);
	$month = date("m",$next);
	$year = date("Y",$next);
	$day = 32;
	do {
		$day--;
	} while (!checkdate($month,$day,$year));
	$last = mktime(0,0,0,$month,$day,$year);
	$nextmonth = $last;

	return FormatDateTime($filter,$nextmonth);
	
}




function GetStartOfThisMonth($filter,$time=0) {
	//this function gets the 1st of the next month from the date supplied.
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$first = mktime(0,0,0,date("m",$time),1,date("Y",$time));
	return FormatDateTime($filter,$first);
	
}




function GetEndOfThisMonth($filter,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$month = date("m",$time);
	$year = date("Y",$time);
	$day = 32;
	do {
		$day--;
	} while (!checkdate($month,$day,$year));
	$last = mktime(0,0,0,$month,$day,$year);

	return FormatDateTime($filter,$last);
	
}













function GetStartOfLastMonth($filter,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$last = GetPrevMonth("",$time);
	$first = mktime(0,0,0,date("m",$last),1,date("Y",$last));
	$lastmonth = $first;

	return FormatDateTime($filter,$lastmonth);
	
}

function GetEndOfLastMonth($filter,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//search for the next.
	$lastm = GetPrevMonth("",$time);
	$month = date("m",$lastm);
	$year = date("Y",$lastm);
	$day = 32;
	do {
		$day--;
	} while (!checkdate($month,$day,$year));
	$last = mktime(0,0,0,$month,$day,$year);
	$lastmonth = $last;

	return FormatDateTime($filter,$lastmonth);
	
}












function GetStartOfNextWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//0 for sunday, 7 for saturday...
	//if today is 2, and the start is spec'd at 2... i don't know where i was going with that.
	
	for ($x=0;$x<7;$x++) {
		$time = GetNextDay("",$time);
		if (date("w",$time) == $dayofweek) {
			$nextweek = $time;
		}
	}

	return FormatDateTime($filter,$nextweek);
}


function GetEndOfNextWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//0 for sunday, 7 for saturday...
	//if today is 2, and the start is spec'd at 2... i don't know where i was going with that.
	$nextweekstart = GetStartOfNextWeek("",$dayofweek,$time);
	$time = $nextweekstart;
	for ($x=0;$x<7;$x++) {
		$time = GetNextDay("",$time);
		if (date("w",$time) == $dayofweek) {
			$endofnextweek = $lastiteration;
		}
		$lastiteration = $time;
	}

	return FormatDateTime($filter,$endofnextweek);
}
















function GetStartOfThisWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//0 for sunday, 7 for saturday...
	//if today is 2, and the start is spec'd at 2... i don't know where i was going with that.
	
	if (date("w",$time) == $dayofweek) {
		//today is the start.. strip times.
		$startofthisweek = mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time));
	} else {
		// start is in previous
		for ($x=0;$x<7;$x++) {
			$time = GetPrevDay("",$time);
			if (date("w",$time) == $dayofweek) {
				$startofthisweek = $time;
			}
		}
	}
	return FormatDateTime($filter,$startofthisweek);
}


function GetEndOfThisWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	//i'm going to cheat here..
	$endofthisweek = GetPrevDay("",GetStartOfNextWeek("",$dayofweek,$time));

	return FormatDateTime($filter,$endofthisweek);
}









function GetStartOfLastWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	
	//cheating here again...
	$startoflastweek = GetStartOfThisWeek("",$dayofweek,GetPrevDay("",GetStartOfThisWeek("",$dayofweek,$time)));	
		
	return FormatDateTime($filter,$startoflastweek);
}


function GetEndOfLastWeek($filter,$dayofweek=0,$time=0) {
	if ($time == 0) {
		$time = time();
	}
	//i'm going to cheat here..
	$endoflastweek = GetPrevDay("",GetStartOfThisWeek("",$dayofweek,$time));	

	return FormatDateTime($filter,$endoflastweek);
}











function AddDays($filter,$days,$time=0) {
	$secs = ($days * 24 * 60 * 60);
	$newtime = $time + $secs;
	return FormatDateTime($filter,$newtime);
}
function SubDays($filter,$days,$time=0) {
	$secs = ($days * 24 * 60 * 60);
	$newtime = $time - $secs;
	return FormatDateTime($filter,$newtime);
}


function AddWeeks($filter,$weeks,$time=0) {
	$secs = ($weeks * 7 * 24 * 60 * 60);
	$newtime = $time + $secs;
	return FormatDateTime($filter,$newtime);
}
function SubWeeks($filter,$weeks,$time=0) {
	$secs = ($weeks * 7 * 24 * 60 * 60);
	$newtime = $time - $secs;
	return FormatDateTime($filter,$newtime);
}


function AddMonths($filter,$months,$time=0) {
	for ($x=0;$x<$months;$x++) {
		$time = GetNextMonth("",$time);
	}
	return FormatDateTime($filter,$time);
}
function SubMonths($filter,$months,$time=0) {
	for ($x=0;$x<$months;$x++) {
		$time = GetPrevMonth("",$time);
	}
	return FormatDateTime($filter,$time);
}


function AddYears($filter,$years,$time=0) {
	//Function gets the date plus $years.
	//If the date is perhaps 29-2-year, which may not occure every year, the function will get the previous day(s).
	$newtime = mktime(date("H",$time),date("i",$time),date("s",$time),date("m",$time),date("d",$time),(date("Y",$time)+$years));
	while (!checkdate(date("m",$newtime),date("d",$newtime),date("Y",$newtime))) {
		$newtime = GetPrevDay("",$newtime);
	}
	return FormatDateTime($filter,$newtime);
}
function SubYears($filter,$years,$time=0) {
	//Function gets the date minus $years.
	//If the date is perhaps 29-2-year, which may not occure every year, the function will get the next day(s).
	$newtime = mktime(date("H",$time),date("i",$time),date("s",$time),date("m",$time),date("d",$time),(date("Y",$time)-$years));
	while (!checkdate(date("m",$newtime),date("d",$newtime),date("Y",$newtime))) {
		$newtime = GetNextDay("",$newtime);
	}
	return FormatDateTime($filter,$newtime);
}



function AddMins($filter,$minutes,$time=0) {
	$secs = ($minutes * 60);
	$newtime = $time + $secs;
	return FormatDateTime($filter,$newtime);
}
function SubMins($filter,$minutes,$time=0) {
	$secs = ($minutes * 60);
	$newtime = $time - $secs;
	return FormatDateTime($filter,$newtime);
}



function AddHours($filter,$hours,$time=0) {
	$secs = ($hours * 60 * 60);
	$newtime = $time + $secs;
	return FormatDateTime($filter,$newtime);
}
function SubHours($filter,$hours,$time=0) {
	$secs = ($hours * 60 * 60);
	$newtime = $time - $secs;
	return FormatDateTime($filter,$newtime);
}









function GetDateDiffWeeks($date1,$date2=0) {
	if ($date2 == 0) {
		$date2 = time();
	}

	if ($date1 < $date2) {
		$lesser_date = $date1;
		$greater_date = $date2;
	} else {
		$lesser_date = $date2;
		$greater_date = $date1;
	}
	
	$lesser_date = mktime(0,0,0,date("m",$lesser_date),date("d",$lesser_date),date("Y",$lesser_date));
	$greater_date = mktime(0,0,0,date("m",$greater_date),date("d",$greater_date),date("Y",$greater_date));
	
	if ($lesser_date == $greater_date) {
		//dates are the same, 
		$difference = 0;
	} else {
		//dates are different..
		$difference = floor((($greater_date - $lesser_date) / (7 * 24 * 60 * 60)));
	}

	return ($difference);
}






function GetDateDiffDays($date1,$date2=0) {
	if ($date2 == 0) {
		$date2 = time();
	}

	if ($date1 < $date2) {
		$lesser_date = $date1;
		$greater_date = $date2;
	} else {
		$lesser_date = $date2;
		$greater_date = $date1;
	}
	
	$lesser_date = mktime(0,0,0,date("m",$lesser_date),date("d",$lesser_date),date("Y",$lesser_date));
	$greater_date = mktime(0,0,0,date("m",$greater_date),date("d",$greater_date),date("Y",$greater_date));
	
	if ($lesser_date == $greater_date) {
		//dates are the same, 
		$difference = 0;
	} else {
		//dates are different..
		$difference = (($greater_date - $lesser_date) / (24 * 60 * 60));
	}

	return ($difference);
}






function GetDateDiffMins($date1,$date2=0) {
	if ($date2 == 0) {
		$date2 = time();
	}

	if ($date1 < $date2) {
		$lesser_date = $date1;
		$greater_date = $date2;
	} else {
		$lesser_date = $date2;
		$greater_date = $date1;
	}

	$lesser_date = mktime(date("H",$lesser_date),date("i",$lesser_date),0,date("m",$lesser_date),date("d",$lesser_date),date("Y",$lesser_date));
	$greater_date = mktime(date("H",$greater_date),date("i",$greater_date),0,date("m",$greater_date),date("d",$greater_date),date("Y",$greater_date));
	
	if ($lesser_date == $greater_date) {
		//dates are the same, 
		$difference = 0;
	} else {
		//dates are different..
		$difference = (($greater_date - $lesser_date) / (60));
	}

	return ($difference);
}





function GetDateDiffHours($date1,$date2=0) {
	if ($date2 == 0) {
		$date2 = time();
	}

	if ($date1 < $date2) {
		$lesser_date = $date1;
		$greater_date = $date2;
	} else {
		$lesser_date = $date2;
		$greater_date = $date1;
	}

	$lesser_date = mktime(date("H",$lesser_date),0,0,date("m",$lesser_date),date("d",$lesser_date),date("Y",$lesser_date));
	$greater_date = mktime(date("H",$greater_date),0,0,date("m",$greater_date),date("d",$greater_date),date("Y",$greater_date));
	
	if ($lesser_date == $greater_date) {
		//dates are the same, 
		$difference = 0;
	} else {
		//dates are different..
		$difference = (($greater_date - $lesser_date) / (60 * 60));
	}

	return ($difference);
}




















function FormatDateTime($filter,$time) {
	if (strlen($filter) == 0) {
		//return time only
		return $time;
	} else {
		$t = date($filter,$time);
		return $t;
	}
}









?>