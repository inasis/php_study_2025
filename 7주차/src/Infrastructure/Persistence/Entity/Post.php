<?php
declare(strict_types=1);

namespace Ginger\Infrastructure\Persistence\Entity;

use Illuminate\Database\Eloquent\Model;
use Ginger\Entity\PostInterface;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
class Post extends Model implements PostInterface
{
    protected $table = 'posts';
    protected $fillable = [
        'title',
        'content',
    ];
    public $timestamps = true;
}