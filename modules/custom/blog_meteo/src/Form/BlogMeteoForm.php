<?php
namespace Drupal\blog_meteo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class BlogMeteoForm extends FormBase {

    public function getFormId() {
        // Unique ID of the form.
        return 'blog_meteo_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        //https://www.drupal.org/docs/8/api/state-api/overview
        //ksm(\Drupal::state()->get('blog_meteo.phone'));

        $form['phone'] = array(
            '#type' => 'tel',
            '#title' => $this->t('Your phone number'),
        );
        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Your email'),
        );
        $form['save'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
        );
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        // Validate submitted form data.
        //ksm('1');
        //ksm($form_state->getValue('phone'));

    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Validate submitted form data.
        //ksm('2');
        //ksm($form_state->getValue('phone'));
        dd('here');
        \Drupal::state()->set('blog_meteo.phone', $form_state->getValue('phone'));
    }
}
