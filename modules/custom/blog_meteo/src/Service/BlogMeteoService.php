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

  public function getWeatherByCity($city, $country) 
  {

    // Objet de retour
    $return = new stdClass();
    $return->erreur = false;
    $return->message = 'OK';

    // Objet de requete
    $curl = new Curl();

    // 5 days : "/forecast" instead of "/weather"
    // http://api.openweathermap.org/data/2.5/weather?q=belligne,fr&APPID=e1e512127f2c75c0c7926078a8b8a3b6
    $api = $curl->get('https://api.openweathermap.org/data/2.5/weather?q=' . $city . ',' . $country . '&APPID=' . $this->key);
    
    // Erreur de l'API "OpenWeatherMap"
    if (!($api instanceof Curl)) {
      $return->erreur = true;
    }
    // Retour OK de la part de l'API "OpenWeatherMap"
    elseif ($api->response && is_string($api->response) ) {

      // Get data
      $data_array = json_decode($api->response, true);
      
      $api5days = $curl->get('https://api.openweathermap.org/data/2.5/forecast?q=' . $city . ',' . $country . '&APPID=' . $this->key);

      // Prochaines période
      $boutonsAfficher = array();      
      if ($api5days instanceof Curl) {
        $data_5days_array = json_decode($api5days->response, true);

        if ($data_5days_array['list'] && count($data_5days_array['list'])) {
          $timestamp_matin = strtotime(date('Y-m-d 10:00:00'));
          $timestamp_apres_midi = strtotime(date('Y-m-d 15:00:00'));
          $timestamp_soir = strtotime(date('Y-m-d 21:00:00'));
  
          $timestamp_now = strtotime(date('Y-m-d H:i:s'));
          
          if ($timestamp_now < $timestamp_soir) {
            $boutonsAfficher[] = 'actuelle';
            $boutonsAfficher[] = 'soir';
            if ($timestamp_now < $timestamp_apres_midi) {
              $boutonsAfficher[] = 'apres-midi';
            }
            if ($timestamp_now < $timestamp_matin) {
              $boutonsAfficher[] = 'matin';
            }
          }        
        }
      }

      $return->boutonsAfficher = $boutonsAfficher; 
      
      // Ville non trouvée
      if ($data_array['cod'] == 404 && $data_array['message'] == 'city not found') {
        $return->message = 'Ville non trouvable';
      } else {
        
        // Weather
        $weather = $data_array['weather'];
        
        // Icon
        $icon = $weather[0]['icon'];

        // Icon for twig
        $icon_twig = 'https://openweathermap.org/img/wn/'.$icon.'@4x.png';
        
        $return->icon = $icon_twig;
        $return->city = $city;

        // Description
        $descriptif = $weather[0]['description'];

        if (in_array($descriptif, array_keys(self::DESCRIPTION_EN_FR))) {
          $return->descriptif = ucfirst(self::DESCRIPTION_EN_FR[$descriptif]);
        } elseif (in_array(ucfirst($descriptif), array_keys(self::DESCRIPTION_EN_FR))) {
          $return->descriptif = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($descriptif)]);
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

        /*####################### Weather of 4 days ###########################*/
        $return->weather4days = $this->getWeather4days($data_5days_array);        
      }
    }
    
    return $return;
  }

  public function getWeatherByCityPeriode($city, $country, $periode) 
  {

    // Objet de retour
    $return = new stdClass();
    $return->erreur = false;
    $return->message = 'OK';

    // Objet de requete
    $curl = new Curl();

    // 5 days : "/forecast" instead of "/weather"
    // http://api.openweathermap.org/data/2.5/weather?q=belligne,fr&APPID=e1e512127f2c75c0c7926078a8b8a3b6
    $api5days = $curl->get('https://api.openweathermap.org/data/2.5/forecast?q=' . $city . ',' . $country . '&APPID=' . $this->key);
    
    // Erreur de l'API "OpenWeatherMap"
    if (!($api5days instanceof Curl)) {
      $return->erreur = true;
    }
    // Retour OK de la part de l'API "OpenWeatherMap"
    elseif ($api5days->response && is_string($api5days->response)) {

      // Get data
      $data_array = json_decode($api5days->response, true);

      // Ville non trouvée
      if ($data_array['cod'] == 404 && $data_array['message'] == 'city not found') {
        $return->message = 'Ville non trouvable';
      } else {

        $donnee = null;
        
        foreach($data_array['list'] as $elem) {

          if ($periode == 'actuelle') {
            $api5days = $curl->get('https://api.openweathermap.org/data/2.5/weather?q=' . $city . ',' . $country . '&APPID=' . $this->key);
            $data_array = json_decode($api5days->response, true);
          } else {
            if ($periode == 'matin' && $elem['dt'] >= strtotime(date('Y-m-d'). '09:00:00') && $elem['dt'] < strtotime(date('Y-m-d'). '12:00:00')) {
              $donnee = $elem;
              break;  
            } elseif ($periode == 'apres-midi' && $elem['dt'] > strtotime(date('Y-m-d'). '12:00:00') && $elem['dt'] <= strtotime(date('Y-m-d'). '16:00:00')) {
              $donnee = $elem;
              break;  
            } elseif ($periode == 'soir' && $elem['dt'] > strtotime(date('Y-m-d'). '20:00:00')) {
              $donnee = $elem;
              break;  
            }
          }
        }
       
        // Weather
        if ($donnee) {
          $weather = $donnee['weather'];  
        } else {
          $weather = $data_array['weather'];
        }
        
        
        // Icon
        $icon = $weather[0]['icon'];

        // Icon for twig
        $icon_twig = 'https://openweathermap.org/img/wn/'.$icon.'@4x.png';
        
        $return->icon = $icon_twig;
        $return->city = $city;

        // Description
        $descriptif = $weather[0]['description'];
        $descriptifs_francais = array_keys(self::DESCRIPTION_EN_FR);

        if (in_array($descriptif, $descriptifs_francais)) {
          $return->descriptif = ucfirst(self::DESCRIPTION_EN_FR[$descriptif]);
        } elseif (in_array(ucfirst($descriptif), $descriptifs_francais)) {
          $return->descriptif = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($descriptif)]);
        } else {
          $return->descriptif = ucfirst($descriptif);
        }

        // Température (Kelvi => Celsius)        
        if ($donnee) {
          $main = $donnee['main']; 
        } else {
          $main = $data_array['main'];
        }

        $return->temperature = floatval($main['temp']) - 273.15;   
        $return->temperature = round($return->temperature);

        // Vent Mètres/s => Km /h(x 3,273)        
        if ($donnee) {
          $wind = $donnee['wind']; 
        } else {
          $wind = $data_array['wind'];
        }
        $return->vent = floatval($wind['speed']) * 3.273;
        $return->vent = round($return->vent);
      }
    }
    
    return $return;
  }

  private function getWeather4days($array_data_5_days)
  {    
    $retour = [
      0 => [],
      1 => [],
      2 => [],
      3 => []
    ];

    foreach($array_data_5_days['list'] as $item) {

      /*dump(date('Y-m-d H:i:s', strtotime(' +1 day'. ' 06:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +1 day'. ' 12:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +1 day'. ' 18:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +1 day'. ' 23:00:00')));

      dump(date('Y-m-d H:i:s', strtotime(' +2 day'. ' 06:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +2 day'. ' 12:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +2 day'. ' 18:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +2 day'. ' 23:00:00')));

      dump(date('Y-m-d H:i:s', strtotime(' +3 day'. ' 06:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +3 day'. ' 12:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +3 day'. ' 18:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +3 day'. ' 23:00:00')));

      dump(date('Y-m-d H:i:s', strtotime(' +4 day'. ' 06:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +4 day'. ' 12:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +4 day'. ' 18:00:00')));
      dump(date('Y-m-d H:i:s', strtotime(' +4 day'. ' 23:00:00')));*/

      switch ($item['dt_txt']) {

        /*########### J + 1 ###########*/
        case date('Y-m-d H:i:s', strtotime(' +1 day'. ' 12:00:00'));
        
          $retour[0]['date'] = $this->dateJourAnglaisToFrancais(date('D, d/m', strtotime($item['dt_txt'])));    
          
          // Icon     
          $retour[0]['matin']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[0]['matin']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[0]['matin']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[0]['matin']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +1 day' . ' 18:00:00'));      

          // Icon     
          $retour[0]['apres-midi']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[0]['apres-midi']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[0]['apres-midi']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[0]['apres-midi']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          break;

        case date('Y-m-d H:i:s', strtotime(' +1 day' . ' 21:00:00')):       

          // Icon     
          $retour[0]['soir']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[0]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[0]['soir']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[0]['soir']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[0]['soir']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          break;

        /*########### J + 2 ###########*/
        case date('Y-m-d H:i:s', strtotime(' +2 day' . ' 12:00:00'));
          $retour[1]['date'] = $this->dateJourAnglaisToFrancais(date('D, d/m', strtotime($item['dt_txt'])));          

          // Icon     
          $retour[1]['matin']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[1]['matin']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[1]['matin']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[1]['matin']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +2 day' . ' 18:00:00'));         

          // Icon     
          $retour[1]['apres-midi']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[1]['apres-midi']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[1]['apres-midi']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[1]['apres-midi']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +2 day' . ' 21:00:00')):         

          // Icon     
          $retour[1]['soir']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[1]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[1]['soir']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[1]['soir']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[1]['soir']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;

        /*########### J + 3 ###########*/
        case date('Y-m-d H:i:s', strtotime(' +3 day' . ' 12:00:00'));
          $retour[2]['date'] = $this->dateJourAnglaisToFrancais(date('D, d/m', strtotime($item['dt_txt'])));          

          // Icon     
          $retour[2]['matin']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[2]['matin']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[2]['matin']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[2]['matin']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +3 day' . ' 18:00:00'));        

          // Icon     
          $retour[2]['apres-midi']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[2]['apres-midi']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[2]['apres-midi']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[2]['apres-midi']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +3 day' . ' 21:00:00')):
          // Icon     
          $retour[2]['soir']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[2]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[2]['soir']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[2]['soir']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[2]['soir']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
          

        /*########### J + 4 ###########*/
        case date('Y-m-d H:i:s', strtotime(' +4 day' . ' 12:00:00'));
          $retour[3]['date'] = $this->dateJourAnglaisToFrancais(date('D, d/m', strtotime($item['dt_txt'])));          

          // Icon     
          $retour[3]['matin']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['matin']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[3]['matin']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[3]['matin']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[3]['matin']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +4 day' . ' 18:00:00'));         

          // Icon     
          $retour[3]['apres-midi']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['apres-midi']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[3]['apres-midi']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[3]['apres-midi']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[3]['apres-midi']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
        case date('Y-m-d H:i:s', strtotime(' +4 day' . ' 21:00:00')):        

          // Icon     
          $retour[3]['soir']['icone'] = 'https://openweathermap.org/img/wn/'.$item['weather'][0]['icon'].'@4x.png';          
          
          // descriptif
          if (in_array($item['weather'][0]['description'], array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[$item['weather'][0]['description']]);
          } elseif (in_array(ucfirst($item['weather'][0]['description']), array_keys(self::DESCRIPTION_EN_FR))) {
            $retour[3]['soir']['descriptif']  = ucfirst(self::DESCRIPTION_EN_FR[ucfirst($item['weather'][0]['description'])]);
          } else {
            $retour[3]['soir']['descriptif']  = ucfirst($item['weather'][0]['description']);
          }

          // température
          $retour[3]['soir']['temperature'] = round(floatval($item['main']['temp']) - 273.15);   

          // Vent
          $retour[3]['soir']['vent'] = round(floatval($item['wind']['speed']) * 3.273);
          
          break;
      }
    }
    
    return $retour;
  }

  private function dateJourAnglaisToFrancais($date_anglaise)
  {    
    $jours_anglais_francais = [
      'Mon' => 'Lun',
      'Tue' => 'Mar',
      'Wed' => 'Mer',
      'Thu' => 'Jeu',
      'Fri' => 'Ven',
      'Sat' => 'Sam',
      'Sun' => 'Dim'
    ];

    foreach($jours_anglais_francais as $cle => $val) {
      if (strpos($date_anglaise, $cle) !== FALSE) {        
        $date_anglaise = str_replace($cle, $val, $date_anglaise);
        break;  
      }
    }

    return $date_anglaise;
  }

}
