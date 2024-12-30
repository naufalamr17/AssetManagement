<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function generate()
    {
        // Your logic to generate the letter
        return view('pages.letter.index');
    }
}
