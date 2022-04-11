<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Marca;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller{
    //Marca(modal)Injeção do modal MarcaController(Controller),
    public function __construct(Marca $marca) {
        $this->marca = $marca;
    }

    //retorna todos os dados da marca 
    public function index(Request $request){
        //$marcas = Marca::all(); // metodo estatico

        /*
        all() -> criando um obj de consulta + get() = collection
        get() -> posso modificar a consulta -> collection
        */

        $marcaRepository =  new MarcaRepository($this->marca);
        $marcas = array();

        if($request->has('atributo_modelos')){
            $atributo_modelos = 'modelos:id,'.$request->atributo_modelos;

            $marcaRepository->selectSubAtributos($atributo_modelos);
        }else{
            $marcaRepository->selectSubAtributos('modelos');
        }

        //filtro
        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro); 
        }

        //has -> verficar se atribute esta sendo encaminhada no request(requisição)
        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributo);
        }

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }
    
    //armazenar
    public function store(Request $request /*Marca $marca*/){
        //$marca = Marca::create($request->all());

        // $regras = [
        //     'nome' => 'required|unique:marcas',
        //     'imagem' =>'required'
        // ];

        // $mgs = [
        //     'nome.unique' => 'Nome já existe',
        //     'required' =>':attribute obrigatório'
        // ];

        // $request->validate($regras, $mgs);

        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens','public');

        $marca = $this->marca->create([
            'nome'      => $request->nome,
            'imagem'    => $imagem_urn
        ]);
            
        return response()->json($marca, 201); 
    }

    //mostra dados da marca escolhida
    public function show($id){ 
        $marca = $this->marca->with('modelos')->find($id); //find = encontrar

        if($marca === null){
            return response()->json(['erro' => 'marca inexistente.'],404);
        }

        return response()->json($marca, 200);
    }

    /* 
        atributo put - atualizar todos os dados
                patch - atualizar partes  (Ex somente o  nome)
    */
    public function update(Request $request, $id){
        //$marca->update($request->all()); 

        $marca = $this->marca->find($id);
    
        if($marca === null){
            return response()->json(['erro' => 'Não foi possível realizar a atualização, marca inexistente.'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no model
            foreach($marca->rules() as $input => $regra){
                //coletar apenas as regras aplicaveis aos parametros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
 
            $request->validate($regrasDinamicas, $marca->feedback());
        }else{
            $request->validate($marca->rules(), $marca->feedback()); //esse marca e da marca que foi recuperado do id.
        }

        // fill espera um array, ele vai pegar o array e sobrescrever os atributos do objeto
        $marca->fill($request->all()); //fill significa "preencher".preenche as informações do seu Model.

        //remove o arquivo antigo 
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens','public');
    
            $marca->imagem = $imagem_urn; // sobrescrevendo o atributo imagem
        }

    
        $marca->save(); // o save faz update caso tenha um id presente se não tiver ele cria

        // $marca->update([
        //     'nome'      => $request->nome,
        //     'imagem'    => $imagem_urn
        // ]);

        return response()->json($marca, 200);
    }
      

    public function destroy($id){
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Não foi possível remover, marca inexistente.'], 404);
        }

        //remove o arquivo antigo 
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['success' => 'A marca foi removida com sucesso.'], 200);
    }
}