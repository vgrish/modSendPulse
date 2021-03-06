<?php

/** @var $scriptProperties */
/** @var modsendpulse $modsendpulse */
if (!$modsendpulse = $modx->getService('modsendpulse')) {
    return;
}
$modsendpulse->initialize($modx->context->key);

/** @var modX $modx */
switch ($modx->event->name) {

    case 'msOnChangeOrderStatus':

        $status = $modx->getOption('status', $scriptProperties);
        if ($status != 2) {
            return;
        }

        /** @var msOrder $order */
        /** @var modUser $user */
        /** @var modUserProfile $profile */
        if (
            !$order = $modx->getOption('order', $scriptProperties)
            OR
            !$user = $order->getOne('User')
            OR
            !$profile = $order->getOne('UserProfile')
            OR
            !$book = $modsendpulse->getOption('addressbook_user_pay_order', null)
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