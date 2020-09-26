(function($, Drupal, drupalSettings) {

    var initialized;
  
    function init(context) {

      if (!initialized) {
        initialized = true;

        $("#inputVille").autocomplete({        
          source: function(request, response) {

            var departement = $('#inputDepartement').val();            
            var term = $('#inputVille').val();

            if (term.trim() == '') {
              term = 'xxxxxxxxxxxxx';
            }

            $.ajax({
              type: 'GET',
              url: drupalSettings.path.baseUrl + "autocomplete/villes/" + departement + "/term/" + term,
              dataType: "json",
              success: function(data) {
                  response(data);
              }
            });
              
          },
          min_length: 3,
          delay: 10,
          select: function(e, ui) {    
            ville = ui.item.value;

            $.ajax({
              dataType: "JSON",
              type: "GET",          
              url: drupalSettings.path.baseUrl + "services/meteo/update/" + ville,          
              success: function (data) {

                refreshDataWeatherCity(data);
                refreshDataWeather4days(data);

                $('#product-search-result').empty(); 
                $('#inputVille').val('');
                
                $( "#div-periodes > div" ).each(function( index ) {
                  $(this).removeClass('moment-actif');
                  if ($(this).attr('data-periode') == 'actuelle') {
                    $(this).addClass('moment-actif');
                  }                   
                });
              }
            }); 

          }
        });
        
        
        $('#inputDepartement').change(function(e) {
          $('#inputVille').val('');
        });


        /*########### Période de la journée (Matin, après-midi et soir ############*/
        $('#div-periodes > div').click(function() {            
          let periode = $(this).attr('data-periode');
          let ville = $('.villeTitre > span').html();
          let div = $(this);
          
          $.ajax({
            dataType: "JSON",
            type: "GET",          
            url: drupalSettings.path.baseUrl + "services/meteo/update/" + ville + "/periode/" + periode,
            success: function (data) {

              refreshDataWeatherCity(data);

              $( "#div-periodes > div" ).each(function( index ) {
                $(this).removeClass('moment-actif');
              });

              div.addClass('moment-actif');
            }
          }); 
        });

        function refreshDataWeatherCity(data)
        {
          $('#villeTitre').empty();
          $('#villeTitre').html('<span>'+data.city+'</span>');
          
          $('#villeDescriptif').empty();
          $('#villeDescriptif').html(data.descriptif);

          $('#villeTemperature').empty();
          $('#villeTemperature').html(data.temperature + '° C');

          $('#villeVent').empty();
          $('#villeVent').html(data.vent + ' km/h');     
                                
          $('#villeIcone').empty();
          $('#villeIcone').html('<img src="'+data.icon+'" height="110" />');
        }        
        
        function refreshDataWeather4days(data)
        {          
          $.each( data.weather4days, function( key, value ) {

              /*################# Matin ##################*/            
              $('#matin_descriptif_'+key).empty();
              $('#matin_descriptif_'+key).html(value['matin']['descriptif']);

              $('#matin_temp_vent_'+key).empty();              
              $('#matin_temp_vent_'+key).html(value['matin']['temperature'] + '° C, ' + value['matin']['vent'] + ' km/h');
              
              $('#matin_icone_'+key).attr('src', '');
              $('#matin_icone_'+key).attr('src', value['matin']['icone']);

              /*################# Après-midi ##################*/
              $('#aprem_descriptif_'+key).empty();
              $('#aprem_descriptif_'+key).html(value['apres-midi']['descriptif']);

              $('#aprem_temp_vent_'+key).empty();
              $('#aprem_temp_vent_'+key).html(value['apres-midi']['temperature'] + '° C, ' + value['apres-midi']['vent'] + ' km/h')
              
              $('#aprem_icone_'+key).attr('src', '');
              $('#aprem_icone_'+key).attr('src', value['apres-midi']['icone']);  

              /*################# Soir ##################*/
              $('#soir_descriptif_'+key).empty();
              $('#soir_descriptif_'+key).html(value['soir']['descriptif']);

              $('#soir_temp_vent_'+key).empty();
              $('#soir_temp_vent_'+key).html(value['soir']['temperature'] + '° C, ' + value['soir']['vent'] + ' km/h')
              
              $('#soir_icone_'+key).attr('src', '');
              $('#soir_icone_'+key).attr('src', value['soir']['icone']);        
          });
        }

      }      
    }
  
    Drupal.behaviors.blogMeteo = {
      attach: function(context, settings) {          
        init(context);
      }
    };
  
}(jQuery, Drupal, drupalSettings));