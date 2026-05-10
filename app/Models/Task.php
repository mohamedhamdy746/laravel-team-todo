<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use App\Models\PostImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'user_id',
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'completed',
        'due_date',
        'priority',
        'status',
        'board_column',
        'assigned_to',
        'color',
        'tags',
        'labels',
        'subtasks',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'due_date' => 'date',
            'tags' => 'array',
            'labels' => 'array',
            'subtasks' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class, 'task_id');
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
