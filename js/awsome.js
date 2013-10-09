// JavaScript Document
var debugmode = false;
var interval = "";
var timer_count = 0;
var tok = null;
var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var serverdate=new Date(currenttime);

$.ajaxSetup ({  
    cache: false  
});

function checkJavaScriptValidity() {
    document.getElementById("jsDisabled").style.display = 'none';
}

function padlength(what){
	var output=(what.toString().length==1)? "0"+what : what;
	return output;
}

function displaytime(){
	serverdate.setSeconds(serverdate.getSeconds()+1);
	var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds());
	document.getElementById("servertime").innerHTML=timestring;
}
	
$(document).ready(function() {
    /*if(document.title == 'awsome - Book' || document.title == 'awsome - Admin' )
	{
		setupSessionTimer();
	}*/
	setInterval("displaytime()", 1000);
	if(document.title == 'awsome - Admin' )
	{
		setInterval("checkAdminPaneAnchor()", 300);
	}
	if(document.title == 'awsome - Login' ) {
		$('#messageloader').load('ajaxcallpass.php?function=settok&tok='+Math.floor(Math.random()*(adminpanes.length - 1))+','+Math.floor(Math.random()*100), null, function() {
			tok = $('#messageloader').html();
			tok = jQuery.trim(tok);
			$('#messageloader').html('');																																							
		});
	}
});

/*function setupSessionTimer()
{
	window.clearInterval(interval);
	interval="";
	$('#timer').load('ajaxcallpass.php?function=getsession_timeelapsed', null, function() {
			timer_count = $('#timer').html();
			$('#timer-text').show();
			$('#timer').html(Math.floor(timer_count/60) + "m " + Math.floor(timer_count%60) + "s");
			if(timer_count > 0)
			{
				interval = window.setInterval("sessionTimerTickEvent()",1000);
			}
	});
}

function sessionTimerTickEvent()
{
	timer_count--;
	if(timer_count >= 0) $('#timer').html(Math.floor(timer_count/60) + "m " + Math.floor(timer_count%60) + "s");
	if (timer_count == 0)
	{
        window.clearInterval(interval);
        interval="";
		$('#messageloader').load('ajaxcallpass.php?function=removelogin', null , function() {
			$('#booknextbtn').css('disabled', 'true');
			$('#startover').css('disabled', 'true');
			$('#reeditbtn').css('disabled', 'true');
			$('#startbook').css('disabled', 'true');
			$('#messagebox').load('messagegenerator.php?message=5');
			alert ("Your session has expired. Please log in again.");
		});
	}
}*/

function checkAndForceLogout(curpage)
{
    /*$('#messageloader').html = "";
	if(document.title == 'awsome - Admin' || timer_count > 0 )
	{
		$('#messageloader').load('ajaxcallpass.php?function=updatelogintime');
	} else {
		$('#messageloader').load('ajaxcallpass.php?function=checkforcedlogout&curpage=' + escape(document.title) , null, function() {
			var message = $('#messageloader').html();																		   
			if($('#messageloader').html() != "")
			{
				alert(message);
				window.location = 'login';
				return true;
			} else {
				return false;
			}
		});
	}*/
	return false;
}

