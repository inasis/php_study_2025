<?php
declare(strict_types=1);

namespace Ginger\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'title',
        'content',
    ];
    public $timestamps = true;
}
