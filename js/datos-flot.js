jQuery(document).ready(function($){
$(function(){

console.log(datahombres);
var datahombres = JSON.parse(drupalSettings.mapas_estadisticos.clases_flot.datosh); 
var datamujeres = JSON.parse(drupalSettings.mapas_estadisticos.clases_flot.datosm);
var datatodos = JSON.parse(drupalSettings.mapas_estadisticos.clases_flot.datost);

    var datasets = {
			'todos': {
				label: 'Todos',
				data: datatodos
			},
			'hombres': {
				label: 'Hombres',
				data: datahombres
			},
			'mujeres': {
				label: 'Mujeres',
				data: datamujeres
			},        
    };
    
console.log (datasets);

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});

		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append("<br/><input type='checkbox' name='" + key +
				"' checked='checked' id='id" + key + "'></input>" +
				"<label for='id" + key + "'>"
				+ val.label + "</label>");
		});

		choiceContainer.find("input").click(plotAccordingToChoices);

		function plotAccordingToChoices() {

			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});

			if (data.length > 0) {
				$.plot("#placeholder", data, {
					yaxis: {
						min: 0
					},
					xaxis: {
						tickDecimals: 0
					}
				});
			}
		}

		plotAccordingToChoices();

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});
});
