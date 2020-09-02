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

  public function __construct() {
      // DI (Dependency Injection)
      $this->blogMeteoService = new BlogMeteoService();
  }

  public function hello() {

      //$salutation = 'Ciao';

      // Instanciation directe du service
      //$demoService = new DemoService();
      //$salutation = $demoService->getSalutation();

      // Utilisation de l'objet contenant le service injecté
      //$salutation = $this->blogMeteoService->getSalutation();

      // Consommation du service par le service container de Drupal
      // https://www.drupal.org/docs/8/api/services-and-dependency-injection/services-and-dependency-injection-in-drupal-8
      // $salutation =
      // '<p class="greet">' . \Drupal::service('demo.demoService')->getSalutation() . '</p>';

      $salutation =  \Drupal::service('blog_meteo.BlogMeteoService')->getWeatherByCity('Nantes', 'fr');
      $students = ['Student 1','Student 2','Student 3'];

      // tableau associatif fourni au moteur de rendu
      // https://www.drupal.org/docs/drupal-apis/render-api/render-arrays
      // return [
      //     '#markup' => $salutation,
      //     '#attached' => [
      //         'library' => [
      //             'demo/super-lib',
      //         ]
      //     ]
      // ];

      return [
          '#theme' => 'container',
          '#attributes' => ['class' => ['greet', 'test']],
          '#children' => [
              '#markup' => $salutation
          ],
          '#attached' => ['library' => ['blog_meteo/super-lib']]
      ];

      // utilisation d'un thème du core de Drupal
      // return [
      //     '#theme' => 'item_list',
      //     '#items' => $students
      // ];

      // utilisation d'un thème personnalisé (défini dans le hook_theme du fichier demo.module)
      // return [
      //     '#theme' => 'demo_theme_hook',
      //     '#variable1' => 'Texte en provenance du controlleur',
      //     '#students' => $students
      // ];
  }

  public function helloSymfony() {
      $student = [
          'firstname' => 'Rémi',
          'lastname' => 'André'
      ];

      // réponse directe sans passer par le moteur de rendu
      //return new \Symfony\Component\HttpFoundation\Response('Hello SF');
      return new \Symfony\Component\HttpFoundation\Response(json_encode($student));
  }

  public function helloBis($salutation, $action) {
      $res = new Response();

      if ($salutation == 'coucou') {
          $res->setContent('Coucou à toi aussi');
      } else {
          $res->setContent($salutation);
      }

      return $res;
  }

  public function square($width, $color) {

      drupal_flush_all_caches();

      // $factory = \Drupal::service('config.factory');
      // $config = $factory->get('demo.blocks_config');
      // $message = $config->get('message');

      // équivalent plus direct (depuis un controlleur)
      $message = $this->config('blog_meteo.square_message')->get('message');

      $res = new Response();

      if (intval($width) > 500) {
          $res->setContent('La largeur doit être inférieure à 500');
      } else {
          $square = '<div style="width:'.$width .'px;height:'.$width.'px;background-color:#'.$color.'">'.$message.'</div>';
          $res->setContent($square);
      }

      return $res;
  }

  public function generateForm() {

      $form = \Drupal::formBuilder()->getForm('\Drupal\blog_meteo\Form\BlogMeteoForm', NULL);

      $build = array();
      //$build['#markup'] = '<p>waiting..</p>';
      $build['phone'] = array(
          '#type' => 'tel',
          '#title' => $this->t('Your phone number'),
      );
      $build['forms'] = array(
          'f1' => $form,
          'f2' => $form
      );
      return $build;
  }


  /**
   * Gère
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


  // ** Manipulation des noeuds **
  public function nodeTest() {

      // https://agaric.coop/blog/creating-links-code-drupal-8

      $link = Link::createFromRoute('This is a link', 'blog_meteo.hello');
      $link2 = Link::fromTextAndUrl('This is a link', Url::fromRoute('blog_meteo.hello'));
      $link3 = $link2->toRenderable();
      return Link::fromTextAndUrl('Link cusom', Url::fromRoute('entity.node.canonical', ['node' => 3]))
          ->toRenderable();

      //$this->createNode(); // création
      //$this->updateNode(); // mise à jour
      //$this->deleteNode();

      $nids = $this->readNode();
      //var_dump($nids);

      // chargement d'un seul node
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nids[2]); // NodeInterface
      //return new Response('<p>' . $node->getTitle() . '</p>');

      // chargement multiple
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

      $str = '';
      $items = array();
      foreach($nodes as $node) {
          $str .= '<p>' . $node->getTitle() . '</p>';
          // le code html ne sera pas interprété par cette approche
          //array_push($items, '<a href="/node/'.$node->id().'">'. $node->getTitle() .'</a>');
          array_push($items, $node->getTitle());
      }
      //return new Response($str);
      $render = [
          '#theme' => 'item_list',
          '#items' => $items
      ];

      return $render;
      //return $this->renderNode();
      //return $this->renderNodeMultiple();

  }

  private function readNode() {
      $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
      $query
          //->condition('type', 'article')
          ->condition('status', TRUE)
          ;
      $result = $query->execute();
      return $result;
  }

  private function createNode() {
      $values = [
          'type' => 'article',
          'title' => 'My title 2'
      ];
      $node = \Drupal::entityTypeManager()->getStorage('node')->create($values);
      $node->save();

  }

  private function updateNode() {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1); // nid 1
      $node->set('title', 'Article 1 updated');
      $node->set('body', 'Article 1 body updated');
      $node->save();
  }

  private function deleteNode() {
      \Drupal::entityTypeManager()->getStorage('node')->load(4)->delete();
  }

  private function renderNode() {

      /** @var \Drupal\node\NodeInterface $node */
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1);

      /** @var \Drupal\node\NodeViewBuilder $builder */
      $builder = \Drupal::entityTypeManager()->getViewBuilder('node');

      $build = $builder->view($node);
      //$build['#theme'] = $build['#theme'] . '__my_suggestion'; // overriting du template de base
      return $build;
  }

  private function renderNodeMultiple() {

      /** @var \Drupal\node\NodeInterface $node */
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple([1,3]);

      /** @var \Drupal\node\NodeViewBuilder $builder */
      $builder = \Drupal::entityTypeManager()->getViewBuilder('node');

      $build = $builder->viewMultiple($nodes);

      return $build;
  }

}
