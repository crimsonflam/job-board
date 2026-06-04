<?php

/*
|--------------------------------------------------------------------------
| Morocco-specific reference data (MOD 2 & 14)
|--------------------------------------------------------------------------
| WHY: The job board is focused on the Moroccan market. Locations must be
|      chosen from a fixed list of Moroccan cities (a dropdown, never free
|      text) so data stays clean and filtering is reliable. Centralizing the
|      list here means every form/filter/validator references one source of
|      truth — add a city once and it appears everywhere.
|
| HOW:  Read with config('morocco.cities'). The list is kept alphabetical so
|       it can be rendered directly in <select> options.
*/

return [

    // All major Moroccan cities, alphabetical. Used in:
    //  - Employer job creation/edit (location dropdown)
    //  - Browse Jobs location filter
    //  - Employer company profile location
    'cities' => [
        'Agadir',
        'Al Hoceima',
        'Azilal',
        'Ben Guerir',
        'Beni Mellal',
        'Casablanca',
        'Dakhla',
        'Errachidia',
        'Essaouira',
        'Fes',
        'Guelmim',
        'Ifrane',
        'Kelaat Sraghna',
        'Kenitra',
        'Khemisset',
        'Khouribga',
        'Laayoune',
        'Larache',
        'Marrakech',
        'Meknes',
        'Midelt',
        'Nador',
        'Ouarzazate',
        'Oujda',
        'Ouezzane',
        'Rabat',
        'Safi',
        'Sale',
        'Settat',
        'Sidi Ifni',
        'Sidi Slimane',
        'Tafraout',
        'Tamanart',
        'Tamegroute',
        'Tangier',
        'Taourirt',
        'Taroudant',
        'Taza',
        'Tetouan',
        'Tinghir',
        'Zagora',
    ],

];
