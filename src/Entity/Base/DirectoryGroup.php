<?php
namespace App\Entity\Base;

use App\FormType\Constraint\UniqueCollectionValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="directory_group")
 * @ORM\Entity
 */
class DirectoryGroup extends DirectoryObject {
	/**
	 * @var Collection
	 * @ORM\ManyToMany(targetEntity="DirectoryObject", inversedBy="parents")
	 * @ORM\JoinTable(name="directory_group_mapping")
	 * @UniqueCollectionValue(message="Duplicated members")
	 */
	private $children;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=256)
	 */
	private $name;

	public function __construct() {
		parent::__construct();
		$this->children = new ArrayCollection();
	}

	/**
	 * @return Collection
	 */
	public function getChildren(): Collection {
		return $this->children;
	}

	public function addChild(DirectoryObject $do): DirectoryGroup {
		if (empty($this->children)) {
			$this->children = new ArrayCollection($do);
		}
		$this->children->add($do);
		return $this;
	}

	public function clearChildren(DirectoryObject $do): DirectoryGroup {
		if (empty($this->children)) {
			$this->children = new ArrayCollection($do);
		}
		$this->children->clear();
		return $this;
	}

	public function setChild($index, DirectoryObject $do): DirectoryGroup {
		if (empty($this->children)) {
			$this->children = new ArrayCollection($do);
		}
		$this->children->set($index, $do);
		return $this;
	}

	public function setChildren(array $arr): DirectoryGroup {
		$this->children = new ArrayCollection();
		foreach ($arr as $item) {
			$this->addChild($item);
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return DirectoryGroup
	 */
	public function setName(string $name): DirectoryGroup {
		$this->name = $name;
		return $this;
	}

	public function getFriendlyName(): string {
		return $this->getName();
	}

	public function getFriendlyClassName(): string {
		return "Directory Group";
	}

}