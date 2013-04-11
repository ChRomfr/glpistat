<!--
    STATS/INDEX.TPL
//-->
{strip}
<div class="well">
    <h4>Statistique {$Date.month} {$Date.year}</h4>
    	
    <div id="formFilterFull"></div>
    <hr/>
    <div class="container">
        <div class="row-fluid">
            <div class="span6">
                <div id="NbTicketByLieu" style="height: 300px; overflow: auto;"></div>
            </div>
            <div class="span6">
                <div id="NbTicketByCategorie" style="height: 300px; overflow: auto;"></div>
            </div>
        </div>
    </div>
    <hr/>
    <h5>Comparer</h5>
    <form method="post" action="{getLink("stats/compare")}">
        <label>Date 1 :</label>
        <input type="text" name="start1" id="start1" class="input-small" />
        <input type="text" name="end1" id="end1" class="input-small" />
        <label>Date 2 :</label>
        <input type="text" name="start2" id="start2" class="input-small" />
        <input type="text" name="end2" id="end2" class="input-small" />
        <div class="form-action">
            <button type="submit" class="btn btn-primary">Comparer</button>
        </div>
    </form>

    <hr />
    <form>
        <select name="month">
			<option {if $Date.month == ''}selected="selected"{/if}></option>
            <option value="01" {if $Date.month == '01'}selected="selected"{/if}>Janvier</option>
            <option value="02" {if $Date.month == '02'}selected="selected"{/if}>Fevrier</option>
            <option value="03" {if $Date.month == '03'}selected="selected"{/if}>Mars</option>
            <option value="04" {if $Date.month == '04'}selected="selected"{/if}>Avril</option>
            <option value="05" {if $Date.month == '05'}selected="selected"{/if}>Mai</option>
            <option value="06" {if $Date.month == '06'}selected="selected"{/if}>Juin</option>
            <option value="07" {if $Date.month == '07'}selected="selected"{/if}>Juillet</option>
            <option value="08" {if $Date.month == '08'}selected="selected"{/if}>Aout</option>
            <option value="09" {if $Date.month == '09'}selected="selected"{/if}>Septembre</option>
            <option value="10" {if $Date.month == '10'}selected="selected"{/if}>Octobre</option>
            <option value="11" {if $Date.month == '11'}selected="selected"{/if}>Novembre</option>
            <option value="12" {if $Date.month == '12'}selected="selected"{/if}>Decembre</option>
        </select>
        <select name="year">
            <option value="2008" {if $Date.year == 2008}selected="selected"{/if}>2008</option>
            <option value="2009" {if $Date.year == 2009}selected="selected"{/if}>2009</option>
            <option value="2010" {if $Date.year == 2010}selected="selected"{/if}>2010</option>
            <option value="2011" {if $Date.year == 2011}selected="selected"{/if}>2011</option>
            <option value="2012" {if $Date.year == 2012}selected="selected"{/if}>2012</option>
            <option value="2013" {if $Date.year == 2013}selected="selected"{/if}>2013</option>
        </select>
        <button class="btn btn-primary" type="submit">Generer</button>
    </form>
	{if isset($NbTickets)}
	<hr/>
	<div>
        <ul class="unstyled">
            <li>Nombre de ticket : <strong>{$NbTickets}</strong></li>
            <li>Nombre de tickets clos : <strong>{$NbTicketsClosed}</strong></li>
            <li>Nombre de tickets resolu : <strong>{$NbTicketsSolved}</strong></li>
        </ul>
    </div>
	{/if}
</div>

<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#home" data-toggle="tab">Generale</a></li>
  <li><a href="#top10" data-toggle="tab">Top 10</a></li>
  <li><a href="#bysite" data-toggle="tab">Site</a></li>
  <li><a href="#bycategorie" data-toggle="tab">Categorie</a></li>
</ul>
 
