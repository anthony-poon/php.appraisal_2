<?php
namespace App\Service;

use App\Entity\SiteUser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BaseTemplateHelper {
    private $sideMenu = [];
    private $title = "Web Application";
    private $jsParam = [];
	private $user = null;
	private $css = [];
	private $js = [];
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage) {
		$token = $tokenStorage->getToken();
		if ($token) {
			$this->user = $token->getUser();
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