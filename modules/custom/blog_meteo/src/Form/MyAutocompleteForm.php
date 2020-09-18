<?php

namespace Drupal\blog_meteo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;

/**
 * Class MyAutocompleteForm
 * @package Drupal\blog_meteo\Form
 */
class MeteoAutocompleteForm extends FormBase
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
      '#title' => $this->t('Autocomplete Articles'),
      '#autocomplete_route_name' => 'blog_meteo.autocomplete',
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
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