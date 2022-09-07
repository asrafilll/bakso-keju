<?php

namespace App\Models;

use App\Traits\HasUuid;

class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    use HasUuid;
}