function showActivityDetails(dayid, activity, warning)
{
	document.getElementById(weekdays[dayid]+'_available').innerHTML = '';

        var details =
            "<div class=\"detailinfo\">Costs - &pound;"+activity.cost+"<br/>"+
		(parseInt(activity.maxstudents) - parseInt(activity.placestaken)) + " of " + activity.maxstudents +
                " Places Left<br/>Duration - " + activity.duration + " Day(s)</div>" +
		"<input id=\"" + weekdays[dayid] + "_cost\" type=\"hidden\" value=\"" +
                activity.cost + "\" /><div id=\"tempplace\" style=\"display: none;\"><input id=\"activityfull\" type=\"hidden\" value=\"";
                if(warning == 'no')
		{
			details = details + '5';
		} else {
			details = details + (parseInt(activity.maxstudents) - parseInt(activity.placestaken));
		}
                details = details + "\" /></div>";

        document.getElementById(weekdays[dayid]+'_details').innerHTML = details;

																																			   		var activityfull = document.getElementById('activityfull');
		if(activityfull != null)
		{
			activityfull = activityfull.value;
			document.getElementById('tempplace').innerHTML = '';
			if(activityfull == 0)
			{
				document.getElementById(weekdays[dayid]+'_select').disabled = false;
                                document.getElementById(weekdays[dayid]+'_available').innerHTML = '<div class="actnoexist"></div>';
				document.getElementById(weekdays[dayid]+'_block').style.backgroundColor = '#FFBABA';
				$('#messagebox').load('messagegenerator.php?message=3');
			} else {
				document.getElementById(weekdays[dayid]+'_available').innerHTML = '';
				document.getElementById('messagebox').innerHTML = '';
				document.getElementById(weekdays[dayid]+'_block').style.backgroundColor = '';
			}
		}
		
		$('#totalcost').load('ajaxcallpass.php?function=disptotalcost&actid='+document.getElementById(weekdays[dayid]+'_select').value, null, function(){
		
		var div;
		var totalcost = 0;
		for(i=0; i <= weekdays.length; i++) {
			div = document.getElementById(weekdays[i]+'_cost');
			if(div != null)
			{
				totalcost = totalcost + parseInt(div.value);
			}
		}
		document.getElementById('totalcost').innerHTML = 'Total Cost: &pound;'+totalcost;
		document.getElementById('totalcost').style.marginBottom = "10px";																															   		});
}

function getDayId(day)
{
	var i = 0, dayid = 0;
        for(i = 0; i < weekdays.length; i++)
	{
                if(weekdays[i] == day.toLowerCase())
		{
			dayid = i;
			break;
		}
	}
	return dayid;
}

function findSelectedActivity(activities, selectedactivity)
{
    var actid = -1;
    for(var i = 0; i < activities.length; i++) {
            if(parseInt(activities[i].id) == parseInt(selectedactivity))
                {
                    actid = i;
                    break;
                }
    }
    return actid;
}

var past_activities = []; 
			
function updateActivityList(selectid, activities )
{
	 var select = document.getElementById(weekdays[selectid]+"_select");
	 select.options.length = 0;
	 select.options[select.length] = new Option('Select Activity...', 'none');
	 select.options[select.length-1].disabled = true;
	 for(var i3=0; i3 < activities.length; i3++)
	 {
		if(parseInt(activities[i3].starts) == (selectid+1))
		{
			select.options[select.length] = new Option(activities[i3].name, activities[i3].id);
		}
		document.getElementById(weekdays[selectid]+'_block').style.display = '';
		var div = document.getElementById(weekdays[selectid]+'_details');
		if(div != null) document.getElementById(weekdays[selectid]+'_details').innerHTML = '';
	 }
}

function blankListBlock(selectid)
{
	var select = document.getElementById(weekdays[selectid]+"_select");
	select.options.length = 0;
	select.options[select.length] = new Option('Select Activity...', 'none');
	select.options[select.length-1].disabled = true;
	document.getElementById(weekdays[selectid]+'_block').style.display = 'none';
	var div = document.getElementById(weekdays[selectid]+'_details');
	if(div != null) document.getElementById(weekdays[selectid]+'_details').innerHTML = '';
}

