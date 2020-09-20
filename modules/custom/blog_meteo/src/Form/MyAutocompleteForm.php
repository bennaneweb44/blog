<?php

namespace Drupal\blog_meteo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;

/**
 * Class MyAutocompleteForm
 * @package Drupal\blog_meteo\Form
 */
class MyAutocompleteForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'meteo_autocomplete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['article'] = [
      '#type' => 'textfield',
      '#placeholder' => 'Nom d\'une ville',
      '#attributes' => [
        'style' => 'background-color: burlywood;'
      ],
      '#autocomplete_route_name' => 'blog_meteo.autocomplete',
    ];
    $form['actions'] = ['#type' => 'actions'];
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $article_id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($form_state->getValue('article'));
    \Drupal::messenger()->addMessage('Article ID is ' . $article_id);
  }


}