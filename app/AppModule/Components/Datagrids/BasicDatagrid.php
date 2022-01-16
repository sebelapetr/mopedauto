<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Utils\StringUtils;
use ReflectionClass;

abstract class BasicDatagrid extends BaseDatagrid implements ISetupDatagrid
{
	protected string $repoName;

	public function getRepoName(): string
	{
		if(!isset($this->repoName)){
			$this->repoName = $this->getRepoNameFromClassName();
		}
		return $this->repoName;
	}

	public function getRepoNameFromClassName(): string
	{
		$reflection = new ReflectionClass(static::class);
		return strtolower(StringUtils::cutEnd($reflection->getShortName(), 'Datagrid'));
	}

	public function getTranslationDomain(): string
	{
		return 'app.' . StringUtils::clean($this->getRepoName());
	}
}