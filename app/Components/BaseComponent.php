<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:48
 */

declare(strict_types=1);

namespace App\Components;

use App\Model\Orm;
use App\Presenters\BasePresenter;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;
use Nette\Utils\Strings;

/**
 * @property-read ITranslator $translator
 * @property-read BasePresenter $presenter
 */
abstract class BaseComponent extends Control
{

	protected Orm $orm;
	private ITranslator $translator;

	public function __construct(Orm $orm, ITranslator $translator)
	{
		$this->translator = $translator;
		$this->orm = $orm;
	}

	public function getTranslator(): ITranslator
	{
		return $this->translator;
	}

	protected function setTemplateFile(?string $file = NULL): void
	{
		$reflection = $this->getReflection();
		$dir = pathinfo($reflection->getFileName(), PATHINFO_DIRNAME);
		
		$ext = '.latte';
		if ($file === NULL) {
			$file = $reflection->getShortName() . $ext;
		} elseif (!Strings::endsWith($file, $ext)) {
			$file .= $ext;
		}
		
		if(is_file($dir . DIRECTORY_SEPARATOR . $file)){
			$this->template->setFile($dir . DIRECTORY_SEPARATOR . $file);
		}
	}


}