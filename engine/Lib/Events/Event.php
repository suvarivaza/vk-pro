<?php

abstract class Lib_Events_Event
{
    final public function Raise($params = [])
    {
        $factory = new \Service\System\Model_Handlers();

        $handlers = $this->ListHandlers();

        foreach ($handlers as $handler) {
            if (isset($handler['method']) && $handler['method'] == 'sync') {
                $class = $handler['name'];
                /** @var Lib_Events_Handler $obj */
                $obj = new $class();
                $obj->Run($params);
            } else {
                $objB = $factory->getNewHandler();
                $objB->class = $handler['name'];
                $objB->setParams($params);
                $objB->dateCreate = time();
                $factory->save($objB);
            }
        }
    }

    /**
     * Загружает список обработчиков в виде массива
     *
     * @abstract
     *
     * @return array
     */
    abstract protected function ListHandlers();
}
