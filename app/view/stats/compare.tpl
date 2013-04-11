<div class="well">
	<h4>Comparatif</h4>

	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th></th>
				<th>{$date.start1} au {$date.end1}</th>
				<th>{$date.start2} au {$date.end2}</th>
				<th>Diff</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Tickets</td>
				<td>{$stats.date1.nbticket}</td>
				<td>{$stats.date2.nbticket}</td>
				<td>{$stats.compare.nbticket}</td>
			</tr>
			<tr>
				<td>Clos</td>
				<td>{$stats.date1.nbclose}</td>
				<td>{$stats.date2.nbclose}</td>
				<td>{$stats.compare.nbclose}</td>
			</tr>
			<tr>
				<td>Resolu</td>
				<td>{$stats.date1.nbsolved}</td>
				<td>{$stats.date2.nbsolved}</td>
				<td>{$stats.compare.nbsolved}</td>
			</tr>
		</tbody>
	</table>

	<h4>Ratio par lieu</h4>
	<table class="table table-bordered table-striped table-hover tablesorter" id="ts1">
		<thead>
			<tr>
				<th>Lieu</th>
				<th>Effectif</th>
				<th>{$date.start1} au {$date.end1}</th>
				<th>{$date.start2} au {$date.end2}</th>
				<!--<th>Diff</th>-->
			</tr>
		</thead>
		<tbody>
			{foreach $lieux as $row}
			<tr>
				<td>{$row.name}</td>
				<td>{if isset($row.nbcollab)}{$row.nbcollab}{else}-{/if}</td>
				<td>{if isset($row.ratio_d1)}{$row.ratio_d1} <small>({$row.nbticket_d1})</small>{else}-{/if}</td>
				<td>{if isset($row.ratio_d2)}{$row.ratio_d2} <small>({$row.nbticket_d2})</small>{else}-{/if}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

<script>
$(document).ready(function() 
    { 
        $("#ts1").tablesorter(); 
    } 
);
</script>