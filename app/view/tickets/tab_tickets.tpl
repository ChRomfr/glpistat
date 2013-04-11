<div class="pull-right">
	{if !empty($smarty.get.order)}<a href="javascript:getTicketsByOrder('{$smarty.get.order}')"><i class="icon icon-refresh"></i></a>
	{else}<a href="javascript:getTickets()"><i class="icon icon-refresh"></i></a>
	{/if}
</div>
<div class="clearfix"></div>
<table class="table table-striped table-hover tablesorter" id="ts1">
	<thead>
		<tr>
			<th>#</th>
			<th><a href="javascript:getTicketsByOrder('u.name');">Demandeur</a></th>
			<th>Titre</th>
			<th>Categorie</th>
			<th>Status</th>
			<th><a href="javascript:getTickets();">Date</a></th>
		</tr>
	</thead>
	<tbody>
	{foreach $Tickets as $Ticket}
		<tr>
			<td><a href="{$config.glpi_url}front/ticket.form.php?id={$Ticket.id}" target="_blank">{$Ticket.id}</a></td>
			<td><a href="{$config.glpi_url}front/user.form.php?id={$Ticket.uid}" target="_blank">{$Ticket.realname} {$Ticket.firstname} </td>
			<td>{$Ticket.name}</td>
			<td>{$Ticket.categorie}</td>
			<td>{$Ticket.status}</td>
			<td>{$Ticket.date}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<script type="text/javascript">
<!--
$(document).ready(function() 
    { 
        $("#ts1").tablesorter(); 
    } 
);
//-->
</script>