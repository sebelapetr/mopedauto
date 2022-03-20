<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\Setting;
use Contributte\Translation\Translator;
use Nette;
use Tracy\Debugger;

class SettingsForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    private Translator $translator;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->orm = $orm;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/SettingsForm.latte');
        $this->template->settings = $this->orm->settings->findBy(['showInSettings' => true]);
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $domain = "settings";

        foreach ($this->orm->settings->findBy(['showInSettings' => true]) as $setting)
        {
            if ($setting->type === Setting::TYPE_BOOLEAN)
            {
                $form->addCheckbox($setting->option, $this->translator->translate("$domain.$setting->option"))
                    ->setHtmlAttribute('class', 'form-check-input');
            } elseif ($setting->type === Setting::TYPE_STRING) {
                $form->addText($setting->option, $this->translator->translate("$domain.$setting->option"))
                    ->setHtmlAttribute('class', 'form-control');
            }
        }

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        $defaults = [];

        foreach ($this->orm->settings->findBy(['showInSettings' => true]) as $setting)
        {
            $defaults[$setting->option] = $setting->getValueWithType();
        }

        $form->setDefaults($defaults);

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values): void
    {
        unset($values['send']);

        foreach ($values as $option => $value)
        {
            $setting = $this->orm->settings->getBy(['option' => $option]);
            if ($setting->type === Setting::TYPE_BOOLEAN)
            {
                $setting->value = $value ? '1' : '0';
            }
            $this->orm->persist($setting);
        }
        $this->orm->flush();

        $this->getPresenter()->flashMessage('Nastavení bylo uloženo.', FlashMessages::SUCCESS);

        $this->getPresenter()->redirect('this');
    }
}