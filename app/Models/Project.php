<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * Note: organization_id is never trusted from the frontend; it is set
     * server-side from the current organization context.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'created_by',
        'name',
        'description',
        'status',
    ];

    /**
     * The organization this project belongs to.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Tasks of this project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The user who created the project.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Count of open (not done) tasks.
     */
    public function openTasksCount(): int
    {
        return $this->tasks()->where('status', '!=', 'done')->count();
    }
}
