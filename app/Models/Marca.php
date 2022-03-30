<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome','imagem'];

    /* 
        Parametros do unique 
        1 -> tabela 
        2 -> nome da coluna que sera pesquisado na tabela
        3 -> id do registro que sera desconsiderado na pesquisa
    */

    //regras
    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'',
            'imagem' =>'required|file|mimes:png'
        ];
    }

    //mgs
    public function feedback(){
        return [
            'nome.unique' => 'Nome já existe',
            'required' =>':attribute obrigatório',
            'imagem.mimes' => 'O arquivo tem que ser uma imagem do tipo PNG'
        ];
    }

    public function modelos(){
        //uma marca POSSUI MUITOS modelos

        return $this->hasMany('App\Models\Modelo');
    }
}
