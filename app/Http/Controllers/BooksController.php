<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function getCollection(Request $request)
    {
        $books = Book::query();

        $this->searchTitle($request, $books);

        $this->searchAuthors($request, $books);

        if (isset($request['sortColumn'])) {

            $sortDirection = ( isset($request['sortDirection'] ) && strtoupper( $request['sortDirection'] ) == 'DESC' ) ? 'DESC': 'asc';
            if ($request['sortColumn'] == 'title'){
                $books->OrderBy( $request['sortColumn'], $sortDirection );
            }

        }

        return BookResource::collection($books->paginate());
    }

    public function post(PostBookRequest $request)
    {
        //@todo code here
    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        //@todo code here
    }

    /**
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Builder $books
     */
    protected function searchTitle(Request $request, \Illuminate\Database\Eloquent\Builder $books): void
    {
        if ($request->filled('title')) {
            $books->searchTitle($request['title']);
        }
    }

    /**
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Builder $books
     */
    protected function searchAuthors(Request $request, \Illuminate\Database\Eloquent\Builder $books): void
    {
        if ($request->filled('authors')) {
            $books->searchAuthors($request['authors']);
        }
    }
}
