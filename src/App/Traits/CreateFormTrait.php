<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait CreateFormTrait
{
  /**
   *
   */
  public function createForm(string $c_form_template_name) : Response
  {
    $csrfToken = $this->formValidator->getToken(); // csrfProvider->refreshToken('token');
    $errors = $this->userSession->getSession()->getBag('flashes')->get('errors');

    $c_html = $this->twig->render($c_form_template_name, [
      'token' => $csrfToken,
      'errors' => $errors
    ]);

    return new Response($c_html);
  }//end of function

}//end of trait
