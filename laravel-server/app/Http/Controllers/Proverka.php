<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Proverka extends Controller
{
    public function proverka(Request $request)
    {
	$envKey = "MySecretKey123";
	$requestKey = "mysecretkey123";

	if ($envKey != $requestKey) {
    	    echo "super";
	} else {
    	    echo "huevo";
	}
    }
}
