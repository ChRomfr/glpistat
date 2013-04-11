<div id="bread">
	<a href="{getLink("index")}" title="{$lang.Accueil}">{$lang.Accueil}</a> &gt;&gt;
	{if isset($smarty.get.cid)}<a href="{getLink("download/index")}">{$lang.Telechargement}</a> &gt;&gt;{else}{$lang.Telechargement}{/if}
	{if isset($Parents)}
		{foreach $Parents as $Parent}
		<a href="{getLink("download/index?cid={$Parent.id}")}">{$Parent.name}</a> &gt;&gt;
		{/foreach}
	{/if}
	{if isset($Categorie)}{$Categorie.name}{/if}
</div>

{if isset($Categories) && count($Categories) > 0}
<div id="categories_list" class="showData" style="min-height:100px;">
	<h1>{$lang.Categories}</h1>
	{foreach $Categories as $Categorie name=lcat}
	<div class="{if $smarty.foreach.lcat.iteration%2 == 0}fright{else}fleft{/if}" style="width:45%; margin-bottom:5px; padding-bottom:5px; ">
		<div>
			<a href="{getLink("download/index?cid={$Categorie.id}")}" title="{$Categorie.name}"><strong>{$Categorie.name}</strong></a><br/>
			{$Categorie.description}
		</div>
	</div>
	{if $smarty.foreach.lcat.iteration%2 == 0}<div class="clear"></div>{/if}
	{/foreach}
</div>
{/if}
<div class="sep"></div>
{if $Downloads > 0}
{foreach $Downloads as $Download}
	<div class="showData">
		<h1>{$Download.name}</h1>
		<div class="download_description">{$Download.description}</div>
		<div class="fright">
			<a href="{$Download.url}" title="{$lang.Telecharger}" target="_blank"><img src="{$config.url}{$config.url_dir}web/images/save.png" alt="{$lang.Telecharger}" style="width:30px;" /></a>
		</div>
		<div class="clear"></div>
	</div>
{/foreach}
{/if}

{if !empty($Pagination)}<div class="pagination">{$Pagination}</div>{/if}