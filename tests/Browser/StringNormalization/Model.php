<?php

namespace Tests\Browser\StringNormalization;

use WpStarter\Database\Eloquent\Model as BaseModel;
use Sushi\Sushi;

class Model extends BaseModel
{
    use Sushi;

    protected $rows = [
        [
            'id' => 1,
            'name' => 'â'
        ]
    ];
}
