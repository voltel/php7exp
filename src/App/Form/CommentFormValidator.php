<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class CommentFormValidator extends FormValidator
{
  protected function buildValidation()
  {
    $this->addAssertion('comment',
      new Assert\NotBlank(['message' => 'The comment should not be blank']));
    $this->addAssertion('comment',
      new Assert\Length(['max' => 250, 'maxMessage' => 'The comment cannot be longer than {{ limit }} characters.']));
  }//end of function

}//end of class
