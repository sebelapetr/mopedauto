<?php
declare(strict_types=1);

namespace App\Model\Emails;

use Nette\Utils\ArrayHash;
use App\Model\Branch;
use App\Model\Order;
use App\Model\User;

class BaseEmailService extends EmailService{

	public function getEmailNamespace(): string {
		return "Other";
	}

	public function sendContactForm(ArrayHash $values, string $sendToEmail, Branch $branch): void
	{
		$this->createAndSendMessage("newContact", $sendToEmail, ['values' => $values, 'date' => new \DateTimeImmutable(), 'branch' => $branch], 'Kontaktní formulář z webu', FALSE);
	}

    public function sendCareerForm(ArrayHash $values, string $sendToEmail, Branch $branch, string $fileName = null, string $filePath = null): void
    {
        $fileUrl = 'https://www.skoricopy.cz/files/resumes/'.$fileName;
        $this->createAndSendMessage("newCareer", $sendToEmail, ['values' => $values, 'date' => new \DateTimeImmutable(), 'branch' => $branch, 'fileName' => $fileName, 'fileUrl' => $fileUrl], 'Přišel nový životopis', FALSE);
    }

    public function sendCareerFormSender(ArrayHash $values, string $sendToEmail, Branch $branch): void
    {
        $this->createAndSendMessage("newCareerSender", $sendToEmail, ['values' => $values, 'date' => new \DateTimeImmutable(), 'branch' => $branch], 'Děkujeme za zaslaný životopis', FALSE);
    }
}
