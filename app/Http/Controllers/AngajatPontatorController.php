<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AngajatPontatorController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('angajati.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $angajat = Angajat::create($this->validateRequest($request));

        return redirect('/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost adăugat cu succes!');
    }
}
