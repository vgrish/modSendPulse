<?php

$settings = array();

$tmp = array(

    'api_url'       => array(
        'value' => 'https://api.sendpulse.com',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_main',
    ),
    'client_id'     => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_main',
    ),
    'client_secret' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_main',
    ),

    'addressbook_user_create' => array(
        'value' => '538999',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_addressbooks',
    ),
    'addressbook_user_pay_order'      => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_addressbooks',
    ),

    //временные
    /* 'assets_path'                => array(
         'value' => '{base_path}modsendpulse/assets/components/modsendpulse/',
         'xtype' => 'textfield',
         'area'  => 'modsendpulse_temp',
     ),
     'assets_url'                 => array(
         'value' => '/modsendpulse/assets/components/modsendpulse/',
         'xtype' => 'textfield',
         'area'  => 'modsendpulse_temp',
     ),
     'core_path'                  => array(
         'value' => '{base_path}modsendpulse/core/components/modsendpulse/',
         'xtype' => 'textfield',
         'area'  => 'modsendpulse_temp',
     )*/

);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'modsendpulse_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
