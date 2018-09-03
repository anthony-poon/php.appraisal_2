<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 3/9/2018
 * Time: 4:11 PM
 */

namespace App\FormType\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SerialIndexTransformer implements DataTransformerInterface {
	public function transform($arr) {
		return $arr;
	}

	public function reverseTransform($arr) {
		$rtn = [];
		foreach ($arr as $item) {
			$rtn[] = $item;
		}
		return $rtn;
	}


}