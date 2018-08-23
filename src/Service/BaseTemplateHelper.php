<?php
namespace App\Service;

use App\Entity\Base\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BaseTemplateHelper {
    private $sideMenu = [];
    private $title = "Web Application";
    private $jsParam = [];
    /* @var User $user */
	private $user = null;
	private $css = [];
	private $js = [];
	private $role = [];
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage) {
		$token = $tokenStorage->getToken();
		$this->role = [];
		if ($token) {
			$this->user = $token->getUser();
			if ($this->user && $this->user instanceof User) {
				$this->role = $this->user->getRoles();
			}
		}
    	$this->sideMenu = [
        	[
        		"text" => "Home",
				"icon" => "home",
				"url" => $router->generate("home"),
			],
			[
				"text" => "View Appraisal",
				"url" => $router->generate("appraisal_list")
			], [
				"text" => "Administration",
				"isVisible" => in_array("ROLE_ADMIN", $this->role),
			], [
				"text" => "User Management",
				"url" => $router->generate("admin_list_user"),
				"isVisible" => in_array("ROLE_ADMIN", $this->role),
				"indent" => true,
			], [
				"text" => "Group Management",
				"url" => $router->generate("admin_list_user_group"),
				"isVisible" => in_array("ROLE_ADMIN", $this->role),
				"indent" => true,
			]
		];
    }

    /**
     * @return array
     */
    public function getSideMenu(): array {
        return $this->sideMenu;
    }

    public function addSideMenuItem(array $item) {
        $this->sideMenu[] = $item;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return BaseTemplateHelper
     */
    public function setTitle(string $title): BaseTemplateHelper {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getJsParam(): array {
        return $this->jsParam;
    }

    /**
     * @param array $jsParam
     * @return BaseTemplateHelper
     */
    public function addJsParam(array $jsParam): BaseTemplateHelper {
        $this->jsParam = array_merge($jsParam, $this->jsParam);
        return $this;
    }

    public function addJs($js): BaseTemplateHelper {
    	$this->js[] = $js;
    	return $this;
	}

	public function addCss($css): BaseTemplateHelper {
    	$this->css[] = $css;
    	return $this;
	}

	/**
	 * @return array
	 */
	public function getCss(): array {
		return $this->css;
	}

	/**
	 * @return array
	 */
	public function getJs(): array {
		return $this->js;
	}


}