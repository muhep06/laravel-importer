<?php


namespace AcikVeri\Importer\Models;


use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class DynamicModel extends Model
{
    use BindsDynamically;

    static $useUuid = false;

    public static function boot()
    {
        parent::boot();
            self::creating(function ($model) {
                if (self::$useUuid) {
                    $model->uuid = (string) Uuid::generate(4);
                }
            });
    }

    public function setUseUuid(bool $use) {
        self::$useUuid = $use;
    }
}
