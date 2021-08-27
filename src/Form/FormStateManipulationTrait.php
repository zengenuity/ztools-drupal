<?php

namespace Zengenuity\Tools\Drupal\Form;

use Drupal\Core\Form\FormStateInterface;

trait FormStateManipulationTrait {

  /**
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param string $key
   * @param null $value
   * @param bool $and_user_input
   */
  public function clearFormValue(FormStateInterface $form_state, string $key, $value = NULL, bool $and_user_input = TRUE) : void {
    $form_state->setValue($key, $value);
    if ($and_user_input) {
      $user_input = $form_state->getUserInput();
      $user_input[$key] = $value;
      $form_state->setUserInput($user_input);
    }
  }

}
