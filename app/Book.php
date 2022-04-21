<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isbn',
        'title',
        'description',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function scopeSearchTitle($query, $title){
        $query->where('title','like','%'.$title.'%');

    }

    public function scopeSearchAuthors($query, $authors){

            $authors = explode(',', $authors);

            $query->whereHas('authors', function ($query) use ($authors) {
                $query->whereIn('author_id', $authors);
            });

    }

    public function scopeSorting($query, $sortColumn, $sortDirection)
    {
        $sortDirection = ($sortDirection && strtoupper($sortDirection) == 'DESC') ? 'DESC' : 'asc';

        if ($sortColumn == 'avg_review') {
            $query->withCount(['reviews as review_average' => function ($query) {
                $query->select(DB::raw('coalesce(avg(review),0)'));
            }])->orderBy('review_average', $sortDirection);

        } elseif ($sortColumn == 'title') {
            $query->OrderBy($sortColumn, $sortDirection);
        }
    }
}
