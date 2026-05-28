<?php

class Lib_Graph
{
    private $_periods = [];

    private $_paths = [];

    public function init($routes)
    {
        $paths = [];

        /** @var \Service\Logistic\Model_Routes_Route $route */
        foreach ($routes as $route) {
            if (!isset($paths[$route->directionFrom])) {
                $paths[$route->directionFrom] = [];
            }
            $paths[$route->directionFrom][] = $route->directionTo;
            $this->_periods[$route->directionFrom . $route->directionTo] = $route->period;
        }

        $this->_paths = $paths;
    }

    private function generate($path, $goal, &$solns)
    {
        // изменить конечную точку
        $state = $path[count($path) - 1];

        if ($state == $goal) {
            // нашли путь, сохраним его
            array_push($solns, $path);
        } else {
            // проверяем все дуги
            if (isset($this->_paths[$state])) {
                foreach ($this->_paths[$state] as $arc) {
                    // исключаем циклы
                    if (!in_array($arc, $path)) {
                        $this->generate(array_merge($path, (array) $arc), $goal, $solns);
                    }
                }
            }
        }
    }

    private function cmp($a, $b)
    {
        if (count($a) == count($b)) {
            return 0;
        }

        return (count($a) < count($b)) ? -1 : 1;
    }

    // поиск на графе

    public function search($start, $goal)
    {
        $sols = [];

        $this->generate([$start], $goal, $sols);

        usort($sols, [$this, 'cmp']);

        $result = [];

        foreach ($sols as $sol) {
            $period = 0;
            $lastPoint = false;

            foreach ($sol as $point) {
                if ($lastPoint) {
                    $period += $this->_periods[$lastPoint . $point];
                }
                $lastPoint = $point;
            }
            $result[] = [
                'points' => $sol,
                'period' => $period,
            ];
        }

        return $result;
    }
}