<div class="tab-content">

  <div class="tab-pane active" id="home">
    <!-- GRAPHIQUE -->
    <div class="container">
        <div class="row-fluid">
            <div class="span6">
                <!-- Graph Pie incident demande -->
                <div id="pieincdem_loader" class="text-alig:center;"><i class="icon-spinner icon-spin"></i></div>
                <div id="pieincdem"></div>
            </div>
            <div class="span6">
                <!-- Graph Pie source demande -->
                <div id="pierequest_loader" class="text-alig:center;"><i class="icon-spinner icon-spin"></i></div>
                <div id="pierequest"></div>
            </div>
        </div><!-- /row-fluid -->

        <div id="graphbystatus" style="width:100%;"></div>

    </div><!-- Contrainer -->
  </div><!-- /home -->

  <div class="tab-pane" id="top10">
    <!-- Top ten -->
    <div class="container">
        <div class="row-fluid">
            <div class="span6"><table id="tableau_toptensite" class="table"><caption>Top 10 des sites</caption></table></div>
            <div class="span6"><table id="tableau_toptencategorie" class="table"><caption>Top 10 des categories</caption></table></div>
        </div>
       </div>
      </div><!-- /top10 -->

  <div class="tab-pane" id="bysite">
        <div id="chart2b_loader" class="text-alig:center;"><i class="icon-spinner icon-spin"></i></div>
        <div id="chart2b" style="height:6000px;" class="jqplot-target"></div>
  </div><!-- /bysite -->

    <!-- CATEGORIES -->
    <div class="tab-pane" id="bycategorie">
        <div id="chartcategorie" style="height:{$config.hauteur_graph_categorie}px;" class="jqplot-target"></div>
    </div>
</div>
{/strip}
<script type="text/javascript">
<!--
var month;
var year;
var lieux;
var categories;

{if isset($Date.month) && !empty($Date.month)}
month = {$Date.month}
{/if}

{if isset($Date.year)}
year = {$Date.year}
{/if}

$.get(
    '{getLink("stats/ajaxGetLieux")}',{literal}
    {nohtml:'nohtml'},
    function(data){ lieux = data; },'json'
);
{/literal}

$.get(
    '{getLink("stats/ajaxGetCategories")}',{literal}
    {nohtml:'nohtml'},
    function(data){ categories = data; },'json'
);
{/literal}

/**
 * Graph PIE incidents/demandes
 * 
 */
$(window).load(function(){
    $.get(
        '{getLink("stats/ajaxGraphByType")}',{literal}
        {nohtml:'nohtml', year:year, month:month},
        function(data){

            var plot1 = jQuery.jqplot ('pieincdem', [data], 
            { 
              seriesDefaults: {
                renderer: jQuery.jqplot.PieRenderer, 
                rendererOptions: {
                  showDataLabels: true
                }
              }, 
              legend: { show:true, location: 'e' }
            }
            ); 

            $('#pieincdem_loader').css('display','none');
        },'json'
    );
});
{/literal}

function getNbByLieu(month, year){
    $('#NbTicketByLieu').html('<i class="icon-spinner"></i>');
    
    $.get(
        '{getLink("stats/ajaxGetNbTicketByLieu")}',{literal}
        {month:month, year:year, nohtml:'nohtml'},
        function(data){ $('#NbTicketByLieu').html(data); }
    )
    {/literal}
}

function getNbByCategorie(month, year){
    $('#NbTicketByCategorie').html('<i class="icon-spinner"></i>');
    
    $.get(
        '{getLink("stats/ajaxGetNbTicketByCategorie")}',{literal}
        {month:month, year:year, nohtml:'nohtml'},
        function(data){ $('#NbTicketByCategorie').html(data); }
    )
    {/literal}
}

function generateFormFiltre(){
    $('#formFilterFull').html('<i class="icon-spinner"></i>');
    $.get(
        '{getLink("stats/generateFormFiltre")}',{literal}
        {nohtml:'nohtml'},
        function(data){ $('#formFilterFull').html(data); }
    )
    {/literal}
}

$(window).load(function(){
    getNbByLieu({if $Date.month == ''}''{else}{$Date.month}{/if}, {$Date.year});
    getNbByCategorie({if $Date.month == ''}''{else}{$Date.month}{/if}, {$Date.year});
    generateFormFiltre();
});

