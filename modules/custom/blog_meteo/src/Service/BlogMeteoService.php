<?php

namespace Drupal\blog_meteo\Service;

use Curl\Curl;
use stdClass;

class BlogMeteoService {

  private $key = 'e1e512127f2c75c0c7926078a8b8a3b6';
  private const DESCRIPTION_EN_FR = [
    
    'clear sky' => 'ciel clair',
    'few clouds' => 'quelques nuages',
    'scattered clouds' => 'nuages ​​dispersés',
    'broken clouds' => 'nuages ​​brisés',
    'shower rain' => 'quelques averses',
    'rain' => 'pluie',
    'thunderstorm' => 'orage',
    'snow' => 'neige',
    'mist' => 'brouillard',

    'thunderstorm with light rain' => 'orage avec pluie légère',
    'thunderstorm with rain' => 'orage avec pluie',
    'thunderstorm with heavy rain' => 'orage avec de fortes pluies',
    'light thunderstorm' => 'orage léger',
    'thunderstorm' => 'orage',
    'heavy thunderstorm' => 'orage violent',
    'ragged thunderstorm' => 'orage irrégulier',
    'thunderstorm with light drizzle' => 'orage avec légère bruine',
    'thunderstorm with drizzle' => 'orage avec bruine',
    'thunderstorm with heavy drizzle' => 'orage avec de fortes bruines',

    'light intensity drizzle' => 'bruine d\'intensité lumineuse',
    'drizzle' => 'bruine',
    'heavy intensity drizzle' => 'bruine intense',
    'light intensity drizzle rain' => 'faible intensité bruine pluie',
    'drizzle rain' => 'bruine pluie',
    'heavy intensity drizzle rain' => 'pluie forte intensité bruine',
    'shower rain and drizzle' => 'averse pluie et bruine',
    'heavy shower rain and drizzle' => 'forte averse de pluie et bruine',
    'shower drizzle' => 'bruine de douche',

    'light rain' => 'pluie légère',
    'moderate rain' => 'pluie modérée',
    'heavy intensity rain' => 'pluie forte',
    'very heavy rain' => 'très forte pluie',
    'extreme rain' => 'pluie extrême',
    'freezing rain' => 'pluie verglaçante',
    'light intensity shower rain' => 'faible intensité averse pluie',
    'shower rain' => 'quelques averses',
    'heavy intensity shower rain' => 'forte intensité averse pluie',
    'ragged shower rain' => 'pluie de pluie en lambeaux',

    'light snow' => 'neige légère',
    'Snow' => 'neige',
    'Heavy snow' => 'Beaucoup de neige',
    'Sleet' => 'Neige fondue',
    'Light shower sleet' => 'Grésil léger',
    'Shower sleet' => 'Grésil de douche',
    'Light rain and snow' => 'Légère pluie et neige',
    'Rain and snow' => 'Pluie et neige',
    'Light shower snow' => 'Légère averse de neige',
    'Shower snow' => 'Averse de neige',
    'Heavy shower snow' => 'Forte averse de neige',

    'Mist' => 'Brouillard',
    'Smoke' => 'Fumée',
    'Haze' => 'Brume',
    'Dust' => 'Poussière',
    'Fog' => 'Brouillard',
    'Sand' => 'Le sable',
    'Dust' => 'Poussière',
    'Ash' => 'Cendre',
    'Squall' => 'Bourrasque',
    'Tornado' => 'Tornade',

    'clear sky' => 'ciel clair',

    'few clouds: 11-25%' => 'quelques nuages: 11-25%',
    'scattered clouds: 25-50%' => 'nuages ​​épars: 25-50%',
    'broken clouds: 51-84%' => 'nuages ​​fragmentés: 51-84%',
    'overcast clouds: 85-100%' => 'couvert nuageux: 85-100%',

    'overcast clouds' => 'couvert nuageux'
  ];

  public function getWeatherByCity($city, $country) {

    // Objet de retour
    $return = new stdClass();
    $return->erreur = false;
    $return->message = 'OK';

    // Objet de requete
    $curl = new Curl();

    // 5 days : "/forecast" instead of "/weather"
    // http://api.openweathermap.org/data/2.5/weather?q=belligne,fr&APPID=e1e512127f2c75c0c7926078a8b8a3b6
    $api = $curl->get('http://api.openweathermap.org/data/2.5/weather?q=' . $city . ',' . $country . '&APPID=' . $this->key);
    
    // Erreur de l'API "OpenWeatherMap"
    if (!($api instanceof Curl)) {
      $return->erreur = true;
    }
    // Retour OK de la part de l'API "OpenWeatherMap"
    elseif ($api->response && is_string($api->response)) {

      // Get data
      $data_array = json_decode($api->response, true);

      // Ville non trouvée
      if ($data_array['cod'] == 404 && $data_array['message'] == 'city not found') {
        $return->message = 'Ville non trouvable';
      } else {
        
        // Weather
        $weather = $data_array['weather'];
        
        // Icon
        $icon = $weather[0]['icon'];

        // Icon for twig
        $icon_twig = 'http://openweathermap.org/img/wn/'.$icon.'@4x.png';
        
        $return->icon = $icon_twig;
        $return->city = $city;

        // Description
        $descriptif = $weather[0]['description'];
        $descriptifs_francais = array_keys(self::DESCRIPTION_EN_FR);

        if (in_array($descriptif, $descriptifs_francais)) {
          $return->descriptif = ucfirst(self::DESCRIPTION_EN_FR[$descriptif]);
        } else {
          $return->descriptif = ucfirst($descriptif);
        }

        // Température (Kelvi => Celsius)
        $main = $data_array['main']; 
        $return->temperature = floatval($main['temp']) - 273.15;   
        $return->temperature = round($return->temperature);

        // Vent Mètres/s => Km /h(x 3,273)
        $wind = $data_array['wind'];
        $return->vent = floatval($wind['speed']) * 3.273;
        $return->vent = round($return->vent);
      }
    }
    
    return $return;
  }

}
