<?php

return [
    'commands' => [
        'test' => [
            'test' => 'Отримати список усіх можливих команд.'
        ],
    ],

    'errors' => [
        'process' => 'Я ще не вмію обробляти :message_type.'
    ],

    'message_types_description' => [
        'audio' => 'аудіо файли',
        'document' => 'документи',
        'photo' => 'фотографії',
        'sticker' => 'стікери',
        'video' => 'відеофайли',
        'voice' => 'голосові повідомлення',
        'contact' => 'контактні дані',
        'location' => 'локації',

        'other' => 'дані повідомлення'
    ],

    'check_security_code' => [
        'need' => 'Для подальшої роботи необхідно ввести код доступу.',
        'success' => 'Вітаю, Колего.',
        'error' => 'Не вірний код, спробуйте ще раз, або зверніться до адміністратора.',
    ],

    'after_registration' => [
        'success' => 'Вітаю тебе :first_name :last_name! Мене звуть Базальтик, я твій вірний супутник. Обери для мене роботу:',
        'error'=> "Нажаль не існує такого відділу, але я вірю, що саме ти доб'єшся його створення!"
    ],


];
