<?

use YooKassa\Client;
$order = $_POST["order"];
$sum = $_POST["sum"];
$phone = $_POST["phone"];
$email = $_POST["email"];

var_dump($_POST); die;
?>
<?if(!empty($order) && !empty($sum) && !empty($phone) && !empty($email)) {
    $description = 'Заказ № '.$order.' Тел.: '.$phone.' E-mail: '.$email;
    $client = new Client();
    $client->setAuth('<Идентификатор магазина>', '<Секретный ключ>');
    $payment = $client->createPayment(
        array(
            'amount' => array(
                'value' => $sum,
                'currency' => 'RUB',
            ),
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => 'https://it-blog.ru/',
            ),
            'capture' => true,
            'description' => $description,
        ),
        uniqid('', true)
    );
    header('Location: ' . $payment["confirmation"]["confirmation_url"]);
    ?>
    <p>Сейчас вы будете перенаправлены на страницу оплаты, если этого не произошло нажмите на ссылку ниже:</p>
    <p><a href="<?=$payment["confirmation"]["confirmation_url"];?>">Оплатить</a></p>
<?} else {?>
    <p>Произошла ошибка. Попробуйте еще раз.</p>
<?}?>