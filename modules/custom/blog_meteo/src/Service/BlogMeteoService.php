<?php

namespace Drupal\blog_meteo\Service;

use Curl\Curl;

class BlogMeteoService {

  private $key = '';

  public function getWeatherByCity($city, $country) {

    $curl = new Curl();
    $get = $curl->get('http://api.openweathermap.org/data/2.5/weather?q=Nantes,fr&APPID=e1e512127f2c75c0c7926078a8b8a3b6');
    dd($get);
  }

}
