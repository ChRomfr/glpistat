<div id="bread">
	<a href="{getLink("index")}" title="{$lang.Accueil}">{$lang.Accueil}</a> &gt;&gt;
	<a href="{getLink("article")}" title="{$lang.Article}">{$lang.Article}</a> &gt;&gt;
	{if isset($Parents)}
		{foreach $Parents as $Parent}
		<a href="{getLink("article/index?cid={$Parent.id}")}">{$Parent.name}</a> &gt;&gt;
		{/foreach}
	{/if}
	
	{if !empty($Article.categorie_id)}
		<a href="{getLink("article/index?cid={$Article.categorie_id}")}" title="{$Article.categorie}">{$Article.categorie}</a> &gt;&gt;
	{/if}
	{$Article.title}
</div>

<div id="article_{$Article.id}" class="showData">
	<h1>{$Article.title}</h1>
	<div id="contenu_article">{$Article.article}</div>
</div>