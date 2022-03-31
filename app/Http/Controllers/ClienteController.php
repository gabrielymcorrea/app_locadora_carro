<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Repositories\ClienteRepository;

class ClienteController extends Controller{
    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
    }


    public function index(Request $request){
        $clienteRepository =  new ClienteRepository($this->cliente);
   
        //filtro
        if($request->has('filtro')){
            $clienteRepository->filtro($request->filtro); 
        }

        //has -> verficar se atribute esta sendo encaminhada no request(requisição)
        if($request->has('atributos')){
            $clienteRepository->selectAtributos($request->atributo);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }


    public function store(Request $request){
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->cliente->create([
            'nome'     => $request->nome,
        ]);
            
        return response()->json($cliente, 201); 
    }


    public function show($id){
        $cliente = $this->cliente->find($id); 

        if($cliente === null){
            return response()->json(['erro' => 'Cliente inexistente.'],404);
        }

        return response()->json($cliente, 200);
    }


    public function update(Request $request, $id){
        $cliente = $this->cliente->find($id);

        if($cliente === null){
            return response()->json(['erro' => 'Não foi possível realizar a atualização, cliente inexistente.'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach($cliente->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        }else{
            $request->validate($cliente->rules());
        }

        $cliente->fill($request->all()); 
        $cliente->save(); 

        return response()->json($cliente, 200);
    }


    public function destroy($id){
        $cliente = $this->cliente->find($id);

        if($cliente === null){
            return response()->json(['erro' => 'Não foi possível remover, cliente inexistente.'], 404);
        }

        $cliente->delete();
        return response()->json(['success' => 'Cliente removido com sucesso.'], 200);
    }
}
