<?php

return array(

    'appNameIOS'     => array(
        'environment' => 'production',
        'certificate' => base_path() . '/Certificate.pem',
        'passPhrase'  => 'amsn2001',
        'service'     => 'apns'
    ),

    'appNameAndroid' => array(
        'environment' => 'production',
        'apiKey'      => 'AIzaSyAS4E1-4Oi0hIIB2EFZQp7TgJ0F9q9Hcis',
        'service'     => 'gcm'
    )

);