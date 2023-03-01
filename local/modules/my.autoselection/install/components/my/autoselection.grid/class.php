<?php

namespace My\AutoSelection;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use My\AutoSelection\Helper;
use My\AutoSelection\Helpers\Iblock;
use My\Autoselection\Helpers\HighloadBlock;

class AutoselectionComponent extends \CBitrixComponent
    implements Controllerable
{
    const IBLOCK_CODE = 'my_autoselection';
    const  HIGHLOADBLOCK_NAME = 'MyAutoSelectionBrands';

    public function executeComponent()
    {
        $this->makeHeaders();
        $this->makeFilter();
        $this->getData();
        $this->includeComponentTemplate();
    }

    public function configureActions()
    {

    }

    public function getData()
    {
        $arValues = [];
        $arSelect = $this->makeSelect();
        foreach ($arSelect as $select) {
            $arValues[] = $select . '_VALUE';
        }
        Loader::includeModule('my.autoselection'); //TODO здесь должна быть проверка
        $IBlockID = Iblock::getIblockId(['CODE' => self::IBLOCK_CODE]); //TODO вынести в конструктор
        $IBlockList = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $IBlockID],
            false,
            false,
            $arSelect
        );
        while ($element = $IBlockList->Fetch()) {
            foreach ($arValues as $value) {
                if (isset($element[$value])) {
                    $row[$value] = $element[$value];
                }
            }
            $this->arResult['ROWS'][] = $row;
        }
        return $this->arResult['ROWS'];
    }

    /**
     * Метод преобразования свойств элементов для запроса GetList()
     * @return array|string[]
     */
    public function makeSelect()
    {
        $arSelect = [];
        $properties = $this->getProperty();
        foreach ($properties as $property) {
            $arSelect[] = 'PROPERTY_' . $property['id'];
        }
//        $arSelect = array_merge($arSelect, ['ID']);
        return $arSelect;
    }

    /**
     * Метод получения свойств элементов
     * @return array|string[]
     */
    public function getProperty()
    {
        $properties = [];
        $iBlockId = Iblock::getIblockId(['CODE' => self::IBLOCK_CODE]); //TODO вынести в конструктор
        $result = \CIBlockElement::GetProperty(
            $iBlockId,
            []
        );
        while ($property = $result->Fetch()) {
            $properties[] = [
                'id' => $property['ID'],
                'name' => $property['NAME'],
                'code' => $property['CODE'],
                'sort' => $property['SORT']
            ];
        }
        return $properties;
    }

    /**
     * Формирование массива заголовков
     * @return array
     */
    public function makeHeaders()
    {
        $headers = [];
        $properties = $this->getProperty();
        foreach ($properties as $property) {
            $headers[] = $property['name'];
        }
        return $this->arResult['HEADERS'] = $headers;
    }

    /**
     * Формирование массива фильтра и полей
     * @return void
     */
    public function makeFilter()
    {
        $filterItem = [];
        $fieldsMap = $this->getProperty();
        foreach ($fieldsMap as $field) {
            $filterItem = [
                'id' => 'PROPERTY_' . $field['id'],
                'name' => $field['name'],
                'code' => $field['code'],
                'default' => 1
            ];
            switch ($filterItem['code']) {
                case 'MY_AUTOSELECTION_BRANDS':
                    $filterItem['type'] = 'list';
                    $filterItem['items'] = $this->getBrands();
                    $filterItem['params'] = ['multiple' => 'Y'];
                    break;
                case 'MY_AUTOSELECTION_CONDITION':
                    $filterItem['type'] = 'list';
                    $filterItem['items'] = ['Поддержанный' => 'Поддержанный', 'Новое' => 'Новое'];
                    $filterItem['params'] = ['multiple' => 'N'];
                    break;
                case 'MY_AUTOSELECTION_PRICE':
                    $filterItem['type'] = 'number';
                    break;
                case 'MY_AUTOSELECTION_YEAR':
                    $filterItem['type'] = 'number';
                    break;
                case 'MY_AUTOSELECTION_RAIN_SENSOR':
                    $filterItem['type'] = 'list';
                    $filterItem['items'] = ['Да' => 'Да', 'Нет' => 'Нет'];
                    $filterItem['params'] = ['multiple' => 'N'];
                    break;
            };
            $this->arResult['FILTER'][] = $filterItem;
            $this->arResult['FILEDS_MAP'][] = $filterItem;
        }
    }

    /**
     * Список брендов из highload-блока
     * @return array|void
     * @throws \Bitrix\Main\SystemException
     */
    public function getBrands()
    {
        $hlBlockId = HighloadBlock::getHlBlockId(['NAME' => self::HIGHLOADBLOCK_NAME]);

        $hlBLockClass = HighloadBlock::getEntityDataClass($hlBlockId);
        $hlBLockList = $hlBLockClass::GetList();
        while ($el = $hlBLockList->Fetch()) {
            $arBrands[$el['UF_BRANDNAME']] = $el['UF_BRANDNAME'];
        }
        if (isset($arBrands) && !empty($arBrands))
            return $arBrands;
    }
}