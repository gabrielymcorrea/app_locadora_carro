<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;

class MarcaController extends Controller
{
    //retorna todos os dados da marca 
    public function index(){
        $marcas = Marca::all();
        return $marcas;
    }
    
    //armazenar
    public function store(Request $request){
        $marca = Marca::create($request->all());
        
        return $marca; // laravel já converte para json 
    }

    //mostrar o dado escolhido - Marca(modal)
    public function show(Marca $marca){
        return $marca;
    }
}
