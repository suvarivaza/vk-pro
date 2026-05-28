<?php

namespace Service\Pages;

class Controller_Admin_Prices_List extends Controller_Admin
{
    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $query = $this->factory->prices->query()->sort('Order', 'ASC');
        $query->limit($this->limit);

        $it = $query->iterator();
        $list = [];

        /** @var Model_Prices_Price $price */
        foreach ($it as $price) {
            $list[] = $price;
        }

        $vars = [
            'list' => $list,
            'app' => $this->_application,
            'errors' => $this->_errors,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/prices/list', $vars));
    }

    /**
     * Обработчик POST-запросов
     *
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'prices':
                return $this->_prices();
        }

        return null;
    }

    private function _prices()
    {
        $query = $this->factory->prices->query()->limit($this->limit);
        $it = $query->iteratorForSave();
        $prices = [];

        foreach ($it as $price) {
            $prices[] = $price;
        }

        /* Удаляем отмеченые */
        /*
         * @var Model_Prices_Price
         */
        foreach ($prices as $id => $price) {
            if ($this->_request->post['Delete'][$price->Alias]->int(0) === 1) {
                $this->factory->prices->delete($price);
                unset($prices[$id]);
            }
        }

        /* Загружаем позиции */
        foreach ($prices as $id => $price) {
            $name = $price->Alias;
            $arr = explode('.', $_FILES['Price']['name'][$price->Alias]);
            $ext = array_pop($arr);
            $name .= '.' . $ext;

            if (isset($_FILES['Download']['tmp_name'][$price->Alias]) && is_file($_FILES['Download']['tmp_name'][$price->Alias])) {
                $name = \Lib_Html::HTMLOut($_FILES['Download']['name'][$price->Alias]);
                copy($_FILES['Download']['tmp_name'][$price->Alias], ENGINE_PATH . '/files/prices/' . $name);
                $sql = "INSERT INTO `settings` SET `name` = 'Download_" . $price->Alias . "', `value` = '" . addslashes($name) . "' ON DUPLICATE KEY UPDATE `value` = '" . addslashes($name) . "'";

                mysql_query($sql);
            }

            if (empty($_FILES['Price']['tmp_name'][$price->Alias])) {
                continue;
            }

            $line = $_POST['Line'][$price->Alias];
            $price->File = $name;

            copy($_FILES['Price']['tmp_name'][$price->Alias], ENGINE_PATH . '/files/prices/' . $name);

            $xls = \PHPExcel_IOFactory::load($_FILES['Price']['tmp_name'][$price->Alias]);
            $sheet = $xls->getSheet();

            $this->factory->positions->deleteByPriceID($price->PriceID);
            $current = $row = 0;
            /*
             * @var \PHPExcel_Worksheet_Row
             */
            foreach ($sheet->getRowIterator() as $pos) {
                $current++;

                if ($current <= $line) {
                    continue;
                }

                foreach ($price->getFields() as $field) {
                    $cell = $sheet->getCellByColumnAndRow($field->Column, $current);
                    $position = $this->factory->positions->getNew();
                    $position->PriceID = $price->PriceID;
                    $position->Column = $field->Column;
                    $position->Row = $row;
                    $position->Value = strval($cell->getValue());

                    $list[] = $position;
                }
                $row++;
            }

            if (!count($list)) {
                $this->_errors[] = 'Не удалось загрузить прайс ' . $price->Title . '. Проверьте правильность заполнения';
            }

            foreach ($list as $position) {
                $this->factory->positions->save($position);
            }
            $this->factory->prices->save($price);
        }

        return null;
    }
}
