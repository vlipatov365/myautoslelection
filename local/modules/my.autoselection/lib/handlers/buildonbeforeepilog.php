<?php

namespace My\Autoselection\Handlers;

use Bitrix\Main\Page\Asset;

class BuildOnBeforeEpilog
{
    public static function addHeaderButton()
    {
        $asset = Asset::getInstance();
        $asset->addJs('/local/js/script.js', true);
    }
}