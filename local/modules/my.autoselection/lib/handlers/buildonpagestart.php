<?php

namespace My\Autoselection\Handlers;

use Bitrix\Main\UI\Extension;
use Bitrix\Main\Context;
class BuildOnPageStart
{
    public static function bootstrapOn()
    {
        $request = Context::getCurrent()->getRequest();
        if (!$request->isAdminSection())
            Extension::load('ui.bootstrap4');
    }
}