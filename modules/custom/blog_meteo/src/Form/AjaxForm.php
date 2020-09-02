<?php

namespace Drupal\blog_meteo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class AjaxForm extends FormBase {

  public function getFormId() {
    return 'blog_meteo_ajax_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['example_select'] = [

      '#type' => 'select',
      '#title' => $this->t('Select element'),
      '#options' => [
        '1' => $this->t('Uno'),
        '2' => $this->t('Due'),
        '3' => $this->t('Tre'),
      ],

      '#ajax' => [
        'callback' => '::myAjaxCallBack',
        'event' => 'change',
        'wrapper' => 'edit-output', // élément qui sera mis à jour par la callback
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Checking entry...')
        ]
      ]
    ];

    $form['output'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#disabled' => TRUE,
      '#value' => 'Ciao drupal',
      '#prefix' => '<div id="edit-output">',
      '#suffix' => '</div>'
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  public function myAjaxCallBack(array &$form, FormStateInterface $form_state) {

    if ($selectedValue = $form_state->getValue('example_select')) {
      $selectedText = $form['example_select']['#options'][$selectedValue];
      $form['output']['#value'] = $selectedText;
    }

    return $form['output'];
  }
}
