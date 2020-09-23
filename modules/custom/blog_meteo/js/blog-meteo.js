(function($, Drupal, drupalSettings) {

    var initialized;
  
    function init(context) {

      if (!initialized) {
        initialized = true;

        // Ajax Request
        $('#inputVille').keyup(function(e) {

          e.preventDefault();
          e.stopPropagation();
        
          var term = $('#inputVille').val();
          var departement = $('#inputDepartement').val();

          if (term.trim() != '') {

            let host = drupalSettings.path.baseUrl;

            $.ajax({
              dataType: "JSON",
              type: "GET",          
              url: host + "/autocomplete/villes/"+departement+"/term/"+term,          
              success: function (data) {
                
                let content = '<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" >';
                let i = 0;
                for ( ; data[i]; ) {                
                  content += '<li class="ui-menu-item reloadCityWeather" style="cursor: pointer; list-style-type: none;" data-city="'+ data[i] +'">' + data[i] + '</li>';
                  i++;
                }
                content += '</ul>';
                
                $('#product-search-result').html(content);  

                $('.reloadCityWeather').click(function(e) {
                  e.preventDefault();
                  
                  let ville = $(this).attr('data-city');

                  $.ajax({
                    dataType: "JSON",
                    type: "GET",          
                    url: drupalSettings.path.baseUrl + "/services/meteo/update/" + ville,          
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

                });
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