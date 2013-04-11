<!DOCTYPE html>
<head>
<title>STATS GLPI</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="{$config.base_url}themes/default/default.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.base_url}web/lib/jqplot/jquery.jqplot.min.css" type="text/css" media="screen" />
<!-- Bootstrap -->
<link rel="stylesheet" href="{$config.base_url}themes/bootstrap/css/font-awesome.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.base_url}themes/bootstrap/css/bootstrap.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.base_url}themes/bootstrap/css/bootstrap-responsive.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.base_url}themes/bootstrap/css/opa-icons.css" type="text/css" media="screen" />
{if !empty($css_add)}
{foreach $css_add as $k => $v}
<link rel="stylesheet" href="{$config.base_url}web/css/{$v}" type="text/css" media="screen" />
{/foreach}
{/if}
{if !empty($js_add)}
{foreach $js_add as $k => $v}
<script type="text/javascript" src="{$config.base_url}web/js/{$v}"></script>
{/foreach}
{/if}
<script type="text/javascript" src="{$config.url}{$config.url_dir}themes/bootstrap/js/bootstrap.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="{$config.base_url}web/lib/tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="{$config.base_url}web/lib/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="{$config.base_url}web/lib/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="{$config.base_url}web/lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="{$config.base_url}web/lib/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="{$config.base_url}web/lib/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
</head>
<body>
{strip}
	<!-- Nav bar -->
	<div class="navbar navbar-fixed-top">
  		<div class="navbar-inner">
      		<div class="container">
      			<!--
	          	<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
	          	</a>-->
          		<a class="brand" href="#">GLPI</a>
          		<div class="nav-collapse">
       				<ul class="nav">
              			<li><a href="{getLink("index")}">Home</a></li>
                    <li><a href="{getLink("stats")}" title="">Statistique</a></li>
                    <li><a href="{getLink("tickets")}" title="">Tickets par lieu</a></li>
              			<li><a href="{$config.glpi_url}" target="_blank">GLPI</a></li>
            		</ul>
          		</div><!--/.nav-collapse -->
        	</div>
      	</div>
    </div>
    <header class="jumbotron" style="padding-top:40px;"></header>

    <!-- Contenu -->
    <div class="container">    
	    <div class="row-fluid">
	    	<div class="span12">{$content}</div>
		</div>
	</div>
    </body>
</html>
{/strip}