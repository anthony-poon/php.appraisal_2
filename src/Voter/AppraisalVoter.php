<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 12/9/2018
 * Time: 4:11 PM
 */

namespace App\Voter;


use App\Entity\Appraisal\AppraisalAbstract;
use App\Entity\Base\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AppraisalVoter extends Voter {
    protected function supports($attribute, $subject) {
        return (in_array($attribute, ["owner", "appraiser", "counter"]) && ($subject instanceof AppraisalAbstract));
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /* @var \App\Entity\Base\User $user */
        /* @var \App\Entity\Appraisal\AppraisalAbstract $subject */
        switch ($attribute) {
            case "owner":
                return $subject->getOwner() === $user;
            case "appraiser":
                return $user->getAppraisees()->contains($subject->getOwner());
            case "counter":
                return $user->getCountersignees()->contains($subject->getOwner());
            default:
                return false;
        }
    }


}