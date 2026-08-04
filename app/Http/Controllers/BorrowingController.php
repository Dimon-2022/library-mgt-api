<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['book', 'member']);

        if($request->has('status')){
            $query->where('status', $request->status);
        }

        if($request->has('member_id')){
            $query->where('member_id', $request->member_id);
        }

        $borrowings = $query->latest()->paginate(10);

        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        if(!$book->isAvailable()){
            return response()->json([
                'message' => 'Book is not available'
            ], 422);
        }

        $borrowing = Borrowing::create($request->validated());

        $book->borrow();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function returnBook(Request $request, Borrowing $borrowing){
        if($borrowing->status !== 'borrowed'){
            return response()->json([
                'message' => 'Book has already been borrowed'
            ], 422);
        }

        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned'
        ]);

        $borrowing->book->returnBook();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function overdue(){
        $overdueBorrowings = Borrowing::with(['book', 'member'])->where('status', 'borrowed')->where('due_date', '<', now())->get();

        Borrowing::where('status', 'borrowed')->where('due_date', '<', now())->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}
