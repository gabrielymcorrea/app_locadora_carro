<?php 

namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;


abstract class AbsttractRepository{
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectSubAtributos($atributos) {
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtros){
        $filtros = explode(';',$filtros);

        foreach($filtros as $condicoes){
            $condicao = explode(':',$condicoes);

            //a query está sendo montada
            $this->model = $this->model->where($condicao[0], $condicao[1], $condicao[2]);
        } 
    }
    
    public function selectAtributos($atributos){
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado(){
        return  $this->model->get();
    }

    public function getResultadoPaginado($numRegistro){
        return  $this->model->paginate($numRegistro);
    }
}