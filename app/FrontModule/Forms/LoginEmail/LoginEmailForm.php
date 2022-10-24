<?php declare(strict_types=1);

namespace App\FrontModule\Forms;

use App\Model\Customer;
use App\Model\Orm;
use Nette\Application\UI\Control;
use Nette\Forms\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

class LoginEmailForm extends Control
{
    public array $onSuccessCallback;
    protected Orm $orm;
    protected ITranslator $translator;

    public function __construct(Orm $orm, ITranslator $translator)
    {
        $this->orm = $orm;
        $this->translator = $translator;
    }


    protected function createComponentForm()
    {
        $form = new \Nette\Application\UI\Form();
        $form->addText('email', 'Váš e-mail')
            ->setRequired();
        $form->addSubmit('submit', "Přihlásit se")
            ->setHtmlId("login-email-submit");
        $form->onSuccess = $this->onSuccessCallback;
        return $form;
    }

    public function render(array $options = []): void
    {
        $this->template->setFile(__DIR__ . "/LoginEmailForm.latte");
        $this->template->render();
    }
}
