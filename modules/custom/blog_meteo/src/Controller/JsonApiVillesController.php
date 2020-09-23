<?php

namespace Drupal\blog_meteo\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;

/**
 * Class JsonApiVillesController
 * @package Drupal\blog_meteo\Controller
 */
class JsonApiVillesController
{

  /**
   * @return JsonResponse
   */
  public function handleAutocomplete(Request $request, $departement, $term)
  {
    $results = [];

    if (!$term || !$departement) {
      return new JsonResponse($results);
    }

    $term = Xss::filter($term);
    $departement = Xss::filter($departement);

    // Request
    $con = \Drupal\Core\Database\Database::getConnection();
    $query = $con->select('villes_france_free', 'vff')
        ->fields('vff', ['ville_nom']);
    $query->condition('ville_nom', $query->escapeLike($term) . "%", 'LIKE');
    $query->condition('ville_departement', $departement);
    $objects = $query->execute()->fetchAll();

    foreach($objects as $obj) {
      $results[] = $obj->ville_nom;
    }

    return new JsonResponse($results);
  }

  /**
   * @return JsonResponse
   */
  public function updateWeatherVille($ville)
  {
    $results = [];

    if (!$ville) {
      return new JsonResponse($results);
    }

    $term = Xss::filter($ville);

    // Request
    $meteo =  \Drupal::service('blog_meteo.BlogMeteoService')->getWeatherByCity($term, 'fr');

    return new JsonResponse($meteo);
  }

}