var reports = {

	conf : {
		range: 29, //default range in days
		userId : false,
		advanced: false,
		defaultReportType : 'clicks',
		loadedModules : [],
		sendData: {}
	},
	
	element : false, //chart element
	
	init : function() {
	
		$('#clicks-chart').css('height', $(window).height() * 0.5);
		
		//load the default conf
		reports.conf.range = $('#rangeSelect').val();
		
		$('#reportUser').val('');
		
		$('#rangeSelect').on('change', function() {
			reports.conf.range = parseInt($(this).val());
			reports.showReport();
		});
		
		$('#reportUser').on('keyup', function(e) {
			if (e.which == 13) {
				reports.conf.userId = $(this).val();
				reports.showAllReports();
			}
		});
		
		$('#report-user-apply').on('click', function() {
			reports.conf.userId = $('#reportUser').val();
			reports.showAllReports();
		});
		
		$('#report-conf').on('submit', function(e) {
			e.preventDefault();
		});
		
		//advanced date search
		$('#rDateFrom, #rDateTo').val('');
		$('#rDateFrom, #rDateTo').datepicker({
			format: 'yyyy-mm-dd'
		}).on('changeDate', function() {
			console.log('date change');
			if ($('#rDateFrom').val() != '' && $('#rDateTo').val() != '' && $('.btn-r-advanced').hasClass('active')) {
				reports.conf.advanced = true;
				reports.conf.from = $('#rDateFrom').val();
				reports.conf.to = $('#rDateTo').val();
				reports.showReport();
			} else {
				reports.conf.advanced = false;
			}
		});
		
		
		$('.btn-r-advanced').on('click', function() {
			reports.conf.advanced = $(this).hasClass('active');
		});
		
	},
	
	showReport : function(reportType) {
	
		if (typeof reportType === 'undefined') {
			reportType = reports.conf.defaultReportType;
		}
		
		if (typeof reports[reportType] === 'undefined') {
			alert('Unknown report type');
			return false;
		}
		
		var module = reports[reportType];
	
		var data = {
			userId : reports.conf.userId,
			advanced: reports.conf.advanced + 0
		}
		
		if (reports.conf.advanced == true) {
			data.from = reports.conf.from;
			data.to = reports.conf.to;
		} else {
			data.range = reports.conf.range;
		}
		
		data.additional_data = reports.conf.sendData;
		
		data.report_type = reportType;
		
		//range : reports.conf.range,
	
		$.ajax({
			url: 'report.php',
			data: data,
			dataType: 'json',
			success: function(res) {
				reports.conf.data = res;
				
				if (res.error == 1) {
					alert(res.data);
				} else {
					if (typeof module.prepareData === 'function') {
						module.prepareData();
					}
					module.showGraph(res);
					
					$('body').trigger('rep.chart_drawn');
					
				}	
			    
			}
		});
	
	},
	
	//show reports for all loaded modules
	showAllReports : function() {
		for (var i in reports.conf.loadedModules) {
			var module = reports.conf.loadedModules[i];
			reports.showReport(module);
		}
	}

}

$(document).ready(function() {
	reports.init();
});
