<?php

namespace Drupal\blog_meteo\Controller;

use Drupal\Core\Entity\Element\EntityAutocomplete;
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
  public function handleAutocomplete(Request $request)
  {
    $results = [];
    $input = $request->query->get('q');
    if (!$input) {
      return new JsonResponse($results);
    }
    $input = Xss::filter($input);

    $results[] = [
        'value' => $input,
        'label' => $input,
    ];

    return new JsonResponse($results);
  }

}