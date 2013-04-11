{strip}
<form method="post" action="{getLink("stats/stats")}">
    <fieldset>
    <div style="float:left; width:500px;">
        <!-- LIEUX -->
        <dl>
            <dt><label id="lieu">Lieu :</label></dt>
            <dd>
                <select name="filtre[lieu]">
                    <option value=""></option>
                    {foreach $Lieux as $Lieu}
                    <option value="{$Lieu.id}">{$Lieu.completename}</option>
                    {/foreach}
                </select>
            </dd>
        </dl>
    </div>
    <div style="margin-left: 510px;">
        <!-- CATEGORIE -->
        <dl>
            <dt><label id="categorie">Categorie :</label></dt>
            <dd>
                <select name="filtre[categorie]">
                    <option value=""></option>
                    {foreach $Categories as $Categorie}
                    <option value="{$Categorie.id}">{$Categorie.completename}</option>
                    {/foreach}
                </select>
            </dd>
        </dl>
    </div>
    <div style="clear:both"></div>
    
    <div style="float:left; width:500px;">
        <!-- DATE DEBUT -->
        <dl>
            <dt><label>Date debut :</label></dt>
            <dd><input type="text" name="filtre[date_debut]" id="date_debut" size="10" required/></dd>
        </dl>
    </div>
    <div style="margin-left: 510px;">
        <!-- DATE FIN -->
        <dl>
            <dt><label>Date fin :</label></dt>
            <dd><input type="text" name="filtre[date_fin]" id="date_fin" size="10" required/></dd>
        </dl>
        </div>
        <div style="clear:both"></div>
    <div class="center">
        <button type="submit" class="btn btn-primary">Generer</button>
        </div>
    </fieldset>
</form>{/strip}
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