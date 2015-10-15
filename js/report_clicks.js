reports.clicks = {

	//prepare for highcharts
	prepareData : function() {
		var data = reports.conf.data;
		
		data.xAxis = [];
		data.series = [];
		
		var clickValues = [];
		var avgClicks = [];
		
		for (var i = 0; i < data.data.length; i++) {
			data.xAxis.push(data.data[i][0]);
			clickValues.push(parseInt(data.data[i][1]));
			if (reports.conf.userId != false && data.dataAverage[i] != undefined) {
				avgClicks.push(data.dataAverage[i][1]);
			}
		}
		
		data.series = [{name: 'Clicks', data: clickValues}];
		
		if (reports.conf.userId != false) {
			data.series.push({name: 'Average clicks', data: avgClicks});
		}
	},

	showGraph	: function(data) {
		
		if (reports.conf.loadedModules.indexOf('clicks') == -1) {
			reports.conf.loadedModules.push('clicks');
		}
	
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
	
		$('#clicks-chart').highcharts({
	        title: {
	            text: 'Activity (Clicks)',
	            x: -20 //center
	        },
	        subtitle: {
	            text: 'Report for '+reports.conf.range+' days',
	            x: -20
	        },
	        xAxis: {
	            categories: data.xAxis
	        },
	        yAxis: {
	            title: {
	                text: 'Clicks'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        legend: {
	            enabled: false
	        },
	        series: data.series
	    });
	    
	    if (data.data.length == 0) {
			alert('No data in the selected period'+(reports.conf.userId == false ? '' : ' and selected user id'));
	    }
	}

}

$(document).ready(function() {
	reports.conf.defaultReportType = 'clicks';
	reports.showReport('clicks');
});
