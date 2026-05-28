<?php
/** @var Model_Users_User $user */
$user = $vars['user'];
/** @var Model_Questions_Question $question */
$question = $vars['question'];

use Service\Faq\Model_Questions_Question;
use Service\Users\Model_Users_User;

?>
<h3>Добрый день, <?= $user->firstName; ?>!</h3>
<div><?= $question->answer; ?></div>
<br/><br/>
<p>Ваш вопрос:</p>
<div><?= $question->question; ?></div>