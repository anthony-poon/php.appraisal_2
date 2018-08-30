<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 9/8/2018
 * Time: 6:24 PM
 */

namespace App\Entity;

use App\Entity\Base\DirectoryGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Office
 * @package App\Entity
 * @ORM\Table(name="user_office")
 * @ORM\Entity()
 */
class Office extends DirectoryGroup {
	public function getFriendlyClassName(): string {
		return "Office";
	}
}