<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'email',
        'website',
        'description',
        'owner_id',
    ];

    /**
     * Get the route key for the model (organizations are addressed by slug in URLs).
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate a unique slug when creating an organization without one.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Organization $organization) {
            if (blank($organization->slug)) {
                $base = Str::slug($organization->name);
                $slug = $base;
                $i = 1;

                while (self::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.(++$i);
                }

                $organization->slug = $slug;
            }
        });
    }

    /**
     * All members of the organization (with their role on the pivot).
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Alias kept for readability.
     */
    public function members()
    {
        return $this->users();
    }

    /**
     * The organization owner.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Projects that belong to this organization.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Tasks that belong to this organization.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Activity entries recorded for this organization.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    /**
     * Scope a query to only include organizations of the given user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('users', function (Builder $q) use ($user) {
            $q->where('users.id', $user->id);
        });
    }
}
