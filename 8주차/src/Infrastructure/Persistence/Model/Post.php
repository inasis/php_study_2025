<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $author_id 
 * @property \DateTimeInterface $created_at
 * @property \DateTimeInterface $updated_at
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'title',
        'content',
        'author_id'
    ];
    public $timestamps = true;
}