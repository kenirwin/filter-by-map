<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/topojson/1.6.9/topojson.min.js"></script>
<script src="lib/datamaps.world.min.js"></script>
<div id="container" style="position: relative; width: 1000px; height: 400px;"></div>
<div id="bib"></div>
<script>
   var map = new Datamap({
     element: document.getElementById('container'),
	 fills: { 
       HIGH: 'red',
	   LOW: 'orange',
	   defaultFill: 'lightgrey'
	   },
	 data: { <? include ('countries.php');  ColorizeCountries(); ?> },
	 geographyConfig: {
       popupTemplate: function(geo, data) {
	   return ['<div class="hoverinfo"><strong>',
		   'Number of citations for ' + geo.properties.name,
		   ': ' + data.numberOfCites,
		   '</strong></div>'].join('');
	 }
       },
	 done: function(datamap) {
	 datamap.svg.selectAll('.datamaps-subunit').on('click', function(geography) {
	     $.ajax({
	       url: 'bib.php',
		   data: { country: geography.properties.name },
		   success: function(result) {
		   $('#bib').html(result);
		 }
	       });

	   });
       }
                   
     });
</script>