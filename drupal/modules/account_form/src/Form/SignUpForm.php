<?php

namespace Drupal\account_form\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

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
          '#maxlength' => 30,
          '#required' => TRUE,
        ];

        // Last Name
        $form['last_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Last Name'),
          '#default_value' => '',
          '#maxlength' => 30,
          '#required' => TRUE,
        ];

        // Email
        $form['email_group']['label'] = [
          '#type' => 'label',
          '#title' => $this->t('Email Address'),
          '#required' => TRUE,
        ];

        $form['email_group']['notice'] = [
            // A custom CSS class for the notice message instead of using style
          '#markup' => $this->t('<span style="font-size: smaller">This will be your username</span>'),
        ];

        $form['email_group']['email'] = [
          '#type' => 'email',
          '#default_value' => '',
          '#maxlength' => 100,
          '#required' => TRUE,
          '#pattern' => '[_\-a-zA-Z0-9\.\+]+@[a-zA-Z0-9](\.?[\-a-zA-Z0-9]*[a-zA-Z0-9])*',
        ];

        // Password
        $form['password'] = [
          '#type' => 'password',
          '#title' => $this->t('Password'),
          '#default_value' => '',
          '#minlength' => 7,
          '#required' => TRUE,
          '#attributes' => [
            'id' => 'password',
          ]
        ];

        // Confirm Password
        $form['password_confirm'] = [
          '#type' => 'password',
          '#title' => $this->t('Confirm Password'),
          '#default_value' => '',
          '#minlength' => 7,
          '#required' => TRUE,
          '#attributes' => [
            'id' => 'password_confirm',
          ]
        ];

        // Phone Number
        $form['phone'] = [
          '#type' => 'tel',
            // A Custom CSS Class should be applied to the optional
          '#title' => $this->t('Phone Number <span style="font-size: small;font-weight: normal;">(optional)</span>'),
          '#default_value' => '',
          '#required' => FALSE,
            '#maxlength' => 12,
          '#pattern' => '(((\+[1-9]\d{0,2})[\-\s.](\d{1,3})|(((1\s)?)\(\d{3}\)))[\-\s.])?(\d{1,8})([\-\s.](\d{1,8})){1,4}(\sext\.\s\d{1,10})?',
        ];

        $form['security_group'] = [
          '#markup' => '<h2>Security Question</h2>',
        ];

        // Security Question
        $form['security_group']['question'] = [
          '#type' => 'select',
          '#title' => $this->t('Security Question'),
          '#options' => $this->securityQuestionOptions(),
          '#required' => TRUE
        ];

        // Security Answer
        // Treat is as a password input type to mask it
        $form['security_group']['answer'] = [
          '#type' => 'password',
          '#title' => $this->t('Security Answer'),
          '#required' => TRUE
        ];
        // Communication Preferences
        $form['communitcation_preference_group'] = [
          '#markup' => '<h2>Communication Preference</h2>',
        ];
        $form['communication_preference_group']['opt_sweepstakes'] = [
          '#type' => 'checkbox',
          '#default_value' => TRUE,
          '#title' => $this->t('Yes, I would like to be enrolled in the Sweepstakes. <a href="@sl" target="_blank">See Sweepstakes rules</a>',
            [
                // Note the sweepstakes endpoint rule should be discussed
                // A contract should be establish on if:
                // we are using a Content Type or Controller or other.
                // For the time being and for the scope of the story
                // it is a static URL
              '@sl' => '/rules/sweepstakes',
            ]),
        ];
        $form['communication_preference_group']['opt_promotion'] = [
          '#type' => 'checkbox',
          '#default_value' => TRUE,
          '#title' => $this->t('Yes, I would like to receive emails about special promotions or sales.'),
        ];

        $form['communication_preference_group']['disclaimer'] = [
          '#markup' => $this->t('<span style="font-size: smaller">Note: You can not opt out of transactional emails</span>'),
        ];

        $form['footer_group'] = [
          '#prefix' => '<div>',
          '#suffix' => '</div>',
        ];
        $form['footer_group']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Submit'),

        ];
        $form['footer_group']['message'] = [
          '#markup' => $this->t(
            '<p>By clicking this button, I agree to the <a href="@tou" target="_blank">Terms of Use</a> and <a href="@pp" target="_blank">Privacy Policy</a></p>',
            [
              '@tou' => '/terms-of-use',
              '@pp' => '/privacy-policy',
            ]),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        try {
            $user = $this->createUser($form_state);
            if ($form_state->getValue('opt_sweepstakes')) {
                $this->addToSweepstakes($user);
            }
            if ($form_state->getValue('opt_promotion')) {
                $this->addUserToPromotionalSalesEmailGroup($user);
            }
        } catch (\Exception $e) {
            \Drupal::logger('form')->error($e);
            $form_state->setError($form, 'We’re sorry but we were unable to create your account at this time. Please try again.');
        }
        if (empty($form_state->getErrors())){
            $form_state->setRedirect('promotional.thank_you'); // This route should be changed once route contract has been established
        }
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        // Validate Form
        $first_name = $form_state->getValue('first_name');
        $last_name = $form_state->getValue('last_name');
        $email = $form_state->getValue('email');
        $password = $form_state->getValue('password');
        $password_confirm = $form_state->getValue('password_confirm');
        $phone = $form_state->getValue('phone');
        $question = $form_state->getValue('question');
        $answer = $form_state->getValue('answer');
        $error = false;

        // Have to remove style once error class gets implemented

        // Validate First Name
        if (empty($first_name)) {
            $error = true;
            $form['first_name']['#suffix'] = $this->errorMessage('First Name is required');
        } elseif (strlen($first_name) > 30) {
            $error = true;
            $form['first_name']['#suffix'] =  $this->errorMessage('First Name is too long, it should be no more than 100 characters.');
        }

        // Validate Last Name
        if (empty($last_name)) {
            $error = true;
            $form['last_name']['#suffix'] =  $this->errorMessage('Last Name is required');
        } elseif (strlen($last_name) > 30) {
            $error = true;
            $form['last_name']['#suffix'] =  $this->errorMessage('Last Name is too long, it should be no more than 30 characters.');
        }

        // Validate Email
        if (empty($email)) {
            $error = true;
            $form['email_group']['email']['#suffix'] =  $this->errorMessage('Email is required');
        } elseif (strlen($email) > 100) {
            $error = true;
            $form['email_group']['email']['#suffix'] =  $this->errorMessage('Email is too long, it should be no more than 100 characters.');
        } elseif (!$this->isValidEmail($email)) {
            $error = true;
            $form['email_group']['email']['#suffix'] =  $this->errorMessage('Please enter a valid email address');
        } elseif (user_load_by_name($email)) {
            $error = true;
            // The password recover link can be change
            $form['email_group']['email']['#suffix'] =  $this->errorMessage('This email address is already in use. If you’ve forgotten your password click <a href="/user/password" target="_blank">here</a> to retrieve it.');
        }

        // Validate Phone, note Phone is optional
        // NOTE: NO AC message on if phone is not valid
        // TODO: Discuss about phone pattern and having a max length of 12
        if (!empty($phone) && strlen($phone) > 12) {
            $error = true;
            $form['password']['#suffix'] =  $this->errorMessage('Phone Number is to long, it shouldn\'t be longer than 12 characters');
        } elseif (!empty($phone) && $this->isValidPhone($phone)) {
            $error = true;
            $form['password']['#suffix'] =  $this->errorMessage('Please enter a valid phone number');
        }

        // Validate Password
        if (empty($password)) {
            $error = true;
            $form['password']['#suffix'] =  $this->errorMessage('Password is required');
        } elseif (!$this->isValidPassword($password)) {
            $error = true;
            $form['password']['#suffix'] = $this->errorMessage('Please enter a password that has at least 7 characters including at least 1 uppercase character, 1 lowercase character, and 1 special character or number');
        }

        // Validate Password Confirm
        if (empty($password_confirm)) {
            $error = true;
            $form['password_confirm']['#suffix'] =  $this->errorMessage('Password Confirm is required');
        } elseif ($password != $password_confirm) {
            $error = true;
            $form['password_confirm']['#suffix'] =  $this->errorMessage('Your password does not match');
        }

        // Validate Security Security Question
        if (is_null($question)) {
            $error = true;
            $form['security_group']['question']['#suffix'] =  $this->errorMessage('Please select your security question');
        }

        // Validate Security Answer
        if (is_null($answer)) {
            $error = true;
            $form['security_group']['answer']['#suffix'] =  $this->errorMessage('Please answer your security question');
        } elseif (strlen($answer) > 100 ) {
            $error = true;
            $form['security_group']['answer']['#suffix'] =  $this->errorMessage('Please use an answer not bigger than 100 characters');
        }

        if ($error) {
            $form_state->clearErrors();
            // This can be changed
            $form_state->setError($form, "Form contains error, please fix them and resubmit");
        }
    }

    /**
     * NOTE: This can be moved to a taxonomy in a future task, making it more
     * versatile
     *
     * @return array
     */
    private function securityQuestionOptions() {
        return [
          $this->t('What street did you live on in 6ᵗʰ grade?'),
          $this->t('What was your childhood nickname?'),
          $this->t('In what city did your mother and father meet?'),
          $this->t('What was the last name of your third grade teacher?'),
          $this->t('What was the name of your first stuffed animal?'),
        ];
    }

    /**
     *
     * @param $email
     *
     * @return mixed
     */
    private function isValidEmail($email) {
        return \Drupal::service('email.validator')->isValid($email, true, true);
    }

    /**
     * Returns phone is valid
     * @param $phone
     *
     * @return int
     */
    private function isValidPhone($phone) {
        $pattern = '/(((\+[1-9]\d{0,2})[\-\s.](\d{1,3})|(((1\s)?)\(\d{3}\)))[\-\s.])?(\d{1,8})([\-\s.](\d{1,8})){1,4}(\sext\.\s\d{1,10})?/';
        return preg_match($pattern, $phone);
    }

    /**
     * Returns if password complies with the constraints
     * @param string $raw_password
     *
     * @return bool
     */
    private function isValidPassword($raw_password) {
        // Good practice to trim Passwords
        $password = trim($raw_password);
        $password_no_alpha = preg_replace('/[a-zA-Z]/', '', $password);
        // Valid Characters a-z A-Z 0-9!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~
        // This Regex will find characters that are not valid
        $allow_password_regex = '/[^a-z A-Z0-9\!\"\#\$\%\&\\\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\]\^\_\{\|\}\~\$]+/';

        if (strlen($password) <= 7) { // Meets minimum length
            echo "Length";
            return false;
        } elseif (preg_match($allow_password_regex, $password)) {  // Contains only Valid Character
            echo "Valid";
            return false;
        } elseif (!preg_match('/[A-Z]/', $password)) { // Contains an Uppercase
            echo "Upper";
            return false;
        } elseif (!preg_match('/[a-z]/', $password)) { // Contains a Lowercase
            echo "Lowe";
            return false;
        } elseif (!strlen($password_no_alpha) || preg_match($allow_password_regex, $password_no_alpha)) { // Contains at least one special Character or Number
            echo "Num or char";
            return false;
        }

        return true;
    }

    /**
     * Create user
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *
     * @return \Drupal\Core\Entity\EntityInterface|static
     */
    private function createUser(FormStateInterface $form_state){
        $user = User::create();
        $user->enforceIsNew();
        $user->setUsername($form_state->getValue('email'));
        $user->setEmail($form_state->getValue('email'));
        $user->setPassword(Crypt::hashBase64($form_state->getValue('password')));
        // TODO: We need to create this fields in the User Entity
        $user->set('field_user_first_name', $form_state->getValue('first_name'));
        $user->set('field_user_last_name', $form_state->getValue('last_name'));
        $user->set('field_user_phone', $form_state->getValue('phone'));
        $user->set('field_user_question', $form_state->getValue('question'));
        $user->set('field_user_answer', Crypt::hashBase64($form_state->getValue('answer')));
        $user->activate();
        // Need to add a user role
        $user->save();
        return $user;
    }

    /**
     * Wraps message with div and error class
     * @param string $message
     *
     * @return \Drupal\Core\StringTranslation\TranslatableMarkup
     */
    private function errorMessage($message) {
        return $this->t('<div class="error" style="color: red;">'.$message.'</div>');
    }

    /**
     * Add User to Sweepstakes List
     * @param \Drupal\Core\Entity\EntityInterface $user
     */
    private function addToSweepstakes($user) {
        // Add user to sweepstakes email list
        // TODO: create an emailing list
    }

    /**
     * Add User to Promotional Sales Email Group List
     * @param \Drupal\Core\Entity\EntityInterface $user
     */
    private function addUserToPromotionalSalesEmailGroup($user) {
        // Add user to promotional/sales email list
        // TODO: create a promotional/sales email list
    }
}
