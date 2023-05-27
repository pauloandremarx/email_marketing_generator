<?php

namespace app\classes;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigFilters extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('message', [$this, 'showMessage'])
        ];
    }

    public function showMessage($message, $alert)
    {
        if (is_string($message) && !empty($message)) {
            return "<p class='uk-alert-{$alert} margin-top-nagative-alert'>{$message}</p>";
        }
    }
}
