<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller{

    public function __construct(Modelo $modelo) {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $modeloRepository =  new ModeloRepository($this->modelo);
        
        if($request->has('atributo_marca')){
            $atributo_marcas = 'marcas:id,'.$request->atributo_marca;

            $modeloRepository->selectSubAtributos($atributo_marcas);
        }else{
            $modeloRepository->selectSubAtributos('marca');
        }

        //filtro
        if($request->has('filtro')){
            $modeloRepository->filtro($request->filtro); 
        }

        //has -> verficar se atribute esta sendo encaminhada no request(requisição)
        if($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributo);
        }

        return response()->json($modeloRepository->getResultado(), 200);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public');

        $modelo = $this->modelo->create([
            'marca_id'      => $request->marca_id,
            'nome'          => $request->nome,
            'imagem'        => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares'       => $request->lugares,
            'air_bag'       => $request->air_bag,
            'abs'           => $request->abs
        ]);
            
        return response()->json($modelo, 201); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id); //find = encontrar

        if($modelo === null){
            return response()->json(['erro' => 'modelo inexistente.'],404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id){
        $modelo = $this->modelo->find($id);
        
        if($modelo === null){
            return response()->json(['erro' => 'Não foi possível realizar a atualização, modelo inexistente.'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no model
            foreach($modelo->rules() as $input => $regra){
                //coletar apenas as regras aplicaveis aos parametros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        }else{
            $request->validate($modelo->rules()); 
        }

        $modelo->fill($request->all()); 

        //remove o arquivo antigo 
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos','public');

            $modelo->imagem = $imagem_urn; 
        }

        $modelo->save();

        // $modelo->update([
        //     'marca_id'      => $request->marca_id,
        //     'nome'          => $request->nome,
        //     'imagem'        => $imagem_urn,
        //     'numero_portas' => $request->numero_portas,
        //     'lugares'       => $request->lugares,
        //     'air_bag'       => $request->air_bag,
        //     'abs'           => $request->abs
        // ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Não foi possível remover, modelo inexistente.'], 404);
        }

        //remove o arquivo antigo 
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['success' => 'O modelo foi removido com sucesso.'], 200);
    }
}
