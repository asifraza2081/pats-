<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Result;

class ResultController
{
    public function index(Request $request, array $params = []): void
    {
        $query = $request->get('q');
        $result = null;

        if ($query) {
            $result = Result::search(trim($query));
            if (!$result) {
                \App\Core\Session::flash('error', 'No result found. Make sure the roll number or CNIC is correct and the result has been officially announced.');
            }
        }

        View::render('results/index', [
            'pageTitle' => 'Check Result',
            'query'     => $query,
            'result'    => $result
        ]);
    }
}
