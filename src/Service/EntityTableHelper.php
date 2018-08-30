<?php

namespace App\Service;

class EntityTableHelper {
	private $header = [];
	private $table = [];
	private $addPath = "";
	private $editPath = "";
	private $delPath = "";
	private $router;
	private $renderer;
	private $title;

	public function __construct(\Symfony\Component\Routing\RouterInterface $router) {
		$this->router = $router;
	}

	/**
	 * @param string $addPath
	 * @return EntityTableHelper
	 */
	public function setAddPath(string $addPath): EntityTableHelper {
		$this->addPath = $this->router->getRouteCollection()->get($addPath)->getPath();
		return $this;
	}

	/**
	 * @param string $editPath
	 * @return EntityTableHelper
	 */
	public function setEditPath(string $editPath): EntityTableHelper {
		$this->editPath = $this->router->getRouteCollection()->get($editPath)->getPath();
		return $this;
	}

	/**
	 * @param string $delPath
	 * @return EntityTableHelper
	 */
	public function setDelPath(string $delPath): EntityTableHelper {
		$this->delPath = $this->router->getRouteCollection()->get($delPath)->getPath();
		return $this;
	}

	public function addRow(int $index,array $row) {
		$this->table[$index] = $row;
	}

	/**
	 * @param mixed $title
	 * @return EntityTableHelper
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @param array $header
	 * @return EntityTableHelper
	 */
	public function setHeader(array $header): EntityTableHelper {
		$this->header = $header;
		return $this;
	}

	public function compile(): array {
		return [
			"title" => $this->title,
			"table" => $this->table,
			"header" => $this->header,
			"addPath" => $this->addPath,
			"delPath" => $this->delPath,
			"editPath" => $this->editPath
		];
	}
}