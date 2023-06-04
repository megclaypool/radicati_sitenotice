<?php

namespace Drupal\radsitenotice\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Creates a site notice block that can be dismissed by the user.
 *
 * @Block(
 *   id="site_notice_block",
 *   admin_label=@Translation("Site Notice"),
 * )
 */
class SiteNoticeBlock extends BlockBase {
    public function defaultConfiguration() {
      return [
        'notice_image' => null,
        'notice_text' => '',
        'notice_title' => '',
        'notice_content' => $this->t(''),
        'notice_link' => '',
        'notice_background' => null,
        //'cookie_expiration' => null,
        'cookie_expiration_days' => 14
      ];
    }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $out = new TranslatableMarkup($config['notice_content']);

    return [
      '#theme' => 'radsitenotice',
      '#notice_image' => $config['notice_image'],
      '#notice_text' => $config['notice_text'],
      '#notice_title' => $config['notice_title'],
      '#content' => $out,
      // '#button_label' => $config['notice_button_label'],
      '#button_url' => $config['notice_link'],
      '#notice_id' => $config['block_id'],
      '#notice_background' => $config['notice_background'],
      '#cookie_expiration' => $config['cookie_expiration'],
      '#cookie_expiration_days' => $config['cookie_expiration_days'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    //$form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

//    $form['notice_data'] = [
//      '#type' => 'details',
//      '#title' => $this->t('Notice Data'),
//      '#open' => TRUE,
//    ];

    $form['notice_image'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['alert_icon'],
      '#title' => $this->t('Image'),
      '#default_value' => $config['notice_image'] ?? '',
      '#description' => $this->t('A small image which will display next to the alert text'),
    ];

    $form['notice_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alert Text'),
      '#default_value' => $config['notice_text'] ?? '',
    ];

    $form['notice_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notice Title'),
      '#default_value' => $config['notice_title'] ?? '',
    ];

    $form['notice_content'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Notice Content'),
      '#default_value' => $config['notice_content'] ?? '',
      '#format' => $config['notice_format'] ?? 'basic_html',
      '#description' => $this->t('Short description of this notice.')
    ];

    $form['notice_button_label'] = [
      '#type'=> 'textfield',
      '#title' => $this->t('Notice Button Text'),
      '#default_value' => $config['notice_button_label'] ?? '',
      '#description' => $this->t('Short text telling the user to click. Screen readers will add "about {Notice Title}" after this text, so it should be something like Learn More, or Read More')
    ];

    $form['notice_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notice Link'),
      '#default_value' => $config['notice_link'] ?? '',
      '#description' => $this->t('Link the user can go to in order to learn more about this.')
    ];

    $background_options['red'] = 'Red';
    $background_options['yellow'] = 'Yellow';

    $form['notice_background'] = [
      '#type' => 'select',
      '#title' => $this->t('Notice Background Color'),
      '#default_value' => $config['notice_background'] ?? 'red',
      '#options' => $background_options,
      '#description' => $this->t('Choose the background color of the notice')
    ];

    $form['cookie_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Cookie Settings'),
      '#open' => FALSE
    ];

    $form['cookie_settings']['cookie_expiration'] = [
      '#type' => 'select',
      '#title' => $this->t('Cookie Expiration'),
      '#description' => $this->t('When the cookie expires, the message will be shown again. Expiring with the session means it will reset when the browser is closed.'),
      '#options' => [
        'session' => $this->t('Expires with session'),
        'expires-days' => $this->t('Expires after a set number of days'),
        'no-cookie' => $this->t('No cookie, alert will not stay closed')
      ],
      '#default_value' => $config['cookie_expiration'],
    ];

    $form['cookie_settings']['cookie_expiration_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Days until expiration'),
      '#default_value' => $config['cookie_expiration_days'] ?? 14,
      '#attributes' => [
        'id' => 'expiration-days',
      ],
      '#states' => [
        'visible' => [
          ':input[name="settings[cookie_settings][cookie_expiration]"]' => ['value' => 'expires-days'],
        ]
      ]
    ];



    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['notice_text'] = $form_state->getValue('notice_text');
    $this->configuration['notice_image'] = $form_state->getValue('notice_image');
    $this->configuration['notice_title'] = $form_state->getValue('notice_title');
    $this->configuration['notice_content'] = $form_state->getValue('notice_content')['value'];
    $this->configuration['notice_format'] = $form_state->getValue('notice_content')['format'];
    //$this->configuration['notice_button_label'] = $form_state->getValue('notice_button_label');
    $this->configuration['notice_link'] = $form_state->getValue('notice_link');
    $this->configuration['notice_background'] = $form_state->getValue('notice_background');
    $this->configuration['block_id'] = $form['id']['#default_value'];

    $cookie = $form_state->getValue('cookie_settings');

    $this->configuration['cookie_expiration'] = $cookie['cookie_expiration'];
    $this->configuration['cookie_expiration_days'] = $cookie['cookie_expiration_days'];
  }
}
