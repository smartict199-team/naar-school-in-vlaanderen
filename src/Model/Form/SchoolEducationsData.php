<?php

declare(strict_types=1);

namespace App\Model\Form;

class SchoolEducationsData extends AbstractEducationsData
{
    private bool $formTypeVisibleOnFrontend = true;
    private bool $finalityVisibleOnFrontend = true;

    public function isFormTypeVisibleOnFrontend(): bool
    {
        return $this->formTypeVisibleOnFrontend;
    }

    public function setFormTypeVisibleOnFrontend(bool $formTypeVisibleOnFrontend): void
    {
        $this->formTypeVisibleOnFrontend = $formTypeVisibleOnFrontend;
    }

    public function isFinalityVisibleOnFrontend(): bool
    {
        return $this->finalityVisibleOnFrontend;
    }

    public function setFinalityVisibleOnFrontend(bool $finalityVisibleOnFrontend): void
    {
        $this->finalityVisibleOnFrontend = $finalityVisibleOnFrontend;
    }
}
