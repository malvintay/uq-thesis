reports.quiz_summary = {

	//prepare for highcharts
	prepareData : function() {},

	showGraph	: function(data) {
	
		if (reports.conf.loadedModules.indexOf('quiz_summary') == -1) {
			reports.conf.loadedModules.push('quiz_summary');
		}
		
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}
		
		$('#quiz-chart').highcharts({
			chart: {
				polar: true,
				type: 'line'
			},
	        title: {
	            text: 'Weekly Quiz Report',
	            x: 0 //center
	        },
	        xAxis: {
	            categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
				tickmarkPlacement: 'on',
				lineWidth: 0
	        },
	        yAxis: {
	            gridLineInterpolation: 'polygon',
	            lineWidth: 0,
	            min: 0
	        },
	        legend: {
	            enabled: true
	        },
	        series: data.series
	    });
	    
	    if (data.series.length == 0) {
			alert('No data');
	    }
	}

}

reports.quiz_temporal = {
	//prepare for highcharts
	prepareData : function() {},

	showGraph	: function(data) {
	
		if (reports.conf.loadedModules.indexOf('quiz_temporal') == -1) {
			reports.conf.loadedModules.splice(reports.conf.loadedModules.indexOf('quiz_drilldown'), 1);
			reports.conf.loadedModules.push('quiz_temporal');
		}
	
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}

		Highcharts.setOptions({
        	lang: {
            drillUpText: '<< Back {series.name}'
        	}
    	});
		
		$('#quiz-chart1').highcharts({
	        chart: {
	            plotBackgroundColor: null,
	            plotBorderWidth: 0,
	            plotShadow: false
	        },
	        title: {
	            text: 'Modules Evaluation',
	            align: 'center',
	            verticalAlign: 'middle',
	            y: 0
	        },
	        tooltip: {
	            formatter: function(p) {
	            	var days = Math.floor(this.y / 86400);
					var hours = Math.floor((this.y % 86400) / 3600);
					var minutes = Math.floor(((this.y % 86400) % 3600) / 60);
					var seconds = ((this.y % 86400) % 3600) % 60;
					
					return this.series.name +' <b>'+ days + " days " + hours + " hours " + minutes + " minutes " + seconds + " seconds"  +'</b>';
				}
	        },
	        plotOptions: {
	            series: {
	            	cursor: 'pointer',
	                point: {
	                	events: {
	                    	click : function() {
	                        	reports.quiz_temporal.showDrilldown(/(\d+)$/.exec(this.name)[1]);
	                        	$('#back-to-donut').removeClass('hidden');
	                        }
	                    }
	                }
	            },
	            pie: {
	                dataLabels: {
	                    enabled: true,
	                    distance: -50,
	                    style: {
	                        fontWeight: 'bold',
	                        color: 'white',
	                        textShadow: '0px 1px 2px black'
	                    }
	                },
	                center: ['50%', '50%']
	            }
	        },
	        series: [{
	            type: 'pie',
	            name: 'Time Spent:',
	            innerSize: '50%',
	            data: data.series
	        }]
	    });
	    
	    if (data.series.length == 0) {
			alert('No data');
	    }
	},
	
	showDrilldown : function(module) {
		reports.conf.sendData = {module : module}
		reports.showReport('quiz_drilldown');
	}
	
}

reports.quiz_drilldown = {
	prepareData : function() {},

	showGraph	: function(data) {
		
		if (reports.conf.loadedModules.indexOf('quiz_drilldown') == -1) {
			reports.conf.loadedModules.splice(reports.conf.loadedModules.indexOf('quiz_temporal'), 1);
			reports.conf.loadedModules.push('quiz_drilldown');
		}
	
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}
		
		$('#quiz-chart1').highcharts({
			chart: {
				type: 'column'
			},
	        title: {
	            text: 'Module Quiz Reports',
	            x: -20 //center
	        },
	        /*
	        subtitle: {
	            text: 'Report for '+reports.conf.range+' days',
	            x: -20
	        },*/
	        xAxis: {
	            categories: data.x_axis,
	            crosshair: true
	        },
	        yAxis: {
	            title: {
	                text: 'Grades'
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
	    
	    if (data.series.length == 0) {
			alert('No data');
	    }
	}
}

$(document).ready(function() {
	reports.showReport('quiz_temporal');
	reports.showReport('quiz_summary');
	setTimeout(function() {reports.conf.loadedModules.push('quiz_summary');}, 600);
	$('#back-to-donut button').click(function() {
		$('#back-to-donut').addClass('hidden');
		reports.showReport('quiz_temporal');
	});
	
});
