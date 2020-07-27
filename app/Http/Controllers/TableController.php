<?php

namespace App\Http\Controllers;

use App\Table;

class TableController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        $data = Table::all();
        return response($data);
    }
}
