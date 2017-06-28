<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserPostFormValidator extends FormValidator
{
  /**
  *
  */
  protected function buildValidation()
  {
    // title
    $this->addAssertion('title',
      new Assert\NotBlank(['message' => 'The title should not be blank']));
    $this->addAssertion('title',
      new Assert\Length(['max'=> 150, 'maxMessage' => 'The title cannot be longer then {{ limit }} characters']));

    // description
    $this->addAssertion('description',
      new Assert\Length(['max'=> 250, 'maxMessage' => 'The description cannot be longer then {{ limit }} characters.']));

  }//end of function
}//end of class
