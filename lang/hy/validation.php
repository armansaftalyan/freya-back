<?php

declare(strict_types=1);

return [
    'required' => ':attribute դաշտը պարտադիր է։',
    'string' => ':attribute դաշտը պետք է լինի տեքստ։',
    'integer' => ':attribute դաշտը պետք է լինի ամբողջ թիվ։',
    'email' => ':attribute դաշտը պետք է լինի ճիշտ email հասցե։',
    'date' => ':attribute դաշտը պետք է լինի ճիշտ ամսաթիվ։',
    'date_format' => ':attribute դաշտը պետք է համապատասխանի :format ձևաչափին։',
    'after' => ':attribute դաշտը պետք է լինի :date-ից հետո։',
    'exists' => 'Ընտրված :attribute արժեքը անվավեր է։',
    'unique' => ':attribute արժեքն արդեն օգտագործվում է։',
    'in' => 'Ընտրված :attribute արժեքը անվավեր է։',
    'min' => [
        'string' => ':attribute դաշտը պետք է լինի առնվազն :min նիշ։',
    ],
    'max' => [
        'string' => ':attribute դաշտը չի կարող լինել :max նիշից երկար։',
    ],
    'attributes' => [
        'name' => 'անուն',
        'email' => 'email',
        'phone' => 'հեռախոս',
        'password' => 'գաղտնաբառ',
        'master_id' => 'վարպետ',
        'branch_id' => 'մասնաճյուղ',
        'service_id' => 'ծառայություն',
        'start_at' => 'մեկնարկի ժամ',
        'comment' => 'մեկնաբանություն',
        'source' => 'աղբյուր',
        'date' => 'ամսաթիվ',
    ],
];
