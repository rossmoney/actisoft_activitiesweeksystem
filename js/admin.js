// JavaScript Document
$.ajaxSetup ({  
    cache: false  
});

//begin admin nav functions

var currentAnchor = null;  
//Function which chek if there are anchor changes, if there are, sends the ajax petition  
function checkAdminPaneAnchor(){  
    //Check if it has changes  
    if(currentAnchor != document.location.hash){  
        currentAnchor = document.location.hash;  
        //if there is not anchor, the loads the default section  
		var panename;
        if(!currentAnchor)  
            panename = "adminhome";  
        else  
        {  
            //Creates the  string callback. This converts the url URL/#main&id=2 in URL/?section=main&id=2  
            var splits = currentAnchor.substring(1).split('&');  
            //Get the section  
            var panename = splits[0];  
            delete splits[0];  
            //Create the params string  
            var params = splits.join('&');  
        }  
		showAdminPane(panename, true);
    }  
}  

function showAdminPane(panename, wipemessage)
{
	if(!checkAndForceLogout('Admin'))
	{
		$('#adminpanecontent').load('adminajax.php?function=loadpane&pane='+panename, null, function(){																										
			for(var i = 0; i < adminpanes.length; i++)
			{
				document.getElementById(adminpanes[i]).className = '';
			}																							 
			var div = document.getElementById(panename);
			if(div != null) div.className = 'adminmenu_active';
			if(wipemessage != false) document.getElementById('messagebox').innerHTML = '';
		});	
	}
}

//end admin nav functions
//begin admin activity functions
function beginActivityEdit(activityid)
{
	if(!checkAndForceLogout('Admin'))
	{
	$('#contentloader').load('adminajax.php?function=getactivity&actid='+activityid, null, function(){
		var formcontents = document.getElementById('contentloader').innerHTML.split("#");
		document.getElementById('contentloader').innerHTML = '';
		document.getElementById('name').value = formcontents[1];
		document.getElementById('desc').value = formcontents[2];
		document.getElementById('additionalinfo').value = formcontents[3];
		document.getElementById('teacher').value = formcontents[4];
		document.getElementById('maxstudents').value = formcontents[5];
		document.getElementById('cost').value = formcontents[7];
		var years = formcontents[8].split("|");
		var list = document.getElementById('yearsavailable');
		for(var i = 0; i < list.options.length; i++) {
			list.options[i].selected = false;
		}
		for(i = 0; i < list.options.length; i++) {
			for(i2 = 0; i2 < years.length; i2++)
			{
				if (list.options[i].value == years[i2]) {
   				 	list.options[i].selected = true;
 				}
			}
		}
		if(formcontents[9] == 1) document.getElementById('onsite').checked = true;
		if(formcontents[9] == 0) document.getElementById('onsite').checked = false;
		var formsneeded = formcontents[10].split("|");
		var list = document.getElementById('formsneeded');
		if(list != null)
		{
		for(var i = 0; i < list.options.length; i++) {
			list.options[i].selected = false;
		}
		for(i = 0; i < list.options.length; i++) {
			for(i2 = 0; i2 < years.length; i2++)
			{
				if (list.options[i].value == formsneeded[i2]) {
   				 	list.options[i].selected = true;
 				}
			}
		}
		}
		var list = document.getElementById('daysavailable');
		for(i = 0; i < list.options.length; i++) {
			list.options[i].selected = false;
		}
		var starts = formcontents[11] - 1;
		var duration = formcontents[12] - 1;
		for(i = starts; i <= (starts + duration); i++) {
			 list.options[i].selected = true;
		}
		document.getElementById('actid').value = formcontents[0];
		document.getElementById('adminform').style.display = '';	
		document.getElementById('messagebox').innerHTML = '';
		document.getElementById('activitysubmit').value = 'Edit Activity';
		window.scroll(0, 170);
	});
	}
}

