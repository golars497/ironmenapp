jQuery(document).ready(function($) {
	$( ".datepicker").datepicker({ dateFormat: 'dd/mm/y' });
});

function ironmen_event_addReminder(){
	console.log("add reminder was called");
	//gets the current number of reminders and gets the last one
	//we do this because the last reminder always has the highest index
	//which we need to know in order to determine the next index
	var reminders = jQuery('.ironmen_event_reminders_list div')
	if (reminders.length > 0) {  
		reminders = jQuery(reminders[reminders.length - 1]);
		var input_name = reminders.find('input').attr("name");
		var input_index = input_name.match(/\d/g); //gets number within string
		input_index = parseInt(input_index);
	} else {
		input_index = 0;
	}
	var controlString = '<div><input type="text" name="event_notif_days[' + (input_index + 1) + '][number]" value="" class="input-metabox" style="width:30%"></input><select type="text" name="event_notif_days[' + (input_index + 1) + '][unit]" value="" class="select-metabox" style="width:30%"><option value="days" selected>days</option><option value="weeks">weeks</option></select><a class="ironmen_event_reminders_list_remove_btn page-title-action" onclick="ironmen_event_removeReminder(this)">remove</a></div>';
	jQuery(".ironmen_event_reminders_list").append(controlString);			
}

function ironmen_event_removeReminder(i){
	//gets the list html element
	var element = jQuery(i)[0].parentElement;
	console.log("remove button pressed");
	jQuery(element).remove();
}