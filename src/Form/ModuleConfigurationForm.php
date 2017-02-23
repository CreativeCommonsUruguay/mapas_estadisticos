<?php

namespace Drupal\mapas_estadisticos\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mapas_estadisticos_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mapas_estadisticos.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

  global $base_url;

    $config = \Drupal::service('config.factory')->getEditable('mapas_estadisticos.settings');
    $form['n_entidades_territoriales'] = array(
      '#type' => 'number',
      '#title' => $this->t('Número de entidades territoriales'),
      '#default_value' => $config->get('n_entidades_territoriales'),
    );


    $form['contorno_exterior'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Contorno exterior'),
      '#default_value' => $config->get('contorno_exterior'),
    );


    $form['estilo'] = array(
      '#type' => 'details',
      '#title' => $this->t('Estilos'),
    );

    $form['estilo']['colorFondo'] = array(
      '#type' => 'color',
      '#title' => $this->t('Color de fondo'),
      '#default_value' => $config->get('colorFondo'), 
   );

    $form['estilo']['escala'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Factor de escala'),
      '#default_value' => $config->get('escala'), 
   );

    $form['estilo']['colorInicial'] = array(
      '#type' => 'color',
      '#title' => $this->t('Color inicial'),
      '#default_value' => $config->get('colorInicial'), 
    );

    $form['estilo']['colorFinal'] = array(
      '#type' => 'color',
      '#title' => $this->t('Color final'),
      '#default_value' => $config->get('colorFinal'), 
    );

    for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

      $form['entidad'.$i] = array(
        '#type' => 'details',
        '#title' => $this->t('Entidad territorial n°'.$i),
      );

      $form['entidad'.$i]['entidad_territorial_nombre'.$i] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Nombre'),
        '#default_value' => $config->get('entidad_territorial_nombre'.$i),
      );

      $form['entidad'.$i]['entidad_territorial_contorno'.$i] = array(
        '#type' => 'textarea',
        '#title' => $this->t('Contorno'),
        '#default_value' => $config->get('entidad_territorial_contorno'.$i),
      );

      $form['entidad'.$i]['posicion_texto_x'.$i] = array(
        '#type' => 'number',
        '#title' => $this->t('X'),
        '#default_value' => $config->get('posicion_texto_x'.$i),
      );

      $form['entidad'.$i]['posicion_texto_y'.$i] = array(
        '#type' => 'number',
        '#title' => $this->t('Y'),
        '#default_value' => $config->get('posicion_texto_y'.$i),
      );

      $form['entidad'.$i]['link'.$i] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Link'),
        '#field_prefix' => $base_url,
        '#default_value' => $config->get('link'.$i),
      );

      $form['entidad'.$i]['taxonomia'.$i] = array(
        '#type' => 'number',
        '#title' => $this->t('Taxonomia'),
        '#default_value' => $config->get('taxonomia'.$i),
      );

    }



    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('mapas_estadisticos.settings');
    $values = $form_state->getValues();

    $config->set('n_entidades_territoriales', $values['n_entidades_territoriales'])
           ->save();

    $config->set('contorno_exterior', $values['contorno_exterior'])
           ->save();

// Estilos
    $config->set('escala', $values['escala'])
           ->save();
    $config->set('colorFondo', $values['colorFondo'])
           ->save();

    $config->set('colorInicial', $values['colorInicial'])
           ->save();
    $config->set('colorFinal', $values['colorFinal'])
           ->save();

// Definición de las entidades territoriales
    for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {
      $config->set('entidad_territorial_contorno'.$i, $values['entidad_territorial_contorno'.$i])
             ->save();
      $config->set('entidad_territorial_nombre'.$i, $values['entidad_territorial_nombre'.$i])
             ->save();
      $config->set('posicion_texto_x'.$i, $values['posicion_texto_x'.$i])
             ->save();
      $config->set('posicion_texto_y'.$i, $values['posicion_texto_y'.$i])
             ->save();
      $config->set('link'.$i, $values['link'.$i])
             ->save();
      $config->set('taxonomia'.$i, $values['taxonomia'.$i])
             ->save();
    }

    parent::submitForm($form, $form_state);
  }
}
