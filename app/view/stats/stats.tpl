{strip}
<form method="post" action="{getLink("stats/stats")}">
    <!-- LIEUX -->
    <dl>
        <dt><label id="lieu">Lieu :</label></dt>
        <dd>
            <select name="filtre[lieu]">
                <option value=""></option>
                {foreach $Lieux as $Lieu}
                <option value="{$Lieu.id}" {if $smarty.post.filtre.lieu == $Lieu.id}selected="selected"{/if}>{$Lieu.completename}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    
    <!-- CATEGORIE -->
    <dl>
        <dt><label id="categorie">Categorie :</label></dt>
        <dd>
            <select name="filtre[categorie]">
                <option value=""></option>
                {foreach $Categories as $Categorie}
                <option value="{$Categorie.id}" {if $smarty.post.filtre.categorie == $Categorie.id}selected="selected"{/if}>{$Categorie.completename}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <!-- DATE DEBUT -->
    <dl>
        <dt><label>Date debut :</label></dt>
        <dd><input type="text" name="filtre[date_debut]" id="date_debut" size="10" value="{$smarty.post.filtre.date_debut}" /></dd>
    </dl>
    <!-- DATE FIN -->
    <dl>
        <dt><label>Date fin :</label></dt>
        <dd><input type="text" name="filtre[date_fin]" id="date_fin" size="10" value="{$smarty.post.filtre.date_fin}"/></dd>
    </dl>
    <div class="center"><input type="submit" value="Generer" /></div>
</form>
<!-- Affichage nb de tickets -->
<div>Nombre de ticket : <span><strong>{$NbTicket}</strong></span></div>
<div><a href="{getLink("stats/index")}" title="">Retour</a></div>
{/strip}
<script type="text/javascript">
<!--
    $(function() {
        $( "#date_debut" ).datepicker({ dateFormat: 'dd/mm/yy', changeMonth:true, changeYear:true, showButtonPanel: true });
        $( "#date_debut" ).datepicker( "option", "showAnim", "clip" );
        $( "#date_fin" ).datepicker({ dateFormat: 'dd/mm/yy', changeMonth:true, changeYear:true, showButtonPanel: true });
        $( "#date_fin" ).datepicker( "option", "showAnim", "clip" );
    });
//-->
</script>
