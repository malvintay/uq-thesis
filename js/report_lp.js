reports.lp = {

	//prepare for highcharts
	prepareData : function() {
	},

	showGraph	: function(data) {
	
		if (reports.conf.loadedModules.indexOf('lp') == -1) {
			reports.conf.loadedModules.push('lp');
		}
	
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}

		$('#clicks-chart').highcharts({
			chart: {
				type: 'column'
			},
	        title: {
	            text: 'LP Accesses',
	            x: -20 //center
	        },
	        subtitle: {
	            text: 'Report for '+reports.conf.range+' days',
	            x: -20
	        },
	        xAxis: {
	            categories: data.x_axis,
	            crosshair: true
	        },
	        yAxis: {
	            title: {
	                text: 'LP Accessed'
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

	    if (data.series[0].data.length == 0 && data.series[1].data.length == 0) {
			alert('No data in the selected period'+(reports.conf.userId == false ? '' : ' and selected user id'));
	    }
	}

}

$(document).ready(function() {
	reports.conf.defaultReportType = 'lp';
	reports.showReport('lp');	
});