function addActivity()
{
	if(!checkAndForceLogout('Admin'))
	{
	document.getElementById('actid').value = '';
	document.getElementById('adminform').style.display = '';
	document.getElementById('name').value = '';
	document.getElementById('desc').value = '';
	document.getElementById('additionalinfo').value = '';
	document.getElementById('teacher').value = '';
	document.getElementById('maxstudents').value = '';
	document.getElementById('cost').value = '';
	document.getElementById('onsite').checked = true;
	var list = document.getElementById('formsneeded');
	for(i = 0; i < list.options.length; i++) {
		list.options[i].selected = false;
	}
	var list = document.getElementById('daysavailable');
	for(i = 0; i < list.options.length; i++) {
		list.options[i].selected = false;
	}
	var list = document.getElementById('yearsavailable');
	for(i = 0; i < list.options.length; i++) {
		list.options[i].selected = false;
	}
	document.getElementById('activitysubmit').value = 'Add Activity';
	}
}

function validateActivityForm()
{
	if(!checkAndForceLogout('Admin'))
	{
	var errors = 0;
	var message = 11;
	if(document.getElementById('name').value == '') errors++;
	if(document.getElementById('desc').value == '') errors++;
	if(document.getElementById('teacher').value == '') errors++;
	if(document.getElementById('maxstudents').value == '') errors++;
	if (!isInteger(document.getElementById('maxstudents').value))
	{
		errors++;
		message = 13;
	}
	if(document.getElementById('cost').value == '') errors++;
	if (!isInteger(document.getElementById('cost').value))
	{
		errors++;
		message = 13;
	}
										   
	var selnum = 0;
	var lastselected = 0;
	var list = document.getElementById('yearsavailable');
	for(i = 0; i < list.options.length; i++) {
		if(list.options[i].selected == true) {
			selnum++;
		}
	}
	if(selnum == 0) errors++;
	selnum = 0;
	var list = document.getElementById('daysavailable');
	for(i = 0; i < list.options.length; i++) {
		if(list.options[i].selected == true) {
			if(selnum > 0)
			{
				var diff = (list.options[i].value - list.options[lastselected].value);
				if(diff > 1)
				{
					errors++;
					message = 43;
				}
			}
			lastselected = i;
			selnum++;
		}
	}
	if(selnum == 0) errors++;
	if(errors == 0)
	{
		document.getElementById('messagebox').innerHTML = '';
		return true;
	} else {
		$('#messagebox').load('messagegenerator.php?message='+message);
		return false;
	}
	}
}

function deleteActivity(activityid)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to delete this activity?")) { 
			$('#contentloader').load('adminajax.php?function=deleteactivity&actid='+activityid, null, function(){	
				document.getElementById('activity_'+activityid).innerHTML = '';	
				if(document.getElementById('actid').value == activityid)
				{
					document.getElementById('adminform').style.display = 'none';
					document.getElementById('activitysubmit').value = '';
				}
				$('#messagebox').load('messagegenerator.php?message=16');
			});
		}
	}
}

//end admin activity functions
//start admin booking functions
function updateGroupList(callbackfunc)
{
	if(!checkAndForceLogout('Admin'))
	{
		if(callbackfunc == 'newuser')
		{
			var list = document.getElementById('new_year');
			var year = list.options[list.selectedIndex].value;
			$('#newuser_groupselect').load('adminajax.php?function=generategrouplist&year='+year+'&newuser=Y');
		} else { 
			var list = document.getElementById('year');
			var year = list.options[list.selectedIndex].value;
			$('#groupselect').load('adminajax.php?function=generategrouplist&year='+year+'&callbackfunc='+callbackfunc);
		}
	}
}

