<?php

namespace Service\Logs;

use Database_Logs;
use Database_Orders;
use Lib_DB_Factory;
use PDO;
use Service\Catalog\Model_Prices_Price;
use System\Service_Controller_Shell;

class Controller_Shell_Statistic_PriceDelivery extends Service_Controller_Shell
{
    private $_result = [];

    /** @var Model_Prices_Price[] */
    private $_prices;

    public function A_start()
    {
        $this->_prices = $this->factoryCatalog->prices->getAll();

        $dbLogs = Lib_DB_Factory::GetInstance(new Database_Logs());
        $dbOrders = Lib_DB_Factory::GetInstance(new Database_Orders());

        $itemIDs = [];
        $resultLogs = [];

        $weekBegin = strtotime('last Monday');
        //$weekBegin = strtotime( "2016-08-01" );
        $weekFinish = strtotime('Sunday') + 86399;

        var_dump(date('d.m.Y H:i:s', $weekBegin));
        var_dump(date('d.m.Y H:i:s', $weekFinish));

        foreach ($this->_prices as $price) {
            $tables = [];
            $tables[] = 'log_' . date('Y', $weekBegin) . '_' . date('m', $weekBegin);

            if (date('m', $weekBegin) != date('m', $weekFinish) || date('Y', $weekBegin) != date('Y', $weekFinish)) {
                $tables[] = 'log_' . date('Y', $weekFinish) . '_' . date('m', $weekFinish);
            }

            foreach ($tables as $table) {
                //var_dump( 'table: ' . $table );
                $sql = 'SELECT a.`itemId`, a.`date`, a.`statusId`, b.`date` as b_date, b.`statusId` as b_statusId, b.`userId` as b_userId';
                $sql .= ' FROM `' . $table . '` a';
                $sql .= ' INNER JOIN `' . $table . '` b ';
                $sql .= ' ON a.`itemId`=b.`itemId`';

                if ($price->priceId == 5) {
                    $sql .= " AND b.`url` = '/admin/warehouses/print/sticker/autodoc'";
                } else {
                    $sql .= ' AND b.`statusId` IN (8, 15)';
                }
                $sql .= " AND b.`action` = '" . Model_Config::ORDERS_CHANGE_STATUS . "'";
                $sql .= ' AND b.`userId` IN (148,854)';
                $sql .= ' WHERE a.`priceId` = ' . $price->priceId;
                $sql .= " AND a.`action` = '" . Model_Config::ORDERS_CHANGE_STATUS . "'";
                $sql .= ' AND a.`statusId` IN (' . \Service\Orders\Model_Config::STATUS_SUP_SENT . ', ' . \Service\Orders\Model_Config::STATUS_SUP_ADOPTED . ', ' . \Service\Orders\Model_Config::STATUS_WORK . ')';
                $sql .= " AND a.`date` >= '" . date('d.m.Y H:i:s', $weekBegin) . "'";
                //$sql .= " AND a.`date` < '" . date('d.m.Y H:i:s', $weekFinish) . "'";
                $sql .= ' GROUP BY a.`itemId`';
                $sql .= ' ORDER BY a.`date`';
                //$sql .= " LIMIT 0,100";

                $res = $dbLogs->query($sql);
                $rows = $res->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $row['week'] = date('w', $row['date']);
                    $row['date'] = strtotime($row['date']);
                    $row['b_date'] = strtotime($row['b_date']);

                    $itemIDs[] = $row['itemId'];
                    $resultLogs[$row['itemId']] = $row;
                }
            }
        }

        $result = [];
        $items = $this->factoryOrders->orders->items->getListByIDs($itemIDs);

        foreach ($items as $item) {
            $row = $resultLogs[$item->itemId];
            $factHour = round(($row['b_date'] - $row['date']) / 3600);
            $deliveryHour = round(($item->delivery - $row['date']) / 3600);

            if ($deliveryHour < 0) {
                continue;
            }

            $factDay = round($factHour / 24);
            $deliveryDay = round($deliveryHour / 24);

            //if( $deliveryDay > 2 )
            //	continue;

            //var_dump( $row['itemId'] . ': ' . date('d.m.Y H:i:s', $row['date']) . ' - ' . date('d.m.Y H:i:s', $row['b_date']) . ' : ' . $factHour . ' : ' . $factDay);
            //var_dump( $row['itemId'] . ': ' . date('d.m.Y H:i:s', $row['date']) . ' - ' . date('d.m.Y H:i:s', $item->delivery ) . ' : ' . $deliveryHour . ' : ' . $deliveryDay );
            //var_dump('');

            $result[$item->priceId][$deliveryDay][] = $factHour;
        }

        $time = time();

        foreach ($result as $priceId => $data) {
            $price = $this->factoryCatalog->prices->getById($priceId);

            foreach ($data as $delivery => $factHour) {
                $factyHour = intval(round(array_sum($factHour) / count($factHour)));
                $factyDay = intval(round(round(array_sum($factHour) / count($factHour)) / 24));

                $statistic = $this->factoryOrders->statistics_delivery->getNew();
                $statistic->dateCreate = $time;
                $statistic->priceId = $price->priceId;
                $statistic->factyHour = $factyHour;
                $statistic->factyDay = $factyDay;
                $statistic->deliveryDay = intval($delivery);
                $statistic->deliveryHour = 0;
                $statistic->count = count($factHour);
                $this->factoryOrders->statistics_delivery->save($statistic);

                echo($price->title . ' : ' . $delivery . ' : ' . $factyHour . '(' . $factyDay . ')' . '; count=' . count($factHour)) . "\n";
            }
        }
    }
}
