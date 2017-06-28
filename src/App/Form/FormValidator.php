<?php
namespace App\Form;

use Symfony\Component\{
  HttpFoundation\Request,
  Security\Csrf\CsrfToken,
  Security\Csrf\CsrfTokenManagerInterface,
  Validator\Validator\ValidatorInterface,
  Validator\Constraint
};

abstract class FormValidator implements FormValidatorInterface
{
  protected $csrfProvider;
  protected $validator;
  protected $errors;
  protected $assertions;

  const TOKEN_NAME = 'token';
  /**
  *
  */
  public function __construct(
    CsrfTokenManagerInterface $csrfProvider,
    ValidatorInterface $validator
    )
  {
    $this->csrfProvider = $csrfProvider;
    $this->validator = $validator;
    $this->errors = [];
    $this->assertions = [];
  }//end of function

  /**
  *
  */
  public function addError(string $error)
  {
    $this->errors[] = $error;
  }//end of function

  /**
  *
  */
  public function getErrors() : array
  {
    return $this->errors;
  }//end of function

  /**
  *
  */
  public function getToken()
  {
    return $this->csrfProvider->refreshToken(self::TOKEN_NAME);
  }//end of function

  /**
  *
  */
  public function addAssertion(string $fieldName, Constraint $assertion)
  {
    $this->assertions[$fieldName][] = $assertion;
  }//end of function

  public function isValid(Request $request) : bool
  {
    if (!$this->isTokenValid($request)) {
      $this->addError('Sorry, something went wrong. Please refresh the page.');
      return false;
    }//endif

    $isValid = true;
    $this->buildValidation();

    foreach ($this->assertions as $fieldName => $assertionGroup) {
      foreach ($assertionGroup as $assertion) {
        $error = $this->validator->validateValue($request->get($fieldName), $assertion);
        if (count($error) > 0) {
          $isValid = false;
          $this->addError($error->get(0)->getMessage());
        }//endif
      }//end foreach inner
    }//end foreach outer

    return $isValid;
  }//end of function

  /**
  *
  */
  public function isTokenValid(Request $request) : bool
  {
    $csrf_token = new CsrfToken(self::TOKEN_NAME, $request->get(self::TOKEN_NAME));
    if ($this->csrfProvider->isTokenValid($csrf_token)) {
      return true;
    }//endif

    return false;
  }//end of function


  /**
   *
   */
   abstract protected function buildValidation();

}//end of class
