<?php
$fileName = $vars['fileName'];
$rows = $vars['rows'];
$countRows = count($rows) - 1;

?>
<style>
    .overflow {
        height: 500px; /* высота нашего блока */
        width: 100%; /* ширина нашего блока */
        background: #fff; /* цвет фона, белый */
        border: 1px solid #C1C1C1; /* размер и цвет границы блока */
        overflow-x: scroll; /* прокрутка по горизонтали */
        overflow-y: scroll; /* прокрутка по вертикали */
    }
</style>

<p><h2>Логи cron заданий. Файл <?= $fileName ?> </h2> (последние <?= $countRows ?> строк.)</p>
<div class="overflow container">
<?php foreach ($rows as $row) {
    $m = false;
    if (mb_stripos($row, 'action=') !== false) $m = true;
    if ($m)
        echo "<br><b>{$row}</b>";
    else
        echo "<br>{$row}";
}
?>
</div>



