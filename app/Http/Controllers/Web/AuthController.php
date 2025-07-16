<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index($mode)
    {
        $mode = strtolower($mode);
        switch ($mode) {
            case 'login':
                $mode = 'login';
                break;
            case 'registrar':
                $mode = 'register';
                break;
            default:
                $mode = 'register';
        }
        return view('web.auth.index', compact('mode'));
    }
}
