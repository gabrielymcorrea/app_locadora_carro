<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="t, key in titulos" :key="key">{{t.titulo}}</th>
                    <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel">Ação</th>
                </tr>
            </thead> 
            <tbody> <!-- é chamado assim dadosFiltrados e nao assim dadosFiltrados() pq é computed -->
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'texto'">{{valor}}</span>
                        <span v-if="titulos[chaveValor].tipo == 'data'">
                            <!-- {{ format_date(valor)}} plugin -->

                            {{ valor | formataDataTempoGlobal }}  <!-- funcao criada em app.js-->
                        </span>
                        <span v-if="titulos[chaveValor].tipo == 'imagem'">
                            <img :src="'/storage/'+valor" width="30" height="30">
                        </span>
                    </td>

                    <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel"> 
                        <button @click="setStore(obj)" v-if="visualizar.visivel" class="btn btn-info btn-sm" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget"> <img src="img/view.svg" height="15" width="15"/></button>
                        <button @click="setStore(obj)" v-if="atualizar.visivel" class="btn btn-warning btn-sm" :data-toggle="atualizar.dataToggle" :data-target="atualizar.dataTarget"> <img src="img/edit.svg" height="15" width="15"/></button>
                        <button @click="setStore(obj)" v-if="remover.visivel" class="btn btn-danger btn-sm" :data-toggle="remover.dataToggle" :data-target="remover.dataTarget"> <img src="img/delete.svg" height="15" width="15"/></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import moment from 'moment';
    export default {
        // filters :{
        //     formatDateHora(){
        //         if(!d) return '';

        //         d = d.split('T')

        //         let data = d[0]
        //         let tempo = d[1]

        //         data = data.split('-')
        //         data = data[2] + '/' + data[1] + '/' + data[0]


        //         tempo = tempo.split('.')

        //         return data + ' ' + tempo
        //     }
        // },
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

            setStore(obj){
                this.$store.state.transacao.status = ''
                this.$store.state.transacao.mensagem = ''
                this.$store.state.transacao.dados = ''
                this.$store.state.item = obj
            }
        },
    }
</script>
