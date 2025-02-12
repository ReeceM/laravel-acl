<?php

namespace Junges\ACL\Tests;

use Illuminate\Database\Eloquent\Model;
use Junges\ACL\Events\PermissionSaving;
use Junges\ACL\Traits\PermissionsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes, PermissionsTrait;

    protected $table = 'test_permissions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'description', 'slug',
    ];

    protected $dispatchesEvents = [
        'creating' => PermissionSaving::class,
    ];
}
