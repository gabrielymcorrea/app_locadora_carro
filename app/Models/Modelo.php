<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id','nome','imagem','numero_portas','lugares','air_bag','abs'];

    public function rules(){
        return [
            'marca_id' => 'exists:marcas,id', /*Se id existe na tabela marcas*/
            'nome' => 'required|unique:modelos,nome,'.$this->id.'',
            'imagem' =>'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5', /*Tem que ser um numeor inteiro entre 1 á 5*/
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }

    public function feedback(){
        return [
            'nome.unique' => 'Nome já existe',
        ];
    }

    public function marca(){
        //Um modelo pertence a uma marca

        return $this->belongsTo('App\Models\Marca'); //belongsTo -> pertence a 
    }
}
