{strip}
<h4>Lieux</h4>
<table class="table table-condensed">
    <thead>
        <tr>
            <th>Lieux</th>
            <th>Tickets</th>
            <th>Ticket/Collab</th>
        </tr>
    </thead>
    <tbody>
        {foreach $Stats as $Data}
        <tr>
            <td>{$Data.lieu}</td>
            <td>{$Data.nombre}</td>
            <td>{if isset($Data.ratio)}{$Data.ratio}{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/strip}