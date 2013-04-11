<div id="bread">
	<a href="{getLink("index")}" title="{$lang.Accueil}">{$lang.Accueil}</a> &gt;&gt;
	{if isset($smarty.get.cid)}<a href="{getLink("article/index")}">{$lang.Article}</a> &gt;&gt;{else}{$lang.Article}{/if}
	{if isset($Parents)}
		{foreach $Parents as $Parent}
		<a href="{getLink("article/index?cid={$Parent.id}")}">{$Parent.name}</a> &gt;&gt;
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
			<a href="{getLink("article/index?cid={$Categorie.id}")}" title="{$Categorie.name}"><strong>{$Categorie.name}</strong></a><br/>
			{$Categorie.description}
		</div>
	</div>
	{if $smarty.foreach.lcat.iteration%2 == 0}<div class="clear"></div>{/if}
	{/foreach}
</div>
{/if}

{if $Articles > 0}
{foreach $Articles as $Article}
	<div class="showData">
		<h1><a href="{getLink("article/read/{$Article.id}")}" title="{$Article.title}">{$Article.title}</a></h1>
		<div class="article_description">
			{$Article.article|strip_tags|wordwrap:50}
		</div>
	</div>
{/foreach}
{/if}