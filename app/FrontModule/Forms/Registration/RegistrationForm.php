<?php
declare(strict_types=1);

namespace App\FrontModule\Forms;

use App\Model\Orm;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use App\Model\Emails\CustomerEmailService;
use App\Model\Services\CustomerRegistrationService;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

interface IRegistrationFormFactory
{
    /** @return RegistrationForm */
	function create(): RegistrationForm;
}

class RegistrationForm extends Control {

	private Orm $orm;

	private ITranslator $translator;

	public CustomerEmailService $customerEmailService;

	public CustomerRegistrationService $customerRegistrationService;

	public function __construct(CustomerRegistrationService $customerRegistrationService, Orm $orm, ITranslator $translator, CustomerEmailService $customerEmailService) {
		$this->customerRegistrationService = $customerRegistrationService;
		$this->orm = $orm;
		$this->translator = $translator;
		$this->customerEmailService = $customerEmailService;
	}

	public function createComponentForm(): Form
	{
	    $formDomain = 'form.userRegistration';

	    $form = new Form();

	    $form->addText('email', $this->translator->translate("$formDomain.email"))
            ->setRequired($this->translator->translate("$formDomain.emailRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('name', $this->translator->translate("$formDomain.name"))
            ->setRequired($this->translator->translate("$formDomain.nameRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('surname', $this->translator->translate("$formDomain.surname"))
            ->setRequired($this->translator->translate("$formDomain.surnameRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('phone', $this->translator->translate("$formDomain.phone"))
            ->setRequired($this->translator->translate("$formDomain.phoneRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('street', $this->translator->translate("$formDomain.street"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('city', $this->translator->translate("$formDomain.city"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('zip', $this->translator->translate("$formDomain.zip"))
            ->setHtmlAttribute('class', 'form-control');

	    $form->addPassword('password',  $this->translator->translate("$formDomain.password"))
            ->setRequired($this->translator->translate("$formDomain.passwordRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword('passwordCheck',  $this->translator->translate("$formDomain.passwordCheck"))
            ->setRequired($this->translator->translate("$formDomain.passwordCheckRequired"))
            ->setHtmlAttribute('class', 'form-control');

        $form->addCheckbox('mailNotifications', $this->translator->translate("$formDomain.mailNotifications"));

        $form->addCheckbox('phoneNotifications', $this->translator->translate("$formDomain.phoneNotifications"));

        $form->addCheckbox('privacyPolicy', $this->translator->translate("$formDomain.privacyPolicy", ['link' => $this->getPresenter()->link(':Front:Pages:gdpr')]))
            ->setRequired($this->translator->translate("$formDomain.cookiesRequired"));

        $form->addCheckbox('termsOfConditions', $this->translator->translate("$formDomain.termsOfConditions", ['link' => $this->getPresenter()->link(':Front:Pages:gdpr')]))
            ->setRequired($this->translator->translate("$formDomain.termsOfConditionsRequired"));

        /*
        $form->addCheckbox('termsOfUseAgreement', $this->translator->translate("$formDomain.termsOfUseAgreement"))
            ->setRequired($this->translator->translate("$formDomain.termsAgreementRequired"));*/


        $form->addSubmit("save", $this->translator->translate("common.login.registration"))
            ->setHtmlAttribute('class', 'btn btn-primary btn-lg');

		$form->onSuccess[] = [$this, 'formSuccess'];

		return $form;
	}

	public function formSuccess(Form $form, ArrayHash $values): void
	{
        $formDomain = 'form.userRegistration';

        try{
		    if ($this->orm->users->getByEmail($values->email)) {
                $form->addError($this->translator->translate("form.user.emailConflict", ['link' => $this->getPresenter()->link('//Login:')]));
                return;
            }

		    if ($values->password !== $values->passwordCheck) {
                $form->addError($this->translator->translate("$formDomain.passwordsNoMatch"));
                return;
            }

			$customer = $this->customerRegistrationService->createNewCustomer($values);

            $this->customerEmailService->confirmRegistration($values, $customer);
		}catch(\Exception $exception){
            Debugger::barDump($exception);
            Debugger::log($exception->getMessage(), ILogger::ERROR);
			$form->addError($this->translator->translate("form.user.error"));
			return;
		}
        $this->getPresenter()->flashMessage($this->translator->translate("$formDomain.success"));
        $this->getPresenter()->redirect('Login:');
	}

	public function render(): void
    {
        $this->template->setFile(__DIR__ . '/../../Forms/Registration/RegistrationForm.latte');
        $this->template->render();
    }



}