function updateActivities(currentday)
{
        var activities = document.getElementById('activityinfo').innerHTML;
        activities = JSON.parse(activities);
		if(currentday == 'startover')
		{
			for(var i = 0; i < weekdays.length; i++)
			{
				past_activities[i] = 'none';
				updateActivityList(i, activities);
			}
		} else {
			var nextbookingday = 0;
			currentday = parseInt(currentday);
        	var select = document.getElementById(weekdays[currentday]+"_select");
			if(select.options.length != 0)
			{
				if(select.options[select.selectedIndex].value != undefined && select.options[select.selectedIndex].value != 'none')
				{
					var div = document.getElementById(weekdays[currentday]+'_details');
					if(div != null) document.getElementById(weekdays[currentday]+'_details').innerHTML = '';
					var actid = findSelectedActivity(activities, parseInt(select.options[select.selectedIndex].value));
					showActivityDetails(currentday, activities[actid]);
					nextbookingday = currentday + parseInt(activities[actid].duration);
					if(past_activities[currentday] != 'none')
					{
						past_activities[currentday] = parseInt(past_activities[currentday]);
						if(parseInt(activities[past_activities[currentday]].duration) != parseInt(activities[actid].duration))
						{
							if(parseInt(activities[past_activities[currentday]].duration) < parseInt(activities[actid].duration))
							{
								for(var i = (currentday + 1); i < nextbookingday; i++)
								{
									 blankListBlock(i);
								}
							}
							if(parseInt(activities[past_activities[currentday]].duration) > parseInt(activities[actid].duration))
							{
								var uncoverable = currentday + (parseInt(activities[past_activities[currentday]].duration) - 1);
								for(var i = nextbookingday; i <= uncoverable; i++)
								{
									updateActivityList(i, activities);
								}
							}
						}
					} else {
						if(parseInt(activities[actid].duration) > 1)
						{
								for(var i = (currentday + 1); i < nextbookingday; i++)
								{
									 blankListBlock(i);
								}
						}
					}
					past_activities[currentday] = actid;
				}
			}
		}
}

function checkBooking(submitbtn, userid)
{
    if(!checkAndForceLogout('Booking'))
	{
        var proceed = true;
		var progress = 0;
		var divnum = 0;
		var dayarray = [];
		var valuearray = [];
		for(i=0; i< weekdays.length; i++) {
			var select = document.getElementById(weekdays[i]+'_select');
			if(document.getElementById(weekdays[i]+'_block').style.display != 'none')
			{
				if(select.selectedIndex > -1)
				{
					var selectedactivity = select.options[select.selectedIndex].value;
					if(selectedactivity != 'none')
					{
						dayarray[progress] = weekdays[i];
						valuearray[progress] = select.options[select.selectedIndex].value;
						progress++;
					}
				}
				divnum++;
			}
		}
		if(progress == divnum)
		{
			var pagetoload = 'formsubmit.php?submit='+submitbtn;
			if(submitbtn == 'Edit+Booking') pagetoload = pagetoload+'&userid='+userid;
			for(i=0; i<dayarray.length; i++)
			{
				pagetoload = pagetoload+'&'+dayarray[i]+'='+valuearray[i];
			}
			$('#messageloader').load(pagetoload, null, function(){
													
				var windowloc = document.getElementById('windowloc');
				if(windowloc != null)
				{
					if(windowloc.innerHTML == "confirm")
					{
						 pagetoload = 'ajaxcallpass.php?function=printpostsummary';
						 for(i=0; i<dayarray.length; i++)
						 {
							  pagetoload = pagetoload+'&'+dayarray[i]+'='+valuearray[i];
						 }
						 $('#reviewscreen').load(pagetoload, null, function() {
							 document.getElementById('bookingfields').style.display = 'none';
							 document.getElementById('reviewscreen').style.display = '';
							 $('#step1outer').css('opacity', '0.4');
							 $('#step1inner').css('opacity', '0.4');
							 $('#step1number').css('opacity', '0.4');
							 $('#step2outer').css('opacity', '1.0');
							 $('#step2inner').css('opacity', '1.0');
							 $('#step2number').css('opacity', '1.0');
							 $('#step3outer').css('opacity', '0.4');
							 $('#step3inner').css('opacity', '0.4');
							 $('#step3number').css('opacity', '0.4');
						 });
					 } else {
						 window.location = windowloc.innerHTML;
						 if(submitbtn == 'Edit+Booking') $('#messagebox').load('messagegenerator.php?message=2');
					 }
				} else {
					var bookedup = document.getElementById('activities_bookedup');
					if(bookedup != null)
					{
						bookedup = bookedup.value;
						updateActivitiesNoLongerAvailable(bookedup);
					}	
				}
													 
			});
		} else {
			$('#messagebox').load('messagegenerator.php?message=4');
		}
	}
}

