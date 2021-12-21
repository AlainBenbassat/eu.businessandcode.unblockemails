<?php

use CRM_Unblockemails_ExtensionUtil as E;

class CRM_Unblockemails_Form_UnblockEmail extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->setTitle('Remove Onhold Flag');

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (!empty($values['onhold_date'])) {
      $helper = new CRM_Unblockemails_Helper();
      $helper->unblockEmailsOnDate($values['onhold_date']);

      CRM_Core_Session::setStatus('Unblocked emails on ' . $values['onhold_date'], 'OK', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/unblock-emails', ['reset' => 1]));
    }

    parent::postProcess();
  }

  private function addFormElements() {
    $numOnHold = $this->getNumOnHold();
    $helper = new CRM_Unblockemails_Helper();
    $this->addRadio('onhold_date', 'Emails on hold on dates:', $helper->getDaysWithEmailsOnHold($numOnHold), NULL,'<br>',FALSE);
  }

  private function getNumOnHold() {
    $numOnHold = CRM_Utils_Request::retrieve('numonhold', 'Positive', $this, FALSE, 1);
    if ($numOnHold) {
      return $numOnHold;
    }
    else {
      return 1;
    }
  }

  private function addFormButtons() {
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Unblock emails on selected date'),
        'isDefault' => TRUE,
      ),
    ));
  }

  private function getRenderableElementNames() {
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
