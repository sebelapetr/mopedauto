<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:27
 */

declare(strict_types=1);

namespace App\Model\Latte;

use Latte\Compiler;
use Latte\Macros\MacroSet;

class LatteMacros extends MacroSet {
	
    public static function install(Compiler $compiler): void
    {
		$set = new MacroSet($compiler);
    }
}
