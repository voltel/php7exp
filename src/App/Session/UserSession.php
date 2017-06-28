<?php
namespace App\Session;

use App\Model\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserSession implements UserSessionInterface
{
  private $session;

  const SESSION_TOKEN = 'myapp';

  /*
  *
  */
  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }//end of function

  /*
  *
  */
  public function setUser(User $user)
  {
    $this->session->set(self::SESSION_TOKEN, serialize($user));

  }//end of function

  /**
  *
  */
  public function hasRole(string $role) : bool
  {
    $user = $this->getUser();
    if ($user) {
      return ($user->getRole() == $role);
    }//endif

    return false;
  }//end of function

  /*
  *
  */
  public function getUser() : User
  {
    $serializedSession = $this->session->get(self::SESSION_TOKEN);
    if ($serializedSession) {
      $user = unserialize($serializedSession);
      return $user;
    }//endif

    return new User();
  }//end of function


  /*
  *
  */
  public function getSession() : SessionInterface
  {
    return $this->session;
  }//end of function


}//end of class
