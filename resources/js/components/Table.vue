<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="t, key in titulos" :key="key">{{t.titulo}}</th>
                    <th v-if="visualizar.visivel || atualizar || remover">Ação</th>
                </tr>
            </thead> <!-- é chamado assim dadosFiltrados e nao assim dadosFiltrados() pq é computed -->
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'texto'">{{valor}}</span>
                        <span v-if="titulos[chaveValor].tipo == 'data'">
                            {{ format_date(valor)}}
                        </span>
                        <span v-if="titulos[chaveValor].tipo == 'imagem'">
                            <img :src="'/storage/'+valor" width="30" height="30">
                        </span>
                    </td>

                    <td v-if="visualizar.visivel || atualizar || remover"> 
                        <button v-if="visualizar.visivel" class="btn btn-outline-info btn-sm" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget"> <img src="img/view.png" height="15" width="15"/></button>
                        <button v-if="atualizar" class="btn btn-outline-warning btn-sm"> <img src="img/edit.png" height="15" width="15"/></button>
                        <button v-if="remover" class="btn btn-outline-danger btn-sm"> <img src="img/delete.png" height="15" width="15"/></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import moment from 'moment';
    export default {
        props: ['dados', 'titulos','visualizar','remover','atualizar'],
        computed: {
            dadosFiltrados() {
                
                let campos = Object.keys(this.titulos) //recuperar chaves do objetos
                let dadosFiltrados = []

                this.dados.map((item, chave) => {

                    let itemFiltrado = {}
                    campos.forEach(campo => {
                        
                        itemFiltrado[campo] = item[campo] //utilizar a sintaxe de array para atribuir valores a objetos
                    })
                    dadosFiltrados.push(itemFiltrado)
                })

                return dadosFiltrados //retorne um array de objetos 
            }
        },
        methods: { 
            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY hh:mm')
                }
            },
        },
    }
</script>
