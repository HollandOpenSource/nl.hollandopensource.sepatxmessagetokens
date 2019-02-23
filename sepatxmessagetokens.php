<?php

require_once 'sepatxmessagetokens.civix.php';

/**
 * This hook lets you customize the collection message.
 * 
 * You can simply put a string here, but most likely you would want to base
 * the message on the type of payment and/or the creditor.
 */ 
function sepatxmessagetokens_civicrm_modify_txmessage(&$transaction_message, $mandate, $creditor) {
  $p = new \Civi\Token\TokenProcessor(\Civi\Core\Container::singleton()->get('dispatcher'), array(
    'controller' => __CLASS__,
    'smarty' => TRUE,
  ));

  // Fill the processor with a batch of data.
  $p->addMessage('transaction_message', $transaction_message, 'text/plain');
  $p->addRow()->context('contactId',    $mandate["contact_id"])
              ->context('entity_table', $mandate["entity_table"])
              ->context('entity_id',    $mandate["entity_id"]);

  // Lookup/compose any tokens which are referenced in the message.
  $p->evaluate();

  // Display mail-merge data.
  foreach ($p->getRows() as $row) {
    $transaction_message = $row->render('transaction_message');
  }
}

function sepatxmessagetokens_civicrm_container(&$container) {
  $container->addResource(new \Symfony\Component\Config\Resource\FileResource(__FILE__));
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    array(\Civi\Token\Events::TOKEN_REGISTER, 'sepatxmessagetokens_register_tokens')
  );
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    array(\Civi\Token\Events::TOKEN_EVALUATE, 'sepatxmessagetokens_evaluate_tokens')
  );
}

function sepatxmessagetokens_register_tokens(\Civi\Token\Event\TokenRegisterEvent $e) {
  $e->entity('sepa')
    ->register('frequencyInterval', ts('Frequency Interval'))
    ->register('financialTypeId',   ts('Financial Type Id'));
}

function sepatxmessagetokens_evaluate_tokens(\Civi\Token\Event\TokenValueEvent $e) {
  foreach ($e->getRows() as $row) {
    $frequency_interval = 'n/a';
    $financial_type_id  = 'n/a';

    if ($row->context['entity_table'] == "civicrm_contribution_recur") {
      $result = civicrm_api3('ContributionRecur', 'get', array(
        'return' => array("frequency_interval", "financial_type_id"),
        'id' => $row->context['entity_id'],
      ));
    } else {
      $result = civicrm_api3('Contribution', 'get', array(
        'return' => array("financial_type_id"),
        'id' => $row->context['entity_id'],
      ));
    }
    if (array_key_exists('id', $result)) {
      $result = $result['values'][$result['id']];

      /** @var TokenRow $row */
      $row->format('text/html');
      if (array_key_exists('frequency_interval', $result)) {
        $frequency_interval = $result['frequency_interval'];
      }
      if (array_key_exists('financial_type_id', $result)) {
        $financial_type_id  = $result['financial_type_id'];
      }
    }
    $row->tokens('sepa', 'frequencyInterval', $frequency_interval);
    $row->tokens('sepa', 'financialTypeId',   $financial_type_id);
  }
}

/**
 * Implementation of hook_civicrm_config
 */
function sepatxmessagetokens_civicrm_config(&$config) {
  _sepatxmessagetokens_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function sepatxmessagetokens_civicrm_xmlMenu(&$files) {
  _sepatxmessagetokens_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function sepatxmessagetokens_civicrm_install() {
  return _sepatxmessagetokens_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function sepatxmessagetokens_civicrm_uninstall() {
  return _sepatxmessagetokens_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function sepatxmessagetokens_civicrm_enable() {
  return _sepatxmessagetokens_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function sepatxmessagetokens_civicrm_disable() {
  return _sepatxmessagetokens_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function sepatxmessagetokens_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _sepatxmessagetokens_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function sepatxmessagetokens_civicrm_managed(&$entities) {
  return _sepatxmessagetokens_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function sepatxmessagetokens_civicrm_caseTypes(&$caseTypes) {
  _sepatxmessagetokens_civix_civicrm_caseTypes($caseTypes);
}
