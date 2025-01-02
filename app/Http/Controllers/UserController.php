<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{

    //Admin Interface Methods
    public function getBookList()
    {

        $borrowed = DB::table("books")
            ->leftJoin("borrow_records", "books.id", "=", "borrow_records.book_id")
            ->leftJoin("users", "borrow_records.user_id", "=", "users.id")
            ->select("books.id", "books.title", "books.description", "books.status", "users.name")
            ->get();
        // echo "<pre>";
        // print_r($borrowed);
        // die;
        return view("welcome", [
            "borrowed" => $borrowed
        ]);
    }

    //Api Methods

    //Api for getting all books
    public function getAllBook(Request $request)
    {
        $title = $request->title;
        // return $bookName;
        // die;
        if (!$title) {
            $books = DB::table("books")->get();
            return response()->json(
                [
                    "status" => "true",
                    "message" => "Books fetched successfully",
                    "books" => $books
                ]
            );
        } else {
            $books = DB::table("books")->where("title", "like", "%$title%")->get();
            return response()->json(
                [
                    "status" => "true",
                    "message" => "Books fetched successfully",
                    "books" => $books,
                    "Available or Borrowed" => $books[0]->status
                ]
            );
        }
    }

    //Api for getting single book
    public function show(Request $request)
    {
        $id = $request->id;
        $book = DB::table("books")->where("id", $id)->first();
        if (!empty($book)) {
            return response()->json([
                "status" => "true",
                "message" => "Book fetched successfully",
                "Book Detail" => $book,
            ], 200);
        } else {
            return response()->json([
                "status" => "faild",
                "message" => "Book not found",
            ], 404);
        }
    }


    //serch book by name
    public function showBookByName(Request $request)
    {
        $name = $request->name;
        $title = DB::table("books")->where("title", $name)->first();
        // return $title;
        // die;
        if (!$title) {
            return response()->json("Book Not Found", 404);
        } else {
            return response()->json([
                "status" => "OK",
                "message" => "success",
                "Book Details" => $title,
            ], 200);
        }
    }



    //Api for borrow the book
    public function borrowBook(Request $request)
    {

        $book_id = $request->input("book_id");
        // return $book_id;
        // die;
        // Fetch the borrow record for the given book_id
        $bookId = DB::table("borrow_records")->where("book_id", $book_id)->get("id");

        $userId = $request->user_id;

        $userName = DB::table("users")->where("id", $userId)->select("name")->first();
        $name = $userName->name;
        // return $name;
        // die;


        $totalBorrowed = DB::table("borrow_records")
            ->where("user_id", $userId)
            ->count("user_id");
        // return $totalBorrowed;
        // die;

        if ($totalBorrowed < 3) {

            if ($bookId->isEmpty()) {

                $borrow = DB::table("borrow_records")->insert([
                    //return response()->json([
                    "user_id" => $request->user_id,
                    "book_id" => $book_id,
                    "borrow_date" => $request->borrow_date,
                    "return_date" => $request->return_date,
                    "status" => $request->status,
                ]);

                $updateUserStatus =  DB::table("books")->where("id", $book_id)->update(["status" => "borrowed"]);
                // return $updateUserStatus;

                if ($borrow) {
                    return response()->json([
                        "status" => "success",
                        "message" => "book is borrowed successfully",
                    ], 200);
                } else {
                    return response()->json([
                        "status" => "faild",
                        "message" => "book is borrowed faild",
                    ]);
                }
            } else {
                return response()->json([
                    "status" => "faild",
                    "message" => "book is already borrowed",
                ]);
            }
        } else {
            return response()->json([
                "status" => "faild",
                "message" => "You can only borrowed 3 books",
            ]);
        }
    }


    public function borrowedBook()
    {
        $borrowed = DB::table("borrow_records")
            ->join("users", "borrow_records.user_id", "=", "users.id")
            ->join("books", "borrow_records.book_id", "=", "books.id")
            ->where("borrow_records.status", "borrowed")
            ->select("users.name", "books.title") // Select specific columns
            ->get();
        return response()->json([
            "status" => "true",
            "message" => "Success",
            "borrowed" => $borrowed,
        ]);
    }

    //api for filter
    public function showAvailableBooks(Request $request)
    {
        $value = $request->filter;
        // return $value;
        // die;
        if ($value == "available") {
            $available = DB::table("books")->where("status", "available")->get();
            return response()->json([
                "status" => "true",
                "message" => "Success",
                "borrowed" => $available,
            ]);
        } elseif ($value == "borrowed") {
            $borrowed = DB::table("borrow_records")
                ->rightJoin("books", "borrow_records.book_id", "=", "books.id")
                ->leftJoin("users", "borrow_records.user_id", "=", "users.id")
                ->where('borrow_records.status', 'borrowed')
                ->select("books.title", "books.description", "books.status", "users.name")
                ->get();
            return response()->json([
                "status" => "true",
                "message" => "Success",
                "borrowed" => $borrowed
            ]);
        } elseif ($value == "returned") {
            $returned = DB::table("borrow_records")
                ->rightJoin("books", "borrow_records.book_id", "=", "books.id")
                ->leftJoin("users", "borrow_records.user_id", "=", "users.id")
                ->where('borrow_records.status', 'returned')
                ->select("books.title", "books.description", "borrow_records.status", "users.name")
                ->get();
            return response()->json([
                "status" => "true",
                "message" => "Success",
                "borrowed" => $returned
            ]);
        } elseif ($value == "all") {
            $all = DB::table("books")
                ->leftJoin("borrow_records", "books.id", "=", "borrow_records.book_id")
                ->leftJoin("users", "borrow_records.user_id", "=", "users.id")
                ->select("books.id", "books.title", "books.description", "books.status", "users.name")
                ->get();
            return response()->json([
                "status" => "true",
                "message" => "Success",
                "borrowed" => $all
            ]);
        }
    }

    public function returnBook(Request $request)
    {
        $book_id = $request->book_id;
        $user_id = $request->user_id;
        $return_date = $request->return_date;

        if (!empty($book_id) && !empty($user_id) && !empty($return_date)) {
            $deleted = DB::table("borrow_records")
                ->where("book_id", $book_id)
                ->where("user_id", $user_id)
                ->update([
                    "status" => "returned",
                    "return_date" => $return_date
                ]);
            $returend = DB::table("books")
                ->where("id", $book_id)
                ->update([
                    "status" => "available",
                ]);
            if ($returend) {
                return response()->json([
                    "status" => "true",
                    "message" => "Book returned successfully"
                ]);
            } else {
                return response()->json([
                    "status" => "faild",
                    "message" => "Book returned faild"
                ]);
            }
        } else {
            return response()->json([
                "status" => "faild",
                "message" => "fill all fields"
            ]);
        }
    }
}
