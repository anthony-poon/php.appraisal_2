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
 * Class Department
 * @package App\Entity
 * @ORM\Table(name="department")
 * @ORM\Entity()
 */
class Department extends DirectoryGroup {

}