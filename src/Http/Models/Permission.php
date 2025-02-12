<?php

namespace Junges\ACL\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Junges\ACL\Events\PermissionSaving;
use Junges\ACL\Traits\PermissionsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes, PermissionsTrait;

    protected $table;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'description', 'slug',
    ];

    protected $dispatchesEvents = [
        'creating' => PermissionSaving::class,
    ];

    /**
     * Permission constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('acl.tables.permissions'));
    }
}
