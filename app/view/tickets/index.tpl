
<div class="well">
	<h4>Tickets</h4>
	Lieu  : 
	<select id="lieu_id" onChange="getTickets();">
		<option></option>
		{foreach $Lieux as $Lieu}
		<option value="{$Lieu.id}">{$Lieu.name}</option>
		{/foreach}
	</select>
	<hr/>
	<div id="tickets_list"></div>
</div>

<script>
<!--
function getTickets(){
		
	if( $('#lieu_id').val() != ''){
		$('#tickets_list').html('<i class="icon-spinner"></i>');
		$(document).ready(function () {
		$.get("{$config.url}{$config.url_dir}index.php/tickets/getByLieu?nohtml", { lieuid: $('#lieu_id').val()  },
		function success(data){
		$('#tickets_list').html(data);
		});
	});
	}	
}

function getTicketsByOrder(order){
	
	if( $('#lieu_id').val() != ''){
		$('#tickets_list').html('<i class="icon-spinner"></i>');
		$(document).ready(function () {
		$.get("{$config.url}{$config.url_dir}index.php/tickets/getByLieu?nohtml", { lieuid: $('#lieu_id').val(), order: order  },
		function success(data){
		$('#tickets_list').html(data);
		});
	});
	}	
}
//-->
</script>