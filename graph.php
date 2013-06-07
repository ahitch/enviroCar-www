<?
include('header.php');
?>

<div id="loadingIndicator" class="loadingIndicator">
<div style="background:url(./assets/img/ajax-loader.gif) no-repeat center center; height:100px;"></div>
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>	

<div id="graphs" class="container rightband">
</div>

<script type="text/javascript">

	function convertToLocalTime(serverDate) {
		var dt = new Date(Date.parse(serverDate));
		var localDate = dt;

		var gmt = localDate;
			var min = gmt.getTime() / 1000 / 60; // convert gmt date to minutes
			var localNow = new Date().getTimezoneOffset(); // get the timezone
			// offset in minutes
			var localTime = min - localNow; // get the local time

		var dateStr = new Date(localTime * 1000 * 60);
		var d = dateStr.getDate();
		var m = dateStr.getMonth() + 1;
		var y = dateStr.getFullYear();

		var totalSec = dateStr.getTime() / 1000;
		var hours = parseInt( totalSec / 3600 ) % 24;
		var minutes = parseInt( totalSec / 60 ) % 60;

		return '' + y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d) + ' ' + hours +':'+ minutes;
	}
	
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(callPhens);
	
	var serverTime = [];
	var time = [];
	var phen = [];
	var phenName = [];
	
	var trackID = '<?php echo $_GET["id"]; ?>';
	
	function callPhens(){
		$.get('http://giv-car.uni-muenster.de:8080/stable/rest/tracks/' + trackID + '/statistics', function(data) {
			for(var i=0;i<data.statistics.length;i++){
				phenName[i] = data.statistics[i].phenomenon.name;
				}
			callData();
			});
		}
	
	function callData(){
		$.get('http://giv-car.uni-muenster.de:8080/stable/rest/tracks/' + trackID, function(data) {
		if(data >=400){
			error_msg("Routes couldn't be loaded successfully.");
			$('#loadingIndicator').hide();
		}else{
			for(var i=0;i<phenName.length;i++){
				$('#graphs').append('<div class="span5"><h2>'+phenName[i]+'</h2><div id="chart'+i+'" style="width: 500px; height: 400px;"></div></div>');
	
				phen[0] = ['time', eval('data.features[0].properties.phenomenons.'+phenName[i]+'.unit')];
				for(var j=0;j<data.features.length;j++){
					serverTime[j] = data.features[j].properties.time;
					time[j] = convertToLocalTime(serverTime[j]);
					phen[j+1] = [time[j], eval('data.features['+j+'].properties.phenomenons.'+phenName[i]+'.value')];
					}
				chartName = 'chart'+i;
				drawChart(phen, chartName);
				}
			}
			$('#loadingIndicator').hide();
			});
		}
		
	function drawChart(phenomenons, chart) {
        data = google.visualization.arrayToDataTable(
			phenomenons
			);

        var options = {
		  colors: ['#048ABF']
        };

        var speedChart = new google.visualization.LineChart(document.getElementById(chart));
        speedChart.draw(data, options);
      }
	  

</script>
<?
include('footer.php');
?>