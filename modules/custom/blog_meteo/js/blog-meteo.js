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

                $('#product-search-result').empty(); 
                $('#inputVille').val('');
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
          $('.villeTitre').empty();
          $('.villeTitre').html('<span>'+data.city+'</span>');
          
          $('.city').empty();
          $('.city').html(data.descriptif);

          $('.temp').empty();
          $('.temp').html(data.temperature + '° C');

          $('.wind').empty();
          $('.wind').html(data.vent + ' km/h');     
                                
          $('.wi-day-sunny').empty();
          $('.wi-day-sunny').html('<img src="'+data.icon+'" height="110" />');
        }
          

      }      
    }
  
    Drupal.behaviors.blogMeteo = {
      attach: function(context, settings) {          
        init(context);
      }
    };
  
}(jQuery, Drupal, drupalSettings));