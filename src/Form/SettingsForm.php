<?php

namespace Drupal\swiftype_node_hooks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * {@inheritdoc}
 */
class SettingsForm extends ConfigFormBase {
  const SETTINGS = 'swiftype_node_hooks.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swiftype_node_hooks_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['swiftype_node_hooks__active'] = array(
      '#type' => 'checkbox',
      '#title' => t('Production Install / Active'),
      '#description'   => $this->t("Only make active in production!"),
      '#default_value' => $config->get('swiftype_node_hooks__active')
    );

    $form['swiftype_node_hooks__engine_id'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Engine ID'),
      '#default_value' => $config->get('swiftype_node_hooks__engine_id')
    ];

    $form['swiftype_node_hooks__domain_id'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Domain ID'),
      '#default_value' => $config->get('swiftype_node_hooks__domain_id')
    ];

    $form['swiftype_node_hooks__api_key'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('API Key'),
      '#default_value' => $config->get('swiftype_node_hooks__api_key')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('swiftype_node_hooks__active', $form_state->getValue('swiftype_node_hooks__active'))
      ->set('swiftype_node_hooks__engine_id', $form_state->getValue('swiftype_node_hooks__engine_id'))
      ->set('swiftype_node_hooks__domain_id', $form_state->getValue('swiftype_node_hooks__domain_id'))
      ->set('swiftype_node_hooks__api_key', $form_state->getValue('swiftype_node_hooks__api_key'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
