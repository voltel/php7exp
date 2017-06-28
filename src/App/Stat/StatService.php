<?php
namespace App\Stat;
#src/App/Stat/StatService.php

use App\Model\User;
use App\Model\Visit;
use App\Session\UserSessionInterface;

use Doctrine\ORM\EntityManagerInterface;


class StatService
{
  private $log;
  private $em;
  private $userSession;

  public function __construct(
    \Monolog\Logger $log,
    EntityManagerInterface $em,
    UserSessionInterface $userSession
    )
  {
    $this->log = $log;
    $this->em = $em;
    $this->userSession = $userSession;

  }// end of __construct


  /**
   *
   */
   public function saveStat()
   {
     $userId = $this->userSession->getUser()->getId();
     if (empty($userId)) {
        //throw new \Exception("Не удалось получить id текущего пользователя из пользовательской сессии.");
        return;
     }

     $user = $this->em->find('App\Model\User', $userId);
     if ($user) {
       $this->saveUserVisit($user);
     }//endif
   }//end of function


   /**
    *
    */
   private function saveUserVisit(User $user)
   {
     $today = new \DateTime('now');
     $dbVisit = $this->em->getRepository('App\Model\Visit')->findOneBy(['user' => $user, 'date' => $today]);

     if (!$dbVisit) {
       $visit = new Visit();
       $visit->setDate($today);
       $visit->setUser($user);

       $this->em->persist($visit);
       $this->em->flush();
     } //endif

   }//end of function




}//end of class
