{strip}
<h4>Categories</h4>

<table class="table table-condensed">
    <thead>
        <tr>
            <th>Categories</th>
            <th>Nombre de tickets</th>
            <th>Cumul enfant</th>
        </tr>
    </thead>
    <tbody>
        {foreach $Stats as $Data}
        <tr>
            <td>{$Data.categorie}</td>
            <td>{$Data.nombre}</td>
            <td>{$Data.cumul}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/strip}