function reeditBooking()
{
    if(!checkAndForceLogout('Booking'))
	{
		document.getElementById('bookingfields').style.display = '';
    	document.getElementById('reviewscreen').style.display = 'none';
		 $('#step1outer').css('opacity', '1.0');
		 $('#step1inner').css('opacity', '1.0');
		 $('#step1number').css('opacity', '1.0');
		 $('#step2outer').css('opacity', '0.4');
		 $('#step2inner').css('opacity', '0.4');
		 $('#step2number').css('opacity', '0.4');
		 $('#step3outer').css('opacity', '0.4');
		 $('#step3inner').css('opacity', '0.4');
		 $('#step3number').css('opacity', '0.4');
	}
}

function saveBooking()
{
    if(!checkAndForceLogout('Booking'))
	{
		var proceed = false;
   	 	if (confirm("Are you sure you want to submit your booking?\n\nThis is the only chance you will have to book your activities."))
		{ 
			proceed = true;
		}
		if(proceed)
		{
			var booking_json = document.getElementById('cur_bookings').innerHTML;
    		$('#messagebox').load('ajaxcallpass.php?function=savebooking&bookings='+booking_json, null, function() {
				 if($('#messagebox').html() == "")
				 {
					 $('#bookingform').html('');
					 $('#yourdone').load('ajaxcallpass.php?function=yourdonescreen', null, function() {
						 $('#step1outer').css('opacity', '0.4');
						 $('#step1inner').css('opacity', '0.4');
						 $('#step1number').css('opacity', '0.4');
						 $('#step2outer').css('opacity', '0.4');
						 $('#step2inner').css('opacity', '0.4');
						 $('#step2number').css('opacity', '0.4');
						 $('#step3outer').css('opacity', '1.0');
						 $('#step3inner').css('opacity', '1.0');
						 $('#step3number').css('opacity', '1.0');
					 });
				 }
    		});
		}
	}
}

function activatePayScreen(activities, full)
{
	var processurl = 'ajaxcallpass.php?function=setpaymentactivities&activities='+activities;
	if(full == 'full')
	{
		processurl = processurl + '&full=true';
	}
	$('#messageloader').load(processurl, null, function() {
			window.location = 'pay';																		 
	});
}

function updateActivitiesNoLongerAvailable(activitiesbookedup, uid)
{
	var activitiesarray = activitiesbookedup.split("|");
	for(i=0; i < weekdays.length; i++)
	{
		if(activitiesarray[i] != 0)
		{
			if(document.getElementById(weekdays[i]+'_select') != null)
			{
				dayObj = eval( 'document.bookingform.'+weekdays[i]+'_select' );
				activityid = dayObj[dayObj.selectedIndex].value;
				if(activityid == activitiesarray[i])
				{
					dayObj[dayObj.selectedIndex].disabled = true;
					document.getElementById(weekdays[i]+'_available').innerHTML = '<div class="actnoexist"></div>';
					document.getElementById(weekdays[i]+'_block').style.backgroundColor = '#FFBABA';
                                        document.getElementById(weekdays[i]+'_select').disabled = false;
				}
			}
		}
	}
}

function clearSelections()
{
	if(!checkAndForceLogout('Booking'))
	{
        document.getElementById('bookingfields').style.display = '';
        document.getElementById('reviewscreen').style.display = 'none';
		for(var i=0; i< weekdays.length; i++) {
			document.getElementById(weekdays[i]+'_details').innerHTML = '';
			document.getElementById(weekdays[i]+'_block').style.backgroundColor = '';
             document.getElementById(weekdays[i]+'_available').innerHTML = '';
		}
		updateActivities('startover');
        document.getElementById('messagebox').innerHTML = '';
		document.getElementById('totalcost').innerHTML = '';
        document.getElementById('bookjsonload').innerHTML = '';
	}
}

function detectIE()
{
	var browserName=navigator.appName;
    if (browserName=="Microsoft Internet Explorer")
	{
		return true;
	} else {
		return false;
	}
}

function isInteger(val)
{
    if(val==null)
    {
        return false;
    }
    if (val.length==0)
    {
        return false;
    }
    for (var i = 0; i < val.length; i++) 
    {
        var ch = val.charAt(i);
        if (i == 0 && ch == "-")
        {
            continue;
        }
        if (ch < "0" || ch > "9")
        {
            return false;
        }
    }
    return true;
}
