<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use Illuminate\Http\Request;
use App\Repositories\CarroRepository;

class CarroController extends Controller{
    public function __construct(Carro $carro) {
        $this->carro = $carro;
    }

 
    public function index(Request $request){
        $carroRepository =  new CarroRepository($this->carro);
   
        if($request->has('atributo_modelo')){
            $atributo_modelo = 'modelo:id,'.$request->atributo_modelo;

            $carroRepository->selectSubAtributos($atributo_modelo);
        }else{
            $carroRepository->selectSubAtributos('modelo');
        }


        //filtro
        if($request->has('filtro')){
            $carroRepository->filtro($request->filtro); 
        }

        //has -> verficar se atribute esta sendo encaminhada no request(requisição)
        if($request->has('atributos')){
            $carroRepository->selectAtributos($request->atributo);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request){
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = $this->carro->create([
            'modelo_id'     => $request->modelo_id,
            'placa'         => $request->placa,
            'disponivel'    => $request->disponivel,
            'km'            => $request->km
        ]);
            
        return response()->json($carro, 201); 
    }

   
    public function show($id){
        $carro = $this->carro->with('modelo')->find($id); //find = encontrar

        if($carro === null){
            return response()->json(['erro' => 'carro inexistente.'],404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function edit(Carro $carro)
    {
        //
    }


    public function update(Request $request, $id){
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Não foi possível realizar a atualização, carro inexistente.'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach($carro->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        }else{
            $request->validate($carro->rules());
        }

        $carro->fill($request->all()); 
        $carro->save(); 

        return response()->json($carro, 200);
    }


    public function destroy($id){
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Não foi possível remover, carro inexistente.'], 404);
        }

        $carro->delete();
        return response()->json(['success' => 'Carro removido com sucesso.'], 200);
    }
}
