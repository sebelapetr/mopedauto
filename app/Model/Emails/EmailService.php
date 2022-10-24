<?php
declare(strict_types=1);

namespace App\Model\Emails;

use Contributte\Translation\Translator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\SmartObject;
use App\Presenters\BasePresenter;
use Tracy\Debugger;

abstract class EmailService
{
	use SmartObject;

	const EMAILS_DIR = APP_DIR . "/presenters/templates/Emails";

	const FROM = 'Mopedauto <info@mopedauto.cz>';

	private IMailer $mailer;
	protected Translator $translator;
	private ITemplateFactory $templateFactory;
	protected LinkGenerator $linkGenerator;

	public function __construct(
			IMailer $mailer,
			Translator $translator,
			ILatteFactory $latteFactory,
			IRequest $httpRequest,
			LinkGenerator $linkGenerator,
			IStorage $cacheStorage
		)
	{
		$this->mailer = $mailer;
		$this->translator = $translator;
		$this->linkGenerator = $linkGenerator;

		$this->templateFactory = new TemplateFactory($latteFactory, $httpRequest, NULL, $cacheStorage);
	}

	abstract protected function getEmailNamespace(): string;

	protected function getTemplatePath(): string
	{
		return self::EMAILS_DIR ."/".$this->getEmailNamespace();
	}
	
	protected function getTranslatorForEmail($emailName): Translator
	{
		return $this->translator->domain('email.'.$this->getEmailNamespace().".".$emailName);
	}
	
	/**
	 * @param string $path
	 * @param array $params
	 * @return Template
	 */
	protected function createTemplate(string $path, array $params = []): Template
	{
		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$template->setFile($path);
		$template->setParameters($params);
		$template->emailService = $this;
		$template->layout = self::EMAILS_DIR . "/@layout.latte";
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);
		
		return $template;
	}


    /**
     * @param string $emailName
     * @param string $sendTo
     * @param array $templateParams
     * @param string $subject
     * @param bool $translateSubject
     * @param string $from
     * @return Message
     * @internal param $templateName
     * @internal param Message|NULL $message
     */
	protected function createMessage(string $emailName, string $sendTo, array $templateParams = [], string $subject = 'subject', bool $translateSubject = TRUE, string $from = self::FROM): Message
	{
		$translator = $this->getTranslatorForEmail($emailName);
		
		$templateParams = array_merge($this->getTemplateBaseParams(), $templateParams);
		$emailSubject = ($translateSubject ? $translator->translate($subject, NULL, $templateParams) : $subject) . ' | ' . $this->getAppName();
		$templatePath = $this->getTemplatePath() . "/{$emailName}.latte";
		
		$template = $this->createTemplate($templatePath, $templateParams + ['subject' => $emailSubject]);
		$template->setTranslator($translator);
		
		$message = new Message;
		$message->setFrom($from);
		$message->addTo($sendTo);
		$message->setSubject($emailSubject);
		$message->setHtmlBody($template->renderToString(), __DIR__."/../../../www/images/");
		
		return $message;
	}

	protected function sendMessage(Message $mail): bool
	{
		try{
			$this->mailer->send($mail);
		}catch(SendException $e){
			Debugger::log($e);
			throw new \Exception("common.emailSendError");
		}
		return TRUE;
	}
	
	protected function createAndSendMessage(string $emailName, string $sendTo, array $templateParams = [], string $subject = 'subject', bool $translateSubject = TRUE): bool
	{
		$mail = $this->createMessage($emailName, $sendTo, $templateParams, $subject, $translateSubject);

		return $this->sendMessage($mail);
	}

    protected function createAndSendMessageWithFrom(string $emailName, string $from, string $sendTo, array $templateParams = [], string $subject = 'subject', bool $translateSubject = TRUE): bool
    {
        $mail = $this->createMessage($emailName, $sendTo, $templateParams, $subject, false, $from);

        return $this->sendMessage($mail);
    }

	private function getTemplateBaseParams(): array
	{
		$link = preg_replace("/^:/", "", ":Front:Homepage:default");

		return [
			'appName' => $this->getAppName(),
			'appLink' => $this->linkGenerator->link($link),
		];
	}

	private function getAppName(): string
	{
		return $this->translator->translate('app.name');
	}	

}