<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);

Route::apiResource('books', BookController::class);

Route::apiResource('members', MemberController::class);

Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'show', 'store']);

Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook']);
Route::get('borrowings/overdue/list', [BorrowingController::class, 'overdue']);


//statistics
Route::get('statistics', function () {
    return response()->json([
        'total_books' => Book::count(),
        'total_authors' => Author::count(),
        'total_members' => Member::count(),
        'books_borrowed' => Borrowing::where('status', 'borrowed')->count(),
        'overdue_borrowings' => Borrowing::where('status', 'overdue')->count(),
    ]);
});
