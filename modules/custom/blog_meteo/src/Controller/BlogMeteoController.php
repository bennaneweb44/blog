<?php

namespace Drupal\blog_meteo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\blog_meteo\Service\BlogMeteoService;

use Drupal\Core\Link;
use Drupal\Core\Url;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

class BlogMeteoController extends ControllerBase {

  private $blogMeteoService;

  public function __construct() 
  {
      // DI (Dependency Injection)
      $this->blogMeteoService = new BlogMeteoService();
  }

  public function index() 
  {
    $meteo =  \Drupal::service('blog_meteo.BlogMeteoService')->getWeatherByCity('Nantes', 'fr');
    
    $form_aturocomplete_villes =  \Drupal::formBuilder()->getForm('Drupal\blog_meteo\Form\MyAutocompleteForm');

    return [
        '#theme' => 'meteo_theme',

        '#form_aturocomplete_villes' => $form_aturocomplete_villes,

        '#city' => $meteo->city,
        '#icon' => $meteo->icon,
        "#message" => $meteo->message,
        '#descriptif' => $meteo->descriptif,
        '#temperature' => $meteo->temperature,
        '#vent' => $meteo->vent,
        '#boutonsAfficher' => $meteo->boutonsAfficher,
        '#weather4days' => $meteo->weather4days,

        '#attached' => [
            'library' => [
                'blog_meteo/blog-meteo-css',
                'blog_meteo/blog-meteo-js'
            ],
        ],
    ];
  }

  /**
   * Gère les permissions d'accès
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   */
  public function access(AccountInterface $account) {
      return in_array('editor', $account->getRoles())
          ? AccessResult::forbidden()
          : AccessResult::allowed();
  }

}
