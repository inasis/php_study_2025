<?php
declare(strict_types=1);

namespace Ginger\Entity;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content'];
    public $timestamps = true;
}