function updateUserList(callbackfunc)
{
	if(!checkAndForceLogout('Admin'))
	{
		var div = document.getElementById('editbookingscreen');
		if(div != null) div.innerHTML = '';
		var div = document.getElementById('pendingpaperwork');
		if(div != null) div.innerHTML = '';
		var div = document.getElementById('pendingpayments');
		if(div != null) div.innerHTML = '';
		var div = document.getElementById('edituserscreen');
		if(div != null) div.innerHTML = '';
		var list = document.getElementById('year');
		var year = list.options[list.selectedIndex].value;
		list = document.getElementById('group');
		var group = list.options[list.selectedIndex].value;
		$('#userselect').load('adminajax.php?function=generateuserlist&year='+year+'&group='+group+'&callbackfunc='+callbackfunc);
	}
}

function getUserReturns()
{
    if(!checkAndForceLogout('Admin'))
	{
		returnUserPayments();
    	returnUserPaperwork();
	}
}

function editBooking()
{
	if(!checkAndForceLogout('Admin'))
	{
	document.getElementById('editbookingscreen').innerHTML = '';
	var list = document.getElementById('user');
	var userid = list.options[list.selectedIndex].value;
	$('#editbookingscreen').load('adminajax.php?function=editbooking&userid='+userid, null, function(){
		var activities = document.getElementById('activityinfo').innerHTML;
        activities = JSON.parse(activities);
		var selectedactivities = document.getElementById('selectedactivities').value;	
		var activityfound;
		if(selectedactivities != "")
		{
			selectedactivities = selectedactivities.split("|");
			document.getElementById('userbookingdata').innerHTML = '';
			for(var i4 = 0; i4 < selectedactivities.length; i4++)
			{
				list = document.getElementById(weekdays[i4]+'_select');
				activityfound = false;
				for(var i2 = 0; i2 < list.options.length; i2++)
				{
					if(parseInt(list.options[i2].value) == parseInt(selectedactivities[i4]))
					{
						list.options[i2].selected = true;
						var actid = findSelectedActivity(activities, parseInt(selectedactivities[i4]));
						showActivityDetails(i4, activities[actid], 'no');
						past_activities[i4] = actid;
						activityfound = true;
						nextbookingday = i4 + parseInt(activities[actid].duration);
						for(var i = (i4 + 1); i < nextbookingday; i++)
						{
							 blankListBlock(i);
						}
					}
				}
			}
		}
	});
	}
}

function deleteBooking(userid)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to delete this booking?")) { 
			$('#contentloader').load('adminajax.php?function=deletebooking&uid='+userid, null, function(){	
				document.getElementById('editbookingscreen').innerHTML = '';
				$('#messagebox').load('messagegenerator.php?message=15');
			});
		}
	}
}
//end admin booking functions
//start admin paperwork functions
function addPaperwork()
{
	if(!checkAndForceLogout('Admin'))
	{
	document.getElementById('papid').value = '';
	document.getElementById('adminform').style.display = '';
	document.getElementById('name').value = '';
	document.getElementById('url').value = '';
	document.getElementById('formfile').value = '';
	document.getElementById('loc_link').checked = true;
	dispURLBox();
	document.getElementById('paperworksubmit').value = 'Add Form';
	}
}

function validatePaperworkForm()
{
	if(!checkAndForceLogout('Admin'))
	{
	var errors = 0;
	var message = 11;
	if(document.getElementById('name').value == '') errors++;
	if(document.getElementById('loc_link').checked == true && document.getElementById('url').value == '') {
		errors++;
	}
	if(document.getElementById('loc_embed').checked == true && document.getElementById('formfile').value == ''){
		errors++;
	}
	if(errors == 0)
	{
		document.getElementById('messagebox').innerHTML = '';
		return true;
	} else {
		$('#messagebox').load('messagegenerator.php?message='+message);
		return false;
	}
	}
}