$(window).load(function(){
  $.get(
        '{getLink("stats/ajaxGraphByRequest")}',{literal}
        {nohtml:'nohtml', year:year, month:month},
        function(data){

            var plot1 = jQuery.jqplot ('pierequest', [data], 
            { 
              seriesDefaults: {
                renderer: jQuery.jqplot.PieRenderer, 
                rendererOptions: {
                  showDataLabels: true
                }
              }, 
              legend: { show:true, location: 'e' }
            }
            ); 

            $('#pierequest_loader').css('display','none');
        },'json'
    );
    {/literal}  
});

$(window).load(function(){
if( month != '' ){
    $.get(
        '{getLink("stats/ajaxGraphByStatus")}',{literal}
        {month:month, year:year, nohtml:'nohtml'},
        function(data){

            $(function() {                      

                plot2b = $.jqplot('graphbystatus', data , {
                    seriesDefaults: {
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: {show: true, edgeTolerance: -15},
                        rendererOptions: {
                            barWidth:5,
                           
                        }
                    },                   
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                        },
                        yaxis: {
                            pad: 0,
                        }
                    },
                    series:[
                        {label:'Nouveau ticket'},
                        {label:'Resolu'},
                        {label:'Cloturer'},
                    ],
                    legend: {
                        show: true,
                        location: 'ne',
                        placement: 'insideGrid'
                    } 
            });
        });

        },'json'
    )
}
});
{/literal}

$('a[href="#bysite"]').on('shown', function (e) {
    e.target // activated tab
    e.relatedTarget // previous tab

    if( month != '' ){
    $.get(
        '{getLink("stats/ajaxGraphiByLieu")}',{literal}
        {month:month, year:year, nohtml:'nohtml'},
        function(data){
            $(function() {                      
               
                var ticks = lieux;
               
                plot2b = $.jqplot('chart2b', data , {
                    seriesDefaults: {
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
                        rendererOptions: {
                            barDirection: 'horizontal'
                        }
                    },                   
                    axes: {
                        xaxis: {
                           // pad:1,
                        },
                        yaxis: {
                            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: ticks
                        }
                    },
                    series:[
                        {label:'Semaine 1'},
                        {label:'Semaine 2'},
                        {label:'Semaine 3'},
                        {label:'Semaine 4'}
                    ],
                    legend: {
                        show: true,
                        location: 'e',
                        placement: 'outside'
                    } 
            });
     
            $('#chart2b').bind('jqplotDataHighlight', 
                function (ev, seriesIndex, pointIndex, data) {
                    $('#info2b').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data+ ', pageX: '+ev.pageX+', pageY: '+ev.pageY);
                }
            );    
            $('#chart2b').bind('jqplotDataClick', 
                function (ev, seriesIndex, pointIndex, data) {
                    $('#info2c').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data+ ', pageX: '+ev.pageX+', pageY: '+ev.pageY);
                }
            );
                 
            $('#chart2b').bind('jqplotDataUnhighlight', 
                function (ev) {
                    $('#info2b').html('Nothing');
                }
            ); 

        });

    },"json"); 

    $('#chart2b_loader').css('display','none');
    
    {/literal}
}

});

