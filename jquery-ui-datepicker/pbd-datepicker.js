jQuery(document).ready(function($) {
	$( ".datepicker").datepicker({ dateFormat: 'dd/mm/y' });
	$(".ironmen_event_reminders_list_btn").on('click',function(i){
		console.log("add reminder was called");
		var controlString = 
			'<div><input type="text" style="width:50%"></input><input type="text" style="width:50%"></input><a class="ironmen_event_reminders_list_remove_btn page-title-action" onclick="ironmen_event_removeReminder(this)">remove</a></div>';
		$(".ironmen_event_reminders_list").append(controlString);			
	});	
});


function ironmen_event_removeReminder(i){
	//gets the list html element
	var element = jQuery(i)[0].parentElement;
	console.log("remove button pressed");
	jQuery(element).remove();
}