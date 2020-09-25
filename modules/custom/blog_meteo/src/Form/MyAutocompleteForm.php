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
  private $departements = [];    

  public function __construct() 
  {
    $this->departements['01'] = '01 - Ain'; 
    $this->departements['02'] = '02 - Aisne'; 
    $this->departements['03'] = '03 - Allier'; 
    $this->departements['04'] = '04 - Alpes de Haute Provence'; 
    $this->departements['05'] = '05 - Hautes Alpes'; 
    $this->departements['06'] = '06 - Alpes Maritimes'; 
    $this->departements['07'] = '07 - Ardèche'; 
    $this->departements['08'] = '08 - Ardennes'; 
    $this->departements['09'] = '09 - Ariège'; 
    $this->departements['10'] = '10 - Aube'; 
    $this->departements['11'] = '11 - Aude'; 
    $this->departements['12'] = '12 - Aveyron'; 
    $this->departements['13'] = '13 - Bouches du Rhône'; 
    $this->departements['14'] = '14 - Calvados'; 
    $this->departements['15'] = '15 - Cantal'; 
    $this->departements['16'] = '16 - Charente'; 
    $this->departements['17'] = '17 - Charente Maritime'; 
    $this->departements['18'] = '18 - Cher'; 
    $this->departements['19'] = '19 - Corrèze'; 
    $this->departements['2A'] = '2A - Corse du Sud'; 
    $this->departements['2B'] = '2B - Haute Corse'; 
    $this->departements['21'] = '21 - Côte d\'Or'; 
    $this->departements['22'] = '22 - Côtes d\'Armor'; 
    $this->departements['23'] = '23 - Creuse'; 
    $this->departements['24'] = '24 - Dordogne'; 
    $this->departements['25'] = '25 - Doubs';
    $this->departements['26'] = '26 - Drôme'; 
    $this->departements['27'] = '27 - Eure'; 
    $this->departements['28'] = '28 - Eure et Loir'; 
    $this->departements['29'] = '29 - Finistère'; 
    $this->departements['30'] = '30 - Gard'; 
    $this->departements['31'] = '31 - Haute Garonne'; 
    $this->departements['32'] = '32 - Gers'; 
    $this->departements['33'] = '33 - Gironde'; 
    $this->departements['34'] = '34 - Hérault'; 
    $this->departements['35'] = '35 - Ille et Vilaine'; 
    $this->departements['36'] = '36 - Indre'; 
    $this->departements['37'] = '37 - Indre et Loire'; 
    $this->departements['38'] = '38 - Isère'; 
    $this->departements['39'] = '39 - Jura'; 
    $this->departements['40'] = '40 - Landes'; 
    $this->departements['41'] = '41 - Loir et Cher'; 
    $this->departements['42'] = '42 - Loire'; 
    $this->departements['43'] = '43 - Haute Loire'; 
    $this->departements['44'] = '44 - Loire Atlantique'; 
    $this->departements['45'] = '45 - Loiret'; 
    $this->departements['46'] = '46 - Lot'; 
    $this->departements['47'] = '47 - Lot et Garonne'; 
    $this->departements['48'] = '48 - Lozère'; 
    $this->departements['49'] = '49 - Maine et Loire'; 
    $this->departements['50'] = '50 - Manche'; 
    $this->departements['51'] = '51 - Marne'; 
    $this->departements['52'] = '52 - Haute Marne'; 
    $this->departements['53'] = '53 - Mayenne'; 
    $this->departements['54'] = '54 - Meurthe et Moselle'; 
    $this->departements['55'] = '55 - Meuse'; 
    $this->departements['56'] = '56 - Morbihan'; 
    $this->departements['57'] = '57 - Moselle'; 
    $this->departements['58'] = '58 - Nièvre'; 
    $this->departements['59'] = '59 - Nord'; 
    $this->departements['60'] = '60 - Oise'; 
    $this->departements['61'] = '61 - Orne'; 
    $this->departements['62'] = '62 - Pas de Calais'; 
    $this->departements['63'] = '63 - Puy de Dôme'; 
    $this->departements['64'] = '64 - Pyrénées Atlantiques'; 
    $this->departements['65'] = '65 - Hautes Pyrénées'; 
    $this->departements['66'] = '66 - Pyrénées Orientales'; 
    $this->departements['67'] = '67 - Bas Rhin'; 
    $this->departements['68'] = '68 - Haut Rhin'; 
    $this->departements['69'] = '69 - Rhône-Alpes'; 
    $this->departements['70'] = '70 - Haute Saône'; 
    $this->departements['71'] = '71 - Saône et Loire'; 
    $this->departements['72'] = '72 - Sarthe'; 
    $this->departements['73'] = '73 - Savoie'; 
    $this->departements['74'] = '74 - Haute Savoie'; 
    $this->departements['75'] = '75 - Paris'; 
    $this->departements['76'] = '76 - Seine Maritime'; 
    $this->departements['77'] = '77 - Seine et Marne'; 
    $this->departements['78'] = '78 - Yvelines'; 
    $this->departements['79'] = '79 - Deux Sèvres'; 
    $this->departements['80'] = '80 - Somme'; 
    $this->departements['81'] = '81 - Tarn'; 
    $this->departements['82'] = '82 - Tarn et Garonne'; 
    $this->departements['83'] = '83 - Var'; 
    $this->departements['84'] = '84 - Vaucluse'; 
    $this->departements['85'] = '85 - Vendée'; 
    $this->departements['86'] = '86 - Vienne'; 
    $this->departements['87'] = '87 - Haute Vienne'; 
    $this->departements['88'] = '88 - Vosges'; 
    $this->departements['89'] = '89 - Yonne'; 
    $this->departements['90'] = '90 - Territoire de Belfort'; 
    $this->departements['91'] = '91 - Essonne'; 
    $this->departements['92'] = '92 - Hauts de Seine'; 
    $this->departements['93'] = '93 - Seine St Denis'; 
    $this->departements['94'] = '94 - Val de Marne'; 
    $this->departements['95'] = '95 - Val d\'Oise'; 
    $this->departements['97'] = '97 - DOM'; 
    $this->departements['971'] = '971 - Guadeloupe'; 
    $this->departements['972'] = '972 - Martinique'; 
    $this->departements['973'] = '973 - Guyane'; 
    $this->departements['974'] = '974 - Réunion'; 
    $this->departements['975'] = '975 - Saint Pierre et Miquelon'; 
    $this->departements['976'] = '976 - Mayotte';
  }

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
    /********************** Département sélectionné (défault : 44) *************************/
    $form['departement_select'] = [
      '#type' => 'select',
      '#id' => 'inputDepartement',
      '#options' => $this->departements,     
      '#attributes' => [
        'style' => 'width: 100%; padding: 2px; background-color: burlywood'
      ],
      '#default_value' => 44,
      '#ajax' => [
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'callback' =>'::myAjaxCallBack',
          'wrapper' => 'departement_select',
          'message' => 'Chargement ...'
        ]
      ]
    ];
    
    /***************************** Nom de la ville saisi **********************************/
    $form['ville'] = [
      '#type' => 'textfield',
      '#id' => 'inputVille',
      '#placeholder' => 'Nom d\'une ville',
      '#attributes' => [
        'style' => 'background-color: burlywood; margin-bottom: 1em; max-height: 24px !important;'
      ]
    ];

    /******************************** Liste des résultats **********************************/
    $form['list_items'] = array(
      '#id' => 'product-search-result',
      '#type' => 'item',
      '#markup' => "",
      '#attributes' => [
        'style' => 'background-color: #e3e3e3; font-weight: bold',
        'class' => 'box-for-input'        
      ]
    );
    
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

  public function myAjaxCallBack(array &$form, FormStateInterface $form_state) 
  {
    if ($selectedValue = $form_state->getValue('departement_select')) {
      $selectedText = $form['departement_select']['#options'][$selectedValue];
      $form['departement_select']['#value'] = $selectedText;
    }
    
    return $form['departement_select'];
  }

}