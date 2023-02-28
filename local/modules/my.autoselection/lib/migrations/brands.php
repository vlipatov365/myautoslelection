<?php

namespace My\Autoselection\Migrations;

use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use My\Autoselection\Helpers;
use My\Autoselection\Helper;

class Brands
{
    public function up()
    {
        Loader::includeModule('highloadblock');
        if (empty($this->isExists)) {
            $result = HighloadBlockTable::add([
                'NAME' => 'MyAutoSelectionBrands',
                'TABLE_NAME' => 'my_autoselection_brands'
            ]);
            if ($result->isSuccess()) {
                $hlbId = $result->getId();
                HighloadBlockLangTable::add([
                    'ID' => $hlbId,
                    'LID' => 's1',
                    'NAME' => Loc::getMessage('HLB_BRANDS_NAME')
                ]);
            }

            self::addUserTypeEntity(self::getUfArFields($hlbId, [
                'FIELD_NAME' => 'UF_BRANDNAME',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'NAME',
                'LABEL' => Loc::getMessage('MY_AUTOSELECTION_HL_BRANDNAME')
            ]));
            self::addUserTypeEntity(self::getUfArFields($hlbId, [
                'FIELD_NAME' => 'UF_XML_ID',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'XML_ID',
                'LABEL' => 'UF_XML_ID'
            ]));
            //TODO массив с полями
        }
    }

    public function down()
    {

        if (!empty($this->isExists)) {
            $result = HighloadBlockTable::delete($this->isExists['ID']);
            if (!$result->isSuccess()) {
                throw new SystemException(implode(';', $result->getErrorMessages()));
            }
        }
    }

    protected static function getUfArFields($hlid, $data)
    {
        $arFields = [
            'ENTITY_ID' => 'HLBLOCK_' . $hlid,
            'FIELD_NAME' => $data['FIELD_NAME'],
            'USER_TYPE_ID' => $data['USER_TYPE_ID'],
            'XML_ID' => $data['FIELD_NAME'],
            'SORT' => $data['SORT'] ?: 100,
            'MULTIPLE' => $data['MULTIPLE'] ?: 'N',
            'MANDATORY' => $data['MANDATORY'] ?: 'N',
            'SHOW_FILTER' => $data['SHOW_FILTER'] ?: 'Y',
            'SHOW_IN_LIST' => $data['SHOW_IN_LIST'] ?: 'Y',
            'EDIT_IN_LIST' => $data['EDIT_IN_LIST'] ?: 'Y',
            'IS_SEARCHABLE' => $data['IS_SEARCHABLE'] ?: 'N',
            'EDIT_FORM_LABEL' => ['ru' => $data['LABEL']],
            'LIST_COLUMN_LABEL' => ['ru' => $data['LABEL']],
            'LIST_FILTER_LABEL' => ['ru' => $data['LABEL']],
            'ERROR_MESSAGE' => ['ru' => '',],
            'HELP_MESSAGE' => ['ru' => '',],
        ];

        switch ($data['USER_TYPE_ID']) {
            case 'boolean':
                $arFields['SETTINGS'] = [
                    'DEFAULT_VALUE' => 1,
                    'DISPLAY' => 'CHECKBOX',
                    'LABEL' => ['', ''],
                    'LABEL_CHECKBOX' => '',

                ];
            case 'integer':
                $arFields['SETTINGS'] = [
                    'SIZE' => 20,
                    'MIN_VALUE' => 0,
                    'MAX_VALUE' => 0,
                    'DEFAULT_VALUE' => ''
                ];
            case 'string':
                $arFields['SETTINGS'] = [
                    'SIZE' => 20,
                    'DISPLAY' => 'SELECT',
                    'ROWS' => 1,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => ''
                ];
                break;
        }
        return $arFields;
    }

    protected static function addUserTypeEntity($field)
    {
        $obUserField = new \CUserTypeEntity;
        $obUserField->Add($field);
    }

    static function hlElements()
    {
        return [
            'Audi',
            'BMW',
            'Subaru',
            'Tesla'
        ];
    }

//    public static function addHlElements($id)
//    {
//        $hlEntityDataClass = Helpers\HighloadBlock::getEntityDataClass($id);
//        $elements = self::hlElements();
//        foreach ($elements as $element) {
//            $hlEntityDataClass::add([
//                "UF_BRANDNAME" => $element,
//                "UF_XML_ID" => $element
//            ]);
//        }
//    }
}