function beginPaperworkEdit(paperworkid)
{
	if(!checkAndForceLogout('Admin'))
	{
	$('#contentloader').load('adminajax.php?function=getpaperwork&papid='+paperworkid, null, function(){
		var formcontents = document.getElementById('contentloader').innerHTML.split(",");
		document.getElementById('contentloader').innerHTML = '';
		if(formcontents[2].substring(0,4) == 'http')
		{
			document.getElementById('url').value = formcontents[2];
			document.getElementById('loc_link').checked = true;
			dispURLBox();
		} else {
			//document.getElementById('formfile').value = formcontents[2];
			document.getElementById('loc_embed').checked = true;
			dispUploadBox();
		}
		document.getElementById('name').value = formcontents[1];
		document.getElementById('papid').value = formcontents[0];
		document.getElementById('adminform').style.display = '';	
		document.getElementById('messagebox').innerHTML = '';
		document.getElementById('paperworksubmit').value = 'Edit Form';
	});
	}
}

function deletePaperwork(paperworkid)
{
	if(!checkAndForceLogout('Admin'))
	{
	
	if (confirm("Are you sure you want to delete this paperwork?")) { 
	$('#contentloader').load('adminajax.php?function=deletepaperwork&papid='+paperworkid, null, function(){	
		if(document.getElementById('contentloader').innerHTML == 'used')
		{
			$('#messagebox').load('messagegenerator.php?message=20');
		} else {
			document.getElementById('form_'+paperworkid).innerHTML = '';	
			if(document.getElementById('papid').value == paperworkid)
			{
				document.getElementById('adminform').style.display = 'none';
				document.getElementById('activitysubmit').value = '';
			}
			$('#messagebox').load('messagegenerator.php?message=14');
		}
		document.getElementById('contentloader').innerHTML = '';
	});
	}
	
	}
}

function dispUploadBox()
{
	if(!checkAndForceLogout('Admin'))
	{
	document.getElementById('formfile').style.display = '';
	document.getElementById('url').style.display = 'none';
	}
}

function dispURLBox()
{
	if(!checkAndForceLogout('Admin'))
	{
	document.getElementById('url').style.display = '';
	document.getElementById('formfile').style.display = 'none';
	}
}

function returnUserPaperwork()
{
	var list = document.getElementById('user');
	var userid = list.options[list.selectedIndex].value;
	$('#pendingpaperwork').load('adminajax.php?function=getpendingpaperwork&uid='+userid);
}

//end admin paperwork functions

//start admin report functions
function showReport(reporttype, reportformat, refreshreport)
{
	if(reporttype == undefined) reporttype = $('#reporttype').val();
	if(reportformat == undefined) reportformat = $('#reportformat').val();
	if(refreshreport == undefined) refreshreport = '';
	if(reportformat == "html")
	{
		$('#reportplaceholder').load('adminajax.php?function=embedhtmlreport&reporttype='+reporttype+'&refresh='+refreshreport);
	} else {
		if(reportformat == "htmldirect") reportformat = "html";
		window.open('reportgen.php?reporttype=' + reportformat + '&reportid=' + reporttype + '&refresh=' + refreshreport + '&directout=Yes');
	}
}

function printReport()
{
	if(!checkAndForceLogout('Admin'))
	{
	if(detectIE())
	{
		document.frames ('reportout').focus ();
		document.frames ('reportout').print ();
	} else {
		var iframe = document.getElementById('reportout');
		iframe.contentWindow.print();
	}
	}
}
//end admin report functions

//start admin payment functions
function returnUserPayments()
{
	var list = document.getElementById('user');
	var userid = list.options[list.selectedIndex].value;
	$('#pendingpayments').load('adminajax.php?function=getpendingpayments&uid='+userid);
}

function removePendingPayment(aid, uid, full)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to continue?"))
		{
			var destpage = null;
			if(full)
			{
				destpage = 'adminajax.php?function=clearpayments&uid='+uid;
			} else {
				destpage = 'adminajax.php?function=removependingpayment&uid='+uid+'&aid='+aid; 
			}
			$('#contentloader').load(destpage, null, function(){
				$('#pendingpayments').load('adminajax.php?function=getpendingpayments&uid='+uid, null, function() {
					$('#messagebox').load('messagegenerator.php?message=31');																			
				});	
			});
		} else {
			if(full)
			{
				document.getElementById('paymentchk').checked = false;
			} else {
				document.getElementById('subpaymentchk_'+aid).checked = false;
			}
		}
	}
}

