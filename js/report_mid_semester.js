reports.mid_semester_pie = {

	//prepare for highcharts
	prepareData : function() {},

	showGraph	: function(data) {
	
		if (reports.conf.loadedModules.indexOf('mid_semester_pie') == -1) {
			reports.conf.loadedModules.push('mid_semester_pie');
		}
		
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}
		
		reports.mid_semester_pie.chart = $('#mid-semester-pie').highcharts({
	        chart: {
	            plotBackgroundColor: null,
	            plotBorderWidth: 0,
	            plotShadow: false,
	            /*
	            events: {
					load: function() {
					    if (reports.conf.userId == false) {
							$('#mid-semester-pie .highcharts-title').css('color', '#59BC54');
					    }
					}
	            }
	            * */
	        },
	        title: {
	            text: 'Average grade '+data.average,
	            align: 'center',
	            verticalAlign: 'middle',
	            y: 0,
	            useHTML: true
	        },
	        subtitle: {
				text: reports.conf.userId == false ? '' : 'User mark '+data.user_mark + '<span class="glyphicon '+(data.user_mark > data.average ? 'glyphicon-arrow-up' : 'glyphicon-arrow-down')+'"></span>',
	            align: 'center',
	            verticalAlign: 'middle',
	            y: 30,
	            useHTML: true,
	            style: {
					color: data.user_mark >= data.average ? 'green' : 'red',
					fontSize: '17px'
	            }
	        },
	        tooltip: {
	            enabled: false
	        },
	        plotOptions: {
	            series: {
	                point: {
	                	events: {
	                    	mouseOver : function() {
								
								//user has higher score than avg?
								var higher = this.user_mark >= this.value;
								
								var subtitleText = reports.conf.userId == false ? '' : 'User mark '+this.user_mark + '<span class="glyphicon '+(this.user_mark > this.value ? 'glyphicon-arrow-up' : 'glyphicon-arrow-down')+'"></span>';
	                    	
	                        	reports.mid_semester_pie.chart.highcharts().setTitle({
									text : this.name + ' average grade '+this.value
	                        	}, reports.conf.userId == false ? undefined : {
									text : subtitleText,
									style: {
										color: (higher ? 'green' : 'red')
									}
	                        	});
	                        },
	                        
	                        mouseOut : function() {
	                        
								//user avg vs total avg
								if (reports.conf.userId == false) {
									var avg_higher = false;
								} else {
									var avg_higher = data.user_mark >= data.average;
								}
	                        
								reports.mid_semester_pie.chart.highcharts().setTitle({
									text : 'Average grade '+data.average
	                        	}, {
									text : reports.conf.userId == false ? '' : 'User mark '+data.user_mark + '<span class="glyphicon '+(data.user_mark > data.average ? 'glyphicon-arrow-up' : 'glyphicon-arrow-down')+'"></span>',
									style: {
										color: (avg_higher ? 'green' : 'red')
									}
	                        	});
	                        }
	                    }
	                }
	            },
	            pie: {
	                dataLabels: {
	                    enabled: true,
	                    distance: -35,
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
	            name: 'Average grade',
	            innerSize: '65%',
	            data: data.series
	        }]
	    });
	    
	    if (data.series.length == 0) {
			alert('No data');
	    }
	}

}

reports.mid_semester_plot = {
	//prepare for highcharts
	prepareData : function() {},

	showGraph	: function(data) {
	
		if (reports.conf.loadedModules.indexOf('mid_semester_plot') == -1) {
			reports.conf.loadedModules.push('mid_semester_plot');
		}
	
		if (typeof data === 'undefined') {
			data = reports.conf.data;
		}
		
		if (data.data) {
			data = data.data;
		}
		
		console.log(data);
		
		reports.mid_semester_plot.chart = $('#mid-semester-plot').highcharts({
	        chart: {
	            type: 'scatter',
	            zoomType: 'xy'
	        },
	        title: {
	            text: 'Users mark/attempts'
	        },
	        xAxis: {
	            title: {
	                enabled: true,
	                text: 'Attempts'
	            },
	            startOnTick: true,
	            endOnTick: true,
	            showLastLabel: true,
	            min: 0,
	            tickInterval : 100
	        },
	        yAxis: {
	            title: {
	                text: 'Marks'
	            },
	            tickInterval : 10
	        },
	        legend: {
	            enabled: false
	        },
	        plotOptions: {
				series : {
					states: {
						hover: {
							enabled: false
						}
					}
				},
	            scatter: {
	                marker: {
	                    radius: 5,
	                    states: {
	                        hover: {
	                            enabled: true,
	                            lineColor: 'rgb(100,100,100)'
	                        }
	                    }
	                },
	                states: {
	                    hover: {
	                        marker: {
	                            enabled: false
	                        }
	                    }
	                },
	                tooltip: {
	                    headerFormat: '<b>{series.name}</b><br>',
	                    pointFormat: 'Attempts {point.x}, Mark {point.y}, User ID: {point.user_id}'
	                }
	            }
	        },
	        series: data.series
	    });
	    
		if (data.series.length == 0) {
			alert('No data');
	    }
	},
	
	showDrilldown : function(module) {
		reports.conf.sendData = {module : module}
		reports.showReport('quiz_drilldown');
	},
	
	findById : function(userId) {
		var chart = reports.mid_semester_plot.chart.highcharts();
		
		
		var data = chart.series[0].data;

		for (var i in data) {
			var point = data[i];
			var point_local = {
				user_id : point.user_id,
				color: '#DD790A',
				x : point.x,
				y : point.y
			}
			if (point.user_id == userId) {
				point_local.color = '#DD790A';
				point.remove();
				//point.update(undefined, true);
				reports.mid_semester_plot.chart.highcharts().series[0].addPoint(point_local);
				reports.mid_semester_plot.chart.highcharts().redraw();
			}
			
		}
		
	}
	
}

$(document).ready(function() {
	reports.showReport('mid_semester_pie');
	reports.showReport('mid_semester_plot');
	
	//locate button
	$('.btn-plot-locate').on('click', function() {
		reports.mid_semester_plot.findById('20150175');
	});
	
});
