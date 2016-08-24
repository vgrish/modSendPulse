<?php

/** @var $scriptProperties */
/** @var modsendpulse $modsendpulse */
if (!$modsendpulse = $modx->getService('modsendpulse')) {
    return;
}
$modsendpulse->initialize($modx->context->key);

/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnUserFormSave':

        $mode = $modx->getOption('mode', $scriptProperties);
        if ($mode != modSystemEvent::MODE_NEW) {
            return;
        }

        /** @var modUser $user */
        if (
            !$user = $modx->getOption('user', $scriptProperties)
            OR
            !$book = $modsendpulse->getOption('addressbook_user_register', null)
        ) {
            return;
        }

        $emails = array(
            'email'     => $user->get('email'),
            'variables' => array(
                'Имя'       => $user->get('username'),
                'Мобильный' => $user->get('mobilephone'),
                'Город'     => $user->get('city'),
            )
        );

        $modsendpulse->sendPulseAddEmailsToBook(array(
            'id'     => $book,
            'emails' => array($emails)
        ));

        break;
}