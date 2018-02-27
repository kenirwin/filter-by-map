<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/topojson/1.6.9/topojson.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>

<? 
require_once('admin/includes/init.php');
$map = new MapSettings(1);
?>

<script src="lib/datamaps.world.min.js"></script>
<div id="container" style="position: relative; width: 1000px; height: 400px; background-color: <?=$map->background_color;?>"></div>
<div id="results_info"></div>
<table id="results"></table>
<script>
   var map = new Datamap({
     element: document.getElementById('container'),
	 fills: { 
<?=$map->fills;?>
	   },
	 data: { <?=$map->fillKeys ?> },
	 geographyConfig: {
       highlightFillColor: '<?=$map->mouseover_color;?>',
       popupTemplate: function(geo, data) {
	   return ['<div class="hoverinfo"><strong>',
		   'Number of <?=$map->item_label_plural;?> for ' + geo.properties.name,
		   ': ' + data.numberOfCites,
		   '</strong></div>'].join('');
	 }
       },
	 done: function(datamap) {
	 datamap.svg.selectAll('.datamaps-subunit').on('click', function(geography) {
	     $('#results_info').html('<h2>Results for: '+geography.properties.name+'</h2>');
	     $.ajax({
	       url: 'items.php',
		   data: { 'geo_search' : geography.properties.name, 'settings_id': 1 },
		   success: function(result) {
		   
		   if ($.fn.dataTable.isDataTable('#results')) {
		     table = $('#results').DataTable();
		     table.destroy();
		   }
		   


		   var obj = JSON.parse(result);
		   
		   var columns = []
		   for (i=0; i<obj.dict.length; i++) {
		     columns.push( {'title': obj.dict[i] } )
		   }

		   $('#results').DataTable({
		       'data': obj.data,
		       'columns': columns
		     });
		   
		 }
	       });

	   });
       }
                   
     });
</script>