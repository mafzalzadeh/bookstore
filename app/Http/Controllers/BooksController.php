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

        $this->sorting($request, $books);

        return BookResource::collection($books->paginate());
    }

    public function post(PostBookRequest $request)
    {
        $book = new Book();
        $book->title =$request['title'];
        $book->isbn =$request['isbn'];
        $book->description =$request['description'];
        $book->save();

        $book->authors()->attach($request['authors']);

        return new BookResource($book);

    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        $bookReview = new BookReview();
        $bookReview->review = $request['review'];
        $bookReview->comment = $request['comment'];

        $bookReview->user_id = Auth::id();
        $bookReview->book_id = $book->id;

        $bookReview->save();;

        return new BookReviewResource($bookReview);
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

    /**
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Builder $books
     */
    protected function sorting(Request $request, \Illuminate\Database\Eloquent\Builder $books): void
    {
        if (isset($request['sortColumn'])) {
            $books->sorting($request['sortColumn'], $request['sortDirection']);
        }
    }
}
