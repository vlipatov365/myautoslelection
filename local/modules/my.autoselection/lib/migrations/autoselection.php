<?php

namespace My\Autoselection\Migrations;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use My\Autoselection\Helper;
use My\Autoselection\Helpers\Iblock;
use CList;

class Autoselection
{

    public function __construct(
        //TODO передать сайт для установки, проверку необходимых модулей, инфоблоков и пр
    )
    {
        $this->vIsExist = false;
    }

    public function up()
    {
        global $CACHE_MANAGER;
        //TODO что делать, если IBLOCK установлен
        Helper::initModules(['lists']);
        $IBlockID = Iblock::createIblock(self::arIblockSettings());
        if ($IBlockID > 0) {
//            $obList = new \CList($IBlockID);
//            $arFields = self::combineFieldsToArray($IBlockID);
//            foreach ($arFields as $field) {
//                $obList->addField($field);
            }
        $iBlockProp = new \CIBlockProperty();
        $arProps = self::combineFieldsToArray($IBlockID);
        foreach ($arProps as $vProp) {
            $iBlockProp->Add($vProp);
        }

        $CACHE_MANAGER->ClearByTag('list_list_' . $IBlockID);
        $CACHE_MANAGER->ClearByTag('list_list_any');
        $CACHE_MANAGER->CleanDir('menu');

    }

    //TODO добавить метод для проверки, что все установилось правильно
    public static function down()
    {

    }

    protected static function arIblockSettings()
    {
        return [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_IBLOCK_NAME'),
            'CODE' => 'my_autoselection',
            'API_CODE' => 'myAutoSelection',
            'IBLOCK_TYPE_ID' => 'lists',
            'LIST_PAGE_URL' => '#SITE_DIR#/auto-selection/',
            'SECTION_PAGE_URL' => '#SITE_DIR#/auto-selection/#SECTION_CODE#/',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/auto-selection/#SECTION_CODE#/#ELEMENT_CODE#/',
            'SITE_ID' => 's1',
            'VERSION' => '1',
            'DESCRIPTION' => '',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'Y',
            'SECTIONS' => [
                'NAME' => Loc::getMessage('MY_AUTOSELECTION_SECTION_NAME'),
                'CODE' => 'AutoSelection',
            ],
            'ELEMENT_NAME' => 'Автомобиль',
            'ELEMENTS_NAME' => 'Автомобили',
            'ELEMENT_ADD' => 'Добавить автомобиль',
            'ELEMENT_EDIT' => 'Изменить автомобиль',
            'ELEMENT_DELETE' => 'Удалить автомобиль',
            'SECTION_NAME' => 'Разделы',
            'SECTIONS_NAME' => 'Раздел',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить Раздел',
            'SECTION_DELETE' => 'Удалить Раздел',
        ];
    }

    protected static function fieldDefaultSettings()
    {
        return [
            'ACTIVE' => 'Y',
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'DEFAULT_VALUE' => '',
            'USER_TYPE_SETTINGS' => NULL,
            'SETTINGS' => [
                'SHOW_ADD_FORM' => 'Y',
                'SHOW_EDIT_FORM' => 'Y',
                'ADD_READ_ONLY_FIELD' => 'N',
                'EDIT_READ_ONLY_FIELD' => 'N',
                'SHOW_FIELD_PREVIEW' => 'N',
            ],
            'SEARCHABLE' => 'Y',
            'FILTRABLE' => 'Y'
        ];
    }

    protected static function combineFieldsToArray($IBlockID)
    {
        return [
            array_merge(['IBLOCK_ID' => $IBlockID], self::fieldPropBrands()),
            array_merge(['IBLOCK_ID' => $IBlockID], self::fieldPropCondition()),
            array_merge(['IBLOCK_ID' => $IBlockID], self::fieldPropPrice()),
            array_merge(['IBLOCK_ID' => $IBlockID], self::fieldPropYear()),
            array_merge(['IBLOCK_ID' => $IBlockID], self::fieldPropRainSensor())
        ];
    }

    protected static function fieldPropBrands(): array
    {
        $arSettings = [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_FIELD_BRANDS'),
            'CODE' => 'MY_AUTOSELECTION_BRANDS',
            'SORT' => 10,
            'PROPERTY_TYPE' => 'S',
            'LIST_TYPE' => 'L',
            'USER_TYPE' => 'directory',
            "MULTIPLE" => "N",
            'USER_TYPE_SETTINGS' => [
                'size' => 1,
                'width' => 0,
                'group' => 'N',
                'multiple' => 'N',
                'TABLE_NAME' => 'my_autoselection_brands'
            ]
        ];
        return array_merge($arSettings, self::fieldDefaultSettings());
    }

    protected static function fieldPropCondition(): array
    {
        $arSettings = [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_FIELD_CONDITION'),
            'CODE' => 'MY_AUTOSELECTION_CONDITION',
            'SORT' => 10,
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'C',
            'VALUES' => [
                [
                    'VALUE' => Loc::getMessage('MY_AUTOSELECTION_FIELD_CONDITION_NEW'),
                ],
                [
                    'VALUE' => Loc::getMessage('MY_AUTOSELECTION_FIELD_CONDITION_USED'),
                ]
            ]
        ];
        return array_merge($arSettings, self::fieldDefaultSettings());
    }

    protected static function fieldPropYear(): array
    {
        $arSettings = [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_FIELD_YEAR'),
            'CODE' => 'MY_AUTOSELECTION_YEAR',
            'SORT' => 10,
            'PROPERTY_TYPE' => 'N',
            'COL_COUNT' => 4
        ];
        return array_merge($arSettings, self::fieldDefaultSettings());
    }

    protected static function fieldPropPrice(): array
    {
        $arSettings = [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_FIELD_PRICE'),
            'CODE' => 'MY_AUTOSELECTION_PRICE',
            'SORT' => 10,
            'PROPERTY_TYPE' => 'N',
            'COL_COUNT' => 15
        ];
        return array_merge($arSettings, self::fieldDefaultSettings());
    }

    protected static function fieldPropRainSensor(): array
    {
        $arSettings = [
            'NAME' => Loc::getMessage('MY_AUTOSELECTION_FIELD_RAIN_SENSOR'),
            'CODE' => 'MY_AUTOSELECTION_RAIN_SENSOR',
            'SORT' => 10,
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'L',
            'VALUES' => [
                [
                    'VALUE' => Loc::getMessage('MY_AUTOSELECTION_FIELD_RAIN_SENSOR_YES'),
                ],
                [
                    'VALUE' => Loc::getMessage('MY_AUTOSELECTION_FIELD_RAIN_SENSOR_NO'),
                ]
            ]
        ];
        return array_merge($arSettings, self::fieldDefaultSettings());
    }

    protected static function checkExist()
    {
        Helper::initModules(['lists']);
        $iBlockId = Iblock::getIblock(['CODE' => self::arIblockSettings()['CODE']], ['ID']);
        if (!empty($iBlockId))
            return true;
        else
            return false;

    }
}