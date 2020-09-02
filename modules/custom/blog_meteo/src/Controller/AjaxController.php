<?php
namespace Drupal\blog_meteo\Controller;

use Drupal\Core\Controller\ControllerBase;
use  \Symfony\Component\HttpFoundation\Response;

class AjaxController extends ControllerBase {

    public function testJs() {

      $render = [
        '#theme' => 'meteo_theme',
        '#variable1' => 'Test JS',
        '#students' => NULL
      ];

      return $render;
    }

    public function testJquery() {

      $render = [
        '#markup' => '<div class="clock"></div>',
        '#attached' => [
          'library' => [
            'blog_meteo/blog-meteo'
          ]
        ]
      ];

      return $render;
    }

}
