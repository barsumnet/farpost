<?php

namespace Barsumnet\Farpost;

use Wa72\HtmlPageDom\HtmlPage;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class Farpost {

    public $store = [
        'RestartService' => 'https://www.farpost.ru/user/RestartService/tech/communication/parts/?page=',
//        'iMarket' => 'https://www.farpost.ru/user/iMarketShop/tech/communication/parts/+/%C4%E8%F1%EF%EB%E5%E8/?goodPresentState%5B%5D=present&page=',
//        'iCanHelp' => 'https://www.farpost.ru/user/iCanHelp/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'VNGSM' => 'https://www.farpost.ru/user/vngsm/tech/communication/parts/?page=',
//        'iVietStore' => 'https://www.farpost.ru/user/iVietStore/tech/communication/parts/+/%C4%E8%F1%EF%EB%E5%E8/?goodPresentState%5B%5D=present&page=',
//        'ElectraShop' => 'https://www.farpost.ru/user/ElectraShop/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'DvSota' => 'https://www.farpost.ru/user/dvsotarepair/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'GsmService' => 'https://www.farpost.ru/user/gsmservicevl/tech/communication/parts/?page=',
//        'iMegaVlad' => 'https://www.farpost.ru/user/imegavl/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'StarPhone' => 'https://www.farpost.ru/user/JediGSM/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'Noutparts' => 'https://www.farpost.ru/user/Noutparts/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
//        'Oceanshop' => 'https://www.farpost.ru/user/Oceanshopcom/tech/communication/parts/?page=',
//        'TechnoShop' => 'https://www.farpost.ru/user/Technoshop/tech/communication/parts/+/%C4%E8%F1%EF%EB%E5%E8/?goodPresentState%5B%5D=present&page=',
//        'Надежный компьютер' => 'https://www.farpost.ru/user/Demonyak/tech/communication/parts/?goodPresentState%5B%5D=present&page=',
    ];
    public $product = [];

    function __construct() {
        $this->product[] = [
            'id' => 'ID',
            'name' => 'Наименование',
            'price' => 'Цена',
            'category' => 'Категория',
            'store' => 'Магазин',
            'currency' => 'Валюта',
        ];
    }

    public function getCSV() {
        $fp = fopen('farpost_price.csv', 'w');

        foreach ($this->generateData() as $fields) {

            fwrite($fp, implode(',', $fields) . "\r\n");
            //fputcsv($fp, $fields, ',');
        }

        fclose($fp);
    }

    public function generateData() {

        $id = 0;
        //Проходим циклом по ссылкам продавцов
        foreach ($this->store as $key => $item) {
            //Получаем кол-во страниц
            $countPage = $this->countPage($item);
            //Проходим циклам по страницам
            for ($i = 1; $i <= $countPage; $i++) {
                //Получаем страницу
                $page = new HtmlPage(file_get_contents($item . $i), '', 'windows-1251');
                //Фильтруем текущию страницу
                $page->filter('tr.bull-item');

                //Проходим циклом по самой странице
                foreach ($page->filter('tr.bull-item') as $val) {
                    $val = new HtmlPageCrawler($val);


                    $this->product[] = [
                        'id' => ++$id,
                        //'name' => $this->substrName($val->filter('a.bulletinLink')->text()),
                        'name' => $val->filter('a.bulletinLink')->text(),
                        'price' => $this->priceReplace($val->filter('.priceCell')->text()),
                        'category' => 'Запчасти',
                        'store' => $key,
                        'currency' => 'RUB',
                    ];
                }
            }
        }

        return $this->product;
    }

    //Удаляем лишние символы в строке наименования 
    private function substrName($str) {
        substr($str, 1, -1);
    }

    //Получаем кол-во страниц магазина
    private function countPage($url) {
        //Получаем страницу
        $page = new HtmlPage(file_get_contents($url), '', 'windows-1251');

        //Кол-во страниц
        $pageCount = $this->сountReplace($page->filter('#itemsCount_placeholder span strong')->text());

        return $pageCount;
    }

    //Убираем все лишние данные полученые со страницы и возвращаем кол-во страниц
    private function сountReplace($str) {

        $preg = preg_replace('/(предложений|предложение|предложения)/', '', $str);

        $count = str_replace(' ', '', $preg);

        $result = ceil($count / 50);

        return (int) $result;
    }

    //Убираем все лишние данные полученые со страницы и возвращаем цену определнной единицы
    private function priceReplace($str) {
        $result = str_replace(' ', '', substr($str, 0, -3));
        return $result;
    }

}
