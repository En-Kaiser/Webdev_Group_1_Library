<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function monitorUsers()
    {
        // Logic
        return view('dashboard.librarian.monitor_users');
    }

    public function transactions()
    {
        $pendingRequestsData = DB::table('history')
            ->join('user_accounts', 'history.user_id', '=', 'user_accounts.user_id')
            ->join('books', 'history.book_id', '=', 'books.book_id')
            ->leftJoin('book_type_avail', function ($join) {
                $join->on('history.book_id', '=', 'book_type_avail.book_id')
                    ->on('history.type', '=', 'book_type_avail.type');
            })
            ->where('history.type', 'physical') 
            ->whereNull('history.date_return','=',null)
            ->select(
                'history.history_id as id',
                'history.type',
                'book_type_avail.availability',
                'user_accounts.first_name',
                'user_accounts.last_name',
                'books.title as book_title'
            )
            ->get();

       
        $pendingRequests = $pendingRequestsData->map(function ($item) {
            return (object) [
                'id'     => $item->id,
                'type'   => 'Physical',
                'status' => 'Available',
                'user'   => (object) [
                    'first_name' => $item->first_name,
                    'last_name'  => $item->last_name
                ],
                'book'   => (object) [
                    'title' => $item->book_title
                ]
            ];
        });

        $completedData = DB::table('history')
            ->join('user_accounts', 'history.user_id', '=', 'user_accounts.user_id')
            ->join('books', 'history.book_id', '=', 'books.book_id')
            ->select(
                'user_accounts.first_name',
                'user_accounts.last_name',
                'books.title as book_title',
                'history.type',
                'history.date_borrowed',
                'history.date_return',
                'history.status'
            )
            ->orderBy('history.date_borrowed', 'desc')
            ->get();

      
        $completedTransactions = $completedData->map(function ($item) {

            $borrowDate = $item->date_borrowed ? Carbon::parse($item->date_borrowed) : null;
          
            $returnDate = $item->date_return ? Carbon::parse($item->date_return) : null;

            return (object) [
                'user_name'   => $item->first_name . ' ' . $item->last_name,
                'book_title'  => $item->book_title,
                'type'        => $item->type == 'physical' ? 'Physical' : 'E-Book',
                'borrow_date' => $borrowDate ? $borrowDate->format('m-d-Y') : '-',
                'due_date'    => '-',
                'return_date' => $returnDate ? $returnDate->format('m-d-Y') : null, 
                'status'      => ucfirst($item->status) 
            ];
        });

        return view('dashboard.librarian.transactions', compact('pendingRequests', 'completedTransactions'));
    }

    public function approve($id)
{
    $request = DB::table('history')
        ->where('history_id', $id)
        ->whereNull('date_return')
        ->first();

    if (!$request) {
        return redirect()->back()->with('error', 'Request not found or already processed.');
    }

    
    DB::table('history')
        ->where('history_id', $id)
        ->update([
            'date_return' => Carbon::now()->addDays(7)
        ]);

    return redirect()->back()->with('success', 'Request approved! Time started.');
}

    public function reject($id)
    {
        $request = DB::table('history')->where('history_id', $id)->first();

        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        
        DB::table('history')->where('history_id', $id)->delete();

        DB::table('book_type_avail')
            ->where('book_id', $request->book_id)
            ->where('type', $request->type)
            ->update(['availability' => 'available']);

        return redirect()->back()->with('success', 'Request rejected and removed.');
    }
}
