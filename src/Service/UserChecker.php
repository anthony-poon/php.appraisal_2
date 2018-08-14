<?php

namespace App\Service;

use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
	public function checkPreAuth(UserInterface $user) {
		/* @var \App\Entity\Base\User $user */
		if ($user->getIsActive()) {
			return true;
		} else {
			throw new AccountExpiredException("Account is disabled.");
		}
	}

	public function checkPostAuth(UserInterface $user) {
		/* @var \App\Entity\Base\User $user */
		if ($user->getIsActive()) {
			return true;
		} else {
			throw new AccountExpiredException("Account is disabled");
		}
	}


}