$('a[href="#bycategorie"]').on('shown', function (e) {
    e.target        // activated tab
    e.relatedTarget // previous tab

    if( month != '' ){
    $.get(
        '{getLink("stats/ajaxGraphiByCategorie")}',{literal}
        {month:month, year:year, nohtml:'nohtml'},
        function(data){
            $(function() {                      
               
                var ticks = categories;
               
                plot2b = $.jqplot('chartcategorie', data , {
                    seriesDefaults: {
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: { show: true, location: 'e'/*, edgeTolerance: -15 */},
                        rendererOptions: {
                            barDirection: 'horizontal'
                        }
                    },                   
                    axes: {
                        xaxis: {
                           // pad:1,
                        },
                        yaxis: {
                            /*autoscale:true,*/
                            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: ticks
                        }
                    },
                    series:[
                        {label:'Semaine 1'},
                        {label:'Semaine 2'},
                        {label:'Semaine 3'},
                        {label:'Semaine 4'}
                    ],
                    legend: {
                        show: true,
                        location: 'e',
                        placement: 'outside'
                    } 
            });
     
            $('#chartcategorie').bind('jqplotDataHighlight', 
                function (ev, seriesIndex, pointIndex, data) {
                    $('#info2b').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data+ ', pageX: '+ev.pageX+', pageY: '+ev.pageY);
                }
            );    
            $('#chartcategorie').bind('jqplotDataClick', 
                function (ev, seriesIndex, pointIndex, data) {
                    $('#info2c').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data+ ', pageX: '+ev.pageX+', pageY: '+ev.pageY);
                }
            );
                 
            $('#chartcategorie').bind('jqplotDataUnhighlight', 
                function (ev) {
                    $('#info2b').html('Nothing');
                }
            ); 

        });

    },"json");

    }
    {/literal}   
});

{literal}
$(window).load(function(){
    if (!$.jqplot.use_excanvas) {
        $('div.jqplot-target').each(function(){
            var outerDiv = $(document.createElement('div'));
            var header = $(document.createElement('div'));
            var div = $(document.createElement('div'));

            outerDiv.append(header);
            outerDiv.append(div);

            outerDiv.addClass('jqplot-image-container');
            header.addClass('jqplot-image-container-header');
            div.addClass('jqplot-image-container-content');

            header.html('Right Click to Save Image As...');

            var close = $(document.createElement('a'));
            close.addClass('jqplot-image-container-close');
            close.html('Close');
            close.attr('href', '#');
            close.click(function() {
                $(this).parents('div.jqplot-image-container').hide(500);
            })
            header.append(close);

            $(this).after(outerDiv);
            outerDiv.hide();

            outerDiv = header = div = close = null;

            if (!$.jqplot._noToImageButton) {
                var btn = $(document.createElement('button'));
                btn.text('View Plot Image');
                btn.addClass('jqplot-image-button btn');
                btn.bind('click', {chart: $(this)}, function(evt) {
                    var imgelem = evt.data.chart.jqplotToImageElem();
                    var div = $(this).nextAll('div.jqplot-image-container').first();
                    div.children('div.jqplot-image-container-content').empty();
                    div.children('div.jqplot-image-container-content').append(imgelem);
                    div.show(500);
                    div = null;
                });

                $(this).after(btn);
                btn.after('<br />');
                btn = null;
            }
        });
    }

    $.get(
        {/literal}'{getLink("stats/ajaxTopTenSite")}',{literal}
        {month:month, year:year,nohtml:'nohtml'},
        function(data){ 
 
            for( var i in data ){      
               $('#tableau_toptensite').append( "<tr><td>"+data[i].site+"</td><td>"+data[i].nb_tickets+"</td></tr>" );
            }
        },'json'
    )

    $.get(
        {/literal}'{getLink("stats/ajaxTopTenCategorie")}',{literal}
        {month:month, year:year,nohtml:'nohtml'},
        function(data){ 

            for( var i in data ){      
               $('#tableau_toptencategorie').append( "<tr><td>"+data[i].categorie+"</td><td>"+data[i].nb_tickets+"</td></tr>" );
            }
        },'json'
    )

});
/*
$(function() {
        $( "#start1" ).datepicker({ dateFormat: 'yy-mm-dd', changeMonth:true, changeYear:true, showButtonPanel: true });
        $( "#end1" ).datepicker({dateFormat: 'yy-mm-dd', changeMonth:true, changeYear:true, showButtonPanel: true });
    });
*/
$(function() {
    $( "#start1" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: 'yy-mm-dd',
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#end1" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: 'yy-mm-dd',
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });

$(function() {
    $( "#start2" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: 'yy-mm-dd',
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#end2" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: 'yy-mm-dd',
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });

{/literal}
//-->
</script>