function removePendingForm(uid, act_id, form_id)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to continue?"))
		{
			$('#contentloader').load('adminajax.php?function=removependingform&uid='+uid+'&formid='+form_id+'&actid='+act_id, null, function(){																																
				$('#pendingpaperwork').load('adminajax.php?function=getpendingpaperwork&uid='+uid, null, function() {
					$('#messagebox').load('messagegenerator.php?message=29');																			
				});
			});
		} else {
			document.getElementById('form_'+uid+'_'+act_id+'_'+form_id).checked = false;
		}
	}
}

//end admin payment functions

//begin admin users functions dec 2010
function deleteBlockedUser(username, uid)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to unblock this user?")) { 
			$('#contentloader').load('adminajax.php?function=deleteblockeduser&user='+username, null, function(){	
				document.getElementById('blocked_user_'+uid).innerHTML = '';
				$('#messagebox').load('messagegenerator.php?message=37');
			});
		}
	}
}

function addBlockedUser()
{
	if(!checkAndForceLogout('Admin'))
	{
		var username = $('#name').val();
		if(username == '')
		{
			$('#messagebox').load('messagegenerator.php?message=11');
		} else {
			if (confirm("Are you sure you want to block this user?")) { 
				$('#contentloader').load('adminajax.php?function=addblockeduser&user='+username, null, function(){	
					showAdminPane('Users', false);
				});
			}
		}
	}
}

function getUserEditor(adduser)
{
	if(adduser)
	{
		$('#edituserscreen').load('adminajax.php?function=showusereditor&add=TRUE');
	} else {
		document.getElementById('edituserscreen').innerHTML = '';
		var list = document.getElementById('user');
		var userid = list.options[list.selectedIndex].value;
		$('#edituserscreen').load('adminajax.php?function=showusereditor&userid='+userid);
	}
	$('#edituserscreen').show();
}

function deleteUser(uid)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to delete this user?")) { 
			$('#contentloader').load('adminajax.php?function=deleteuser&user='+uid, null, function(){	
				showAdminPane('Users', false);																				   
				$('#messagebox').load('messagegenerator.php?message=39');
			});
		}
	}
}

function validateUserDetailsForm()
{
	if(!checkAndForceLogout('Admin'))
	{
		var errors = 0;
		var message = 11;
		if(document.getElementById('new_username').value == '') errors++;
		if(document.getElementById('new_firstname').value == '') errors++;
		if(document.getElementById('new_lastname').value == '') errors++;
		if(document.getElementById('new_year').value == '') errors++;
		if(document.getElementById('new_admin').value == '') errors++;
		if(errors == 0)
		{
			document.getElementById('messagebox').innerHTML = '';
			return true;
		} else {
			$('#messagebox').load('messagegenerator.php?message='+message);
			return false;
		}
	}
}

function validateUserCSVForm()
{
	if(!checkAndForceLogout('Admin'))
	{
	var errors = 0;
	var message = 11;
	if(document.getElementById('csvfile').value == '') errors++;
	if(errors == 0)
	{
		document.getElementById('messagebox').innerHTML = '';
		return true;
	} else {
		$('#messagebox').load('messagegenerator.php?message='+message);
		return false;
	}
	}
}
//end admin users functions dec 2010

//start admin functions may 2010

function resetSystem()
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to reset the places taken to 0 for all activities and delete all bookings?\n\nThis will also delete:\nForms that need to be returned.\nPayments that need to be returned.")) { 
			$('#contentloader').load('adminajax.php?function=resetsystem', null, function(){	
				showAdminPane('Settings', false);																				   
				$('#messagebox').load('messagegenerator.php?message=44');
			});
		}
	}
}

