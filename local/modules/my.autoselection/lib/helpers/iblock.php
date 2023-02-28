<?php

namespace My\AutoSelection\Helpers;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\SystemException;
use MY\AutoSelection\Helper;

class Iblock extends Helper
{
    public static function getIblock(
        $filter = [],
        $select = []
    ): array
    {
        $res = IblockTable::getRow(
            [
                'filter' => $filter,
                'select' => $select
            ]
        );
        if (isset($res) && !empty($res))
            return $res;
        return [];
    }

    public static function createIblock($fields)
    {
        $ob = new \CIBlock();
        return $ob->Add($fields);
    }

    public static function deleteIblock($existId)
    {
        $ob = new \CIBlock();
        return $ob::Delete($existId);
    }

    public static function getIblockId(
        $filter = []
    ): int
    {
        $arIblockId = self::getIblock(
            $filter,
            ['ID']
        );
        if (!empty($arIblockId['ID'])) {
            return intval($arIblockId['ID']);
        } else
            throw new SystemException('Не установлен необходимый инфоблок');
    }

    public static function getEntity(string $apiCode): object
    {
        return IblockTable::compileEntity($apiCode);

    }
}