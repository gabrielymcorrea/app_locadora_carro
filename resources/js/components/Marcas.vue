<template>
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- início do card de busca -->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <inputContainer-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </inputContainer-component>                   
                            </div>
                            <div class="col mb-3">
                                <inputContainer-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" v-model="busca.nome">
                                </inputContainer-component>
                            </div>
                        </div>
                    </template>
                    
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de busca -->


                <!-- início do card de listagem de marcas -->
                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                         <table-component
                            :dados="marcas.data"
                            :visualizar="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaVisualizar'
                            }"
                            :atualizar="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaAtualizar'
                            }"
                            :remover="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaRemover'
                            }"
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'texto'},
                                nome: {titulo: 'Nome', tipo: 'texto'},
                                imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                created_at: {titulo: 'Criação', tipo: 'data'},
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key"
                                        :class="l.active ? 'page-item active' : 'page-item'"
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col-2"><button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button></div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>


        <!-- Modal add marca -->
        <modal-component id="modalMarca" titulo="Adicionar marca">
            <template v-slot:alertas> 
                <alert-component v-if="transacaoStatus == 'success'" tipo="success" :detalhe="transacaoDetalhes" titulo="Sucesso! marca cadastrada."> </alert-component>

                <alert-component v-if="transacaoStatus == 'error'"  tipo="danger" :detalhe="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca."> </alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <inputContainer-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </inputContainer-component>
                </div>

                <div class="form-group">
                    <inputContainer-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </inputContainer-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>


        <!-- Modal visualizar marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar marca">
                <template v-slot:conteudo>
                    <inputContainer-component titulo="ID">
                        <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                    </inputContainer-component>

                    <inputContainer-component titulo="Nome da marca">
                        <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                    </inputContainer-component>

                    <inputContainer-component titulo="Imagem">
                        <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                    </inputContainer-component>

                    <inputContainer-component titulo="Data e hora da criação">
                        <input type="text" class="form-control" :value="format_date($store.state.item.created_at)" disabled>
                    </inputContainer-component>
                </template>
        </modal-component>

        <!-- Modal exclusão marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover marca">
             <template v-slot:alertas> 
                <alert-component v-if="$store.state.transacao.status == 'sucesso'" tipo="success" :detalhe="$store.state.transacao" titulo="Sucesso!"> </alert-component>

                <alert-component v-if="$store.state.transacao.status == 'erro'"  tipo="danger" :detalhe="$store.state.transacao" titulo="Opss... algo deu errado!"> </alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <inputContainer-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </inputContainer-component>

                <inputContainer-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </inputContainer-component>
            </template>

            <template v-slot:rodape>
                <button v-if="$store.state.transacao.status != 'sucesso'" type="button" class="btn btn-danger" @click="remover()">Remover</button>
            </template>
        </modal-component>

        <!-- Modal atualização de  marca -->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar marca">
             <template v-slot:alertas> 
                <alert-component v-if="$store.state.transacao.status == 'sucesso'" tipo="success" :detalhe="$store.state.transacao" titulo="Sucesso!"> </alert-component>

                <alert-component v-if="$store.state.transacao.status == 'erro'"  tipo="danger" :detalhe="$store.state.transacao" titulo="Opss... algo deu errado!"> </alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <inputContainer-component titulo="Nome da marca" id="atualizarNome" id-help="atualizarNomeHelp">
                    <input type="text" class="form-control"  id="atualizarNome" aria-describedby="atualizarNomeHelp" v-model="$store.state.item.nome">
                </inputContainer-component>

                <inputContainer-component titulo="Nova imagem" id="atualizarImagem" id-help="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                    <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                </inputContainer-component>
            </template>

            <template v-slot:rodape>
                <button v-if="$store.state.transacao.status != 'sucesso'" type="button" class="btn btn-success" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
    </div>
</template>

<script>
    import Paginate from './Paginate.vue'
    import moment from 'moment';
    export default {
    components: { Paginate },
        data() {
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: { data: [] },
                busca: { id: '', nome: '' }
            }
        },
        methods: {
            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY hh:mm')
                }
            },
            pesquisar() {
                let filtro = ''

                for(let chave in this.busca) {

                    if(this.busca[chave]) {
                        if(filtro != '') {
                            filtro += ";"
                        }

                        filtro += chave + ':like:' + this.busca[chave]
                    }
                }
                if(filtro != '') {
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro='+filtro
                } else {
                    this.urlFiltro = ''
                }

                this.carregarLista()
            },
            paginacao(l) {
                if(l.url) {
                    //this.urlBase = l.url //ajustando a url de consulta com o parâmetro de página
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarLista() //requisitando novamente os dados para nossa API
                }
            },
            carregarLista() {
                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro

                axios.get(url)
                    .then(response => {
                        this.marcas = response.data
                    })
                    .catch(errors => {

                    })
            },
            carregarImagem(e) {
                this.arquivoImagem = e.target.files
            },
            salvar() {
                let formData = new FormData();
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'success'
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro: ' + response.data.id
                        }

                    })
                    .catch(errors => {
                        this.transacaoStatus = 'error'
                        
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                    
                    })
            },
            remover(){
                let confirmacao = confirm('tem certeza que deseja remover esse registro?')

                if(!confirmacao) return false


                let formData = new FormData();
                formData.append('_method', 'delete')

                let url = this.urlBase + '/' + this.$store.state.item.id

                axios.post(url, formData)
                    .then(response => {
                        this.$store.state.transacao.status =  'sucesso'
                        this.$store.state.transacao.mensagem = response.data.success

                        this.carregarLista()
                    })
                    .catch(errors => {

                        this.$store.state.transacao.status =  'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.erro
                    })
            },
            atualizar(){
                let formData = new FormData();
                formData.append('_method', 'patch')
                formData.append('nome', this.$store.state.item.nome)

                if(this.arquivoImagem[0]) {
                    formData.append('imagem', this.arquivoImagem[0])
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }

                axios.post(url, formData, config)
                    .then(response => {
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso!'

                        //limpar o campo de seleção de arquivos
                        atualizarImagem.value = ''
                        this.carregarLista()
                    })
                    .catch(errors => {
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.message
                        this.$store.state.transacao.dados = errors.response.data.errors
                    })
            }
        },
        mounted() {
            this.carregarLista()
        }
    }
</script>
