<?php
declare(strict_types=1);

namespace App\Model\Upload;

use Nette\SmartObject;

class ImageSize{

	use SmartObject;

	private ?int $width;
	private ?int $height;

	public function __construct(?int $width = NULL, ?int $height = NULL)
	{
		$this->width = $width;
		$this->height = $height;
	}

	public function getWidth(): ?int
	{
		return $this->width;
	}

	public function getHeight(): ?int
	{
		return $this->height;
	}


}