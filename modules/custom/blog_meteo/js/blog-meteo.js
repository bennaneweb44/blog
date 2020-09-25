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
              //data: {input_recherche_recette : $('#input_recherche_recette').val()},
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

                $('#product-search-result').empty(); 
                $('#inputVille').val('');
              }
            }); 

          }
        });
        
        
        $('#inputDepartement').change(function(e) {
          $('#inputVille').val('');
        });

      }      
    }
  
    Drupal.behaviors.blogMeteo = {
      attach: function(context, settings) {          
        init(context);
      }
    };
  
}(jQuery, Drupal, drupalSettings));