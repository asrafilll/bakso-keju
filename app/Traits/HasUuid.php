<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasUuid
{
    /**
     * @return void
     */
    public static function bootHasUuid()
    {
        static::creating(fn (Model $model) => $model->{$model->getKeyName()} = Uuid::uuid4()->toString());
    }

    /**
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
