<?php

namespace NawaGrow\Contractor\Base\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

use Spatie\Translatable\HasTranslations;

class BaseAuthenticationModel extends Authenticatable
{
    use \NawaGrow\Contractor\Base\Traits\HasModel;
}
