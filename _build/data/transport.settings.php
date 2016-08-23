<?php

$settings = array();

$tmp = array(


    //временные
    'assets_path' => array(
        'value' => '{base_path}modsendpulse/assets/components/modsendpulse/',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_temp',
    ),
    'assets_url'  => array(
        'value' => '/modsendpulse/assets/components/modsendpulse/',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_temp',
    ),
    'core_path'   => array(
        'value' => '{base_path}modsendpulse/core/components/modsendpulse/',
        'xtype' => 'textfield',
        'area'  => 'modsendpulse_temp',
    )


    /*
        'some_setting' => array(
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'modsendpulse_main',
        ),
        */
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
