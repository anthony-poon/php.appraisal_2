<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 5/9/2018
 * Time: 5:22 PM
 */

namespace App\Controller;


use App\Entity\Base\User;

class ControllerContext {
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var array
	 */
	private $param;

	/**
	 * @var array
	 */
	private $data;
	/**
	 * @return User
	 */
	public function getUser(): User {
		return $this->user;
	}

	/**
	 * @param User $user
	 * @return ControllerContext
	 */
	public function setUser(User $user): ControllerContext {
		$this->user = $user;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getParam(): array {
		return $this->param;
	}

	/**
	 * @param array $param
	 * @return ControllerContext
	 */
	public function setParam(array $param): ControllerContext {
		$this->param = $param;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData(): array {
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return ControllerContext
	 */
	public function setData(array $data): ControllerContext {
		$this->data = $data;
		return $this;
	}
}