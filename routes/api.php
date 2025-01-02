<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get("/books", [UserController::class, "getAllBook"]); // get all books

Route::get("/booksById", [UserController::class, "show"]); // get single book

Route::get("/book", [UserController::class, "showBookByName"]); // get booke by name

Route::post("/borrow", [UserController::class, "borrowBook"]); // borrow the book

Route::get("/borrowedbooks", [UserController::class, "borrowedBook"]); // borrowed the bookList

//Book filter api
Route::get("/books/available", [UserController::class, "showAvailableBooks"]); // Available books

Route::post("/books/returnbook", [UserController::class, "returnBook"]); // Available books
