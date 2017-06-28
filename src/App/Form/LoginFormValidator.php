<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class LoginFormValidator extends FormValidator
{
  protected function buildValidation()
  {
    $this->addAssertion('email', new Assert\NotBlank(['message' => 'The email should not be blank.'] ));
    $this->addAssertion('email', new Assert\Email(['message' => 'Please provide a valid email.'] ));
    $this->addAssertion('password', new Assert\NotBlank(['message' => 'The passwork should not be empty (blank).'] ));
  }//end of function

}//end of class
