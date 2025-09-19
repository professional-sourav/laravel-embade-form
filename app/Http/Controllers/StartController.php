<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Dtos\FirstDto;

class StartController extends Controller
{
    public function index()
    {
        $firstDto = FirstDto::create( 'John Lark', 42 );
        
        return response()->json($firstDto);
    }
}
