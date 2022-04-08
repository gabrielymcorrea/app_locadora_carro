<template>
    <div :class="estilo" role="alert">
        <strong>{{titulo}} </strong>{{detalhe.mensagem}}
        <ul v-if="detalhe.dados">
            <li v-for="e, key in detalhe.dados" :key="key">{{e[0]}}</li>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
    export default {
        props: ['tipo','detalhe','titulo'],
        computed:{
            estilo(){
                return 'alert-dismissible fade show alert alert-'+this.tipo
            }
        }
    }
</script>
