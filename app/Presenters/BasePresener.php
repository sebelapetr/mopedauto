<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:26
 */

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    protected function getPureName(): string
    {
        $pos = strrpos($this->name, ':');
        if (is_int($pos)) {
            return substr($this->name, $pos + 1);
        }
        return $this->name;
    }

    public function isLinkCurrentIn($links): bool
    {
        foreach(explode('|', $links) as $item) {
            if($this->isLinkCurrent($item)){
                return TRUE;
            }
        }
        return FALSE;
    }
}