function validateSettingsEditForm()
{
	if(!checkAndForceLogout('Admin'))
	{
	var errors = 0;
	var message = 11;
	if(document.getElementsByName('online_start_time')[0].value == '') errors++;
	if(document.getElementsByName('online_end_time')[0].value == '') errors++;
	if(document.getElementsByName('session_time')[0].value == '') errors++;
	if(document.getElementsByName('timetokeepreports')[0].value == '') errors++;
	if(document.getElementsByName('weekduration')[0].value == '') errors++;
	if(document.getElementsByName('checkouttext')[0].value == '') errors++;
	
	if(document.getElementsByName('parentpay_OrgId')[0].value == '') errors++;
	if(document.getElementsByName('parentpay_UserId')[0].value == '') errors++;
	if(document.getElementsByName('parentpay_ServiceId')[0].value == '') errors++;
	if(errors == 0)
	{
		document.getElementById('messagebox').innerHTML = '';
		return true;
	} else {
		$('#messagebox').load('messagegenerator.php?message='+message);
		return false;
	}
	}
}

function deductPayment(userid)
{
	if(!checkAndForceLogout('Admin'))
	{
		var amount = document.getElementById('deductamount').value;
		if(amount == '')
		{
			$('#messagebox').load('messagegenerator.php?message=11');
		} else {
			$('#contentloader').load('adminajax.php?function=deductpayment&amount='+amount+'&uid='+userid, null, function() {																																											   				//$('#contentloader').html('');
				$('#pendingpayments').load('adminajax.php?function=getpendingpayments&uid='+userid, null, function() {
					$('#messagebox').load('messagegenerator.php?message=45');																					
				});																																																
			});
		}
	}
}

//end admin functions may 2010

//start years & groups functions 
function addYear()
{
	if(!checkAndForceLogout('Admin'))
	{
		if($('#newyear').val() == '')
		{
			$('#messagebox').load('messagegenerator.php?message=11');
		} else {
			$('#messageloader').load('adminajax.php?function=addyear&year='+$('#newyear').val(), null, function(){	
					showAdminPane('Groups', false);	
			});
		}
	}
}

function deleteYear(year)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to delete this year?")) { 
			$('#messageloader').load('adminajax.php?function=deleteyear&year='+year, null, function(){	
				$('#mod_year_'+year).html('');
			});
		}
	}
}

function addGroup()
{
	if(!checkAndForceLogout('Admin'))
	{
		if($('#newgroup').val() == '')
		{
			$('#messagebox').load('messagegenerator.php?message=11');
		} else {
			$('#messageloader').load('adminajax.php?function=addgroup&group='+$('#newgroup').val(), null, function(){	
					showAdminPane('Groups', false);
			});
		}
	}
}

function deleteGroup(group)
{
	if(!checkAndForceLogout('Admin'))
	{
		if (confirm("Are you sure you want to delete this group?")) { 
			$('#messageloader').load('adminajax.php?function=deletegroup&group='+group, null, function(){	
				if($('#messageloader').html() == 'inuse')
				{
					$('#messagebox').load('messagegenerator.php?message=49');
				} else {
					$('#mod_group_'+group).html('');
				}
				$('#messageloader').html('');
			});
		}
	}
}

function beginYearEdit(year)
{
	if(!checkAndForceLogout('Admin'))
	{
		$('#yeareditor').load('adminajax.php?function=showyeareditor&year='+year);
	}	
}

function saveYear(year)
{
	if(!checkAndForceLogout('Admin'))
	{
		if($('#edit_newyear').val() == "")
		{
			$('#messagebox').load('messagegenerator.php?message=11');
		} else {
			var ob = document.getElementById('groupselector');
			selected = new Array();
			for (var i = 0; i < ob.options.length; i++)
			{
				if (ob.options[i].selected) selected.push(ob.options[i].value);
			}
			$('#messageloader').load('adminajax.php?function=saveyear&year='+year+'&newgroups='+escape(selected.join("|"))+'&newyear='+$('#edit_newyear').val(), null, function(){
				$('#messagebox').load('messagegenerator.php?message=50');
				showAdminPane('Groups', false);
			});	
		}
	}	
}
