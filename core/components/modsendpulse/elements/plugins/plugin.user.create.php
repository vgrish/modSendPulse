<?php

/** @var $scriptProperties */
/** @var modsendpulse $modsendpulse */
if (!$modsendpulse = $modx->getService('modsendpulse')) {
    return;
}
$modsendpulse->initialize($modx->context->key);

/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnUserSave':

        $mode = $modx->getOption('mode', $scriptProperties);
        if ($mode != modSystemEvent::MODE_NEW) {
            return;
        }

        /** @var modUser $user */
        if (
            !$user = $modx->getOption('user', $scriptProperties)
            OR
            !$profile = $user->getOne('Profile')
            OR
            !$book = $modsendpulse->getOption('addressbook_user_create', null)
        ) {
            return;
        }

        $emails = array(
            'email'     => $profile->get('email'),
            'variables' => array(
                'Имя'       => $user->get('username'),
                'Мобильный' => $profile->get('mobilephone'),
                'Город'     => $profile->get('city'),
            )
        );

        $modsendpulse->sendPulseAddEmailsToBook(array(
            'id'     => $book,
            'emails' => array($emails)
        ));
        
        break;
}