<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Barsumnet\Farpost;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Description of Taggsm
 *
 * @author User
 */
class Taggsm {

    public function setPrice() {

        //Название прайса на выходе
        $outFile = 'downloads/ocean_repairs.xls';

        //Название праса поставщика
        $file = 'downloads/' . 'taggsm.xls';


        $export = new Spreadsheet();

        //Название листа
        $page = $export->setActiveSheetIndex(0);
        $page->setTitle('Прайс');

        //Устанавливаем ширину колонок
        $export->getActiveSheet()->getColumnDimension('C')->setWidth(60);
        $export->getActiveSheet()->getColumnDimension('H')->setWidth(60);

        //Устанавливаем жирный шрифт и размер шрифта
        $export->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(12)->setBold(true);

        //Устанавливаем заголовок
        $export->getActiveSheet()->setCellValue('A1', 'Артикул');
        $export->getActiveSheet()->setCellValue('B1', 'Vendor');
        $export->getActiveSheet()->setCellValue('C1', 'Наименование');
        $export->getActiveSheet()->setCellValue('D1', 'Цена');
        $export->getActiveSheet()->setCellValue('E1', 'Наличие');
        $export->getActiveSheet()->setCellValue('F1', 'Сроки доставки');
        $export->getActiveSheet()->setCellValue('G1', 'Состояние');
        $export->getActiveSheet()->setCellValue('H1', 'Описание');

        $reader = new Xls();

        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($file);

        //Выбирам лист для чтения
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        //Кол-во строк
        $highestRow = $sheet->getHighestRow();

        //Номер строки в новом записываемом файле
        $count = 2;

        //Позволяет получить данные о листах
        //var_dump($reader->ListWorksheetInfo($file));

        for ($row = 13; $row <= $highestRow; $row++) {

            if ($sheet->getCell('C' . $row)->getValue() == null) {
                //|| empty($rowData[0][2]) ||
//                    $rowData[0][5] == 'К' ||
//                    preg_match("/(АКБ)/", $rowData[0][2]) ||
//                    preg_match("/(Аккумулятор)/", $rowData[0][2]) ||
//                    preg_match("/(Чехол-аккумулятор)/", $rowData[0][2])) {
                continue;
            }


            if ($this->selectName($sheet->getCell('C' . $row)->getValue(), ['Дисплей', 'аккумулятор'])) {
                //continue;
                //Артикул
                $export->getActiveSheet()->setCellValue('A' . $count, 'D' . ($count + 10000));
                //Артикул поставщика
                $export->getActiveSheet()->setCellValue('B' . $count, 'zm' . $sheet->getCell('B' . $row)->getValue());
                //Наименование
                //str_replace();  Установка дисплея на

                $export->getActiveSheet()->setCellValue('C' . $count, preg_replace('/(Дисплей для)/', 'Установка дисплея на', $sheet->getCell('C' . $row)->getValue()));
                //Цена
                //$export->getActiveSheet()->setCellValue('D' . $count, $sheet->getCell('D' . $row)->getValue());
                $export->getActiveSheet()->setCellValue('D' . $count, $this->replacePrice($sheet->getCell('D' . $row)->getValue()));
                //Наличие
                $export->getActiveSheet()->setCellValue('E' . $count, 'Под заказ');
                //Сроки доставки
                $export->getActiveSheet()->setCellValue('F' . $count, '2-3  часа');
                //Состояние товара
                $export->getActiveSheet()->setCellValue('G' . $count, 'Новый');
                //Описание
                $export->getActiveSheet()->setCellValue('H' . $count, '');


                $count++;
            }
        }

        //Записываем сформированный прайс
        $writer = IOFactory::createWriter($export, 'Xls');
        $writer->save($outFile);
    }

    public function getPrice() {
        file_put_contents("downloads/" . "taggsm.xls", $this->getFile());
        // var_dump($this->getFile($file));
        return true;
    }

    public function getFile() {

        $file = 'https://taggsm.ru/download/price_list/price_' . date("d-m-Y") . '_vladivostok.xls';

        if ($this->isAvailable($file, 120)) {
            $file = file_get_contents($file);
            return $file;
        }
    }

    protected function isAvailable($url, $timeout) {
        $ch = curl_init(); // get cURL handle
        // set cURL options
        $opts = array(CURLOPT_RETURNTRANSFER => true, // do not output to browser
            CURLOPT_URL => $url, // set URL
            CURLOPT_NOBODY => true, // do a HEAD request only
            CURLOPT_TIMEOUT => $timeout);   // set timeout
        curl_setopt_array($ch, $opts);

        curl_exec($ch); // do it!

        $retval = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200; // check if HTTP OK

        curl_close($ch); // close handle

        return $retval;
    }

    protected function selectName($verifiableData, $data) {

        foreach ($data as $val) {

            $pos = strpos($verifiableData, $val);

            if ($pos !== false) {
                return true;
            } else {
                continue;
            }
        }
    }

    protected function replaceName($data) {

        return true;
    }

    protected function replacePrice($data) {
        //  $data = $data + 1000;


        $rounded = (ceil($data / 50)) * 50 + 250;
        if ($rounded >= 100 && $rounded <= 400)
            $result = $rounded + 1500;
        else if ($rounded >= 400 && $rounded <= 800)
            $result = $rounded + 1500;
        else if ($rounded >= 800 && $rounded <= 1500)
            $result = $rounded + 1200;
        else if ($rounded >= 1500 && $rounded <= 2200)
            $result = $rounded + 1600;
        else if ($rounded >= 2200 && $rounded <= 3000)
            $result = $rounded + 1800;
        else if ($rounded >= 3000 && $rounded <= 6200)
            $result = $rounded + 2000;
        else if ($rounded >= 6200 && $rounded <= 12000)
            $result = $rounded + 2400;
        else if ($rounded >= 12000 && $rounded <= 18000)
            $result = $rounded + 3000;
        else if ($rounded >= 18000 && $rounded <= 30000)
            $result = $rounded + 5000;
        return $result;
    }

}
