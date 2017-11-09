<?php

namespace Drupal\account_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SignUpForm.
 */
class SignUpForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'account_signup_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        // First Name
        $form['first_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('First Name'),
          '#default_value' => '',
          '#maxlenth' => 30,
          '#required' => true
        ];

        // Last Name
        $form['last_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Last Name'),
          '#default_value' => '',
          '#maxlenth' => 30,
          '#required' => true
        ];

        // Email
        $form['email_group']['label'] = [
          '#type' => 'label',
          '#title' => $this->t('Email Address'),
          '#required' => true
        ];

        $form['email_group']['notice'] = [
          // A custom CSS class for the notice message instead of using style
          '#markup' => $this->t('<span style="font-size: smaller">This will be your username</span>')
        ];

        $form['email_group']['email'] = [
          '#type' => 'email',
          '#default_value' => '',
          '#maxlenth' => 100,
          '#required' => true
        ];
        // We could use a Module like Password Policy here
        // Password
        $form['password'] = [
          '#type' => 'password',
          '#title' => $this->t('Password'),
          '#default_value' => '',
          '#maxlenth' => 30,
          '#required' => true
        ];
        // Confirm Password
        $form['confirm_password'] = [
          '#type' => 'password',
          '#title' => $this->t('Confirm Password'),
          '#default_value' => '',
          '#maxlenth' => 30,
          '#required' => true
        ];
        // Phone Number
        $form['phone'] = [
          '#type' => 'tel',
            // A Custom CSS Class should be applied to the optional
          '#title' => $this->t('Phone Number <span style="">(optional)</span>'),
          '#default_value' => '',
          '#maxlenth' => 30,
          '#required' => false
        ];

        $form['security_group'] = [
          '#markup' => '<h2>Security Question</h2>'
        ];
        // Security Question
        $form['security_group']['question'] = [
          '#type' => 'select',
          '#title' => $this->t('Security Question'),
          '#options' => $this->securityQuestionOptions(),
          '#required' => true
        ];

        // Security Answer
        // Treat is as a password input type to mask it
        $form['security_group']['answer'] = [
          '#type' => 'password',
          '#title' => $this->t('Security Answer'),
          '#required' => true
        ];
        // Communication Preferences
        $form['communitcation_preference_group'] = [
          '#markup' => '<h2>Communication Preference</h2>',
        ];
        $form['communication_preference_group']['opt_sweepstakes'] = [
          '#type' => 'checkbox',
            '#default_value' => true,
            '#title' => $this->t('Yes, I would like to be enrolled in the Sweepstakes. <a href="@sl" target="_blank">See Sweepstakes rules</a>',[
              // Note the sweepstakes endpoint rule should be discussed
                // A contract should be establish on if:
                // we are using a Content Type or Controller or other.
                // For the time being and for the scope of the story
                // it is a static URL
              '@sl' => '/rules/sweepstakes'
            ])

        ];
        $form['communication_preference_group']['opt_promotion'] = [
          '#type' => 'checkbox',
          '#default_value' => true,
          '#title' => $this->t('Yes, I would like to receive emails about special promotions or sales.')
        ];

        $form['communication_preference_group']['disclaimer'] = [
          '#markup' => $this->t('<span style="font-size: smaller">Note: You can not opt out of transactional emails</span>')
        ];

        $form['footer_group'] = [
          '#prefix' => '<div>',
            '#suffix' => '</div>'
        ];
        $form['footer_group']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Submit')

        ];
        $form['footer_group']['message'] = [
          '#markup' => $this->t(
            '<p>By clicking this button, I agree to the <a href="@tou" target="_blank">Terms of Use</a> and <a href="@pp" target="_blank">Privacy Policy</a></p>', [
              '@tou' => '/terms-of-use',
              '@pp' => '/privacy-policy'
          ])
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        // Validate Form

    }

    /**
     * NOTE: This can be moved to a taxonomy in a future task, making it more versatile
     * @return array
     */
    private function securityQuestionOptions() {
        return [
          $this->t('What street did you live on in 6ᵗʰ grade?'),
          $this->t('What was your childhood nickname?'),
          $this->t('In what city did your mother and father meet?'),
          $this->t('What was the last name of your third grade teacher?'),
          $this->t('What was the name of your first stuffed animal?')
        ];
    }

    protected function validateFirstName(array &$form, FormStateInterface $form_state) {

    }
}
