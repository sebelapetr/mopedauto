<?php declare(strict_types=1);

namespace App\FrontModule\Forms;

use BeUtils\BeVisualComponents\BaseForm;
use BeUtils\BeVisualComponents\IFormRendererComponentFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use App\Components\BaseFormComponent;
use App\Model\Orm;
use Tracy\Debugger;

class LoginPasswordForm extends Control
{
    private string $userEmail;

    public array $onSuccessCallback;
    protected Orm $orm;
    protected ITranslator $translator;

    public function __construct(Orm $orm, ITranslator $translator, string $userEmail)
    {
        $this->orm = $orm;
        $this->translator = $translator;
        $this->userEmail = $userEmail;
    }

    protected function createComponentForm()
    {
        $form = new \Nette\Application\UI\Form();
        Debugger::barDump($this->userEmail);
        $form->addText('email', 'Váš e-mail')
            ->setDefaultValue($this->userEmail)
            ->setRequired();
        $form->addPassword('password', 'Heslo')
            ->setRequired();
        $form->addSubmit('submit', "Přihlásit se");
        $form->onSuccess = $this->onSuccessCallback;
        return $form;
    }

    public function render(array $options = []): void
    {
        $this->template->setFile(__DIR__ . "/LoginPasswordForm.latte");
        $this->template->email = $this->userEmail;
        $this->template->render();
    }
}
