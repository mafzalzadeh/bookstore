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
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    /**
     * @param Request $request
     * @return ResourceCollection
     */
    public function getCollection(Request $request): ResourceCollection
    {
        $books = Book::query();

        $this->searchTitle($request, $books);

        $this->searchAuthors($request, $books);

        $this->sorting($request, $books);

        return BookResource::collection($books->paginate());
    }

    /**
     * @param PostBookRequest $request
     * @return BookResource
     */
    public function post(PostBookRequest $request): BookResource
    {
        $book = new Book();
        $book->title =$request['title'];
        $book->isbn =$request['isbn'];
        $book->description =$request['description'];
        $book->save();

        $book->authors()->attach($request['authors']);

        return new BookResource($book);

    }

    /**
     * @param Book $book
     * @param PostBookReviewRequest $request
     * @return BookReviewResource
     */
    public function postReview(Book $book, PostBookReviewRequest $request): BookReviewResource
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
