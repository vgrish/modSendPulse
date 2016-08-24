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
            !$items = $order->getMany('Products')
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

        /** @var msOrderProduct $item */
        foreach ($items as $item) {
            /** @var msProduct $product */
            if (!$product = $item->getOne('Product')) {
                continue;
            }

            $book = $modsendpulse->sendPulseGetAddressBookIdFromName(array('name' => $product->get('pagetitle')), true);

            switch (true) {
                case $book AND $status == 0:
                case $book AND $status == 2:
                    $modsendpulse->sendPulseAddEmailsToBook(array(
                        'id'     => $book,
                        'emails' => array($emails)
                    ));
                    break;
                case $book AND $status == 4:
                    $modsendpulse->sendPulseRemoveEmailsFromBook(array(
                        'id'     => $book,
                        'emails' => array($emails['email'])
                    ));
                    break;
                default:
                    break;
            }
        }

        break;
}