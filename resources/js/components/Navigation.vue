<template>
    <div>
        <router-link class="cursor-pointer" replace :to="{name: 'maker', params:{id: flow.id}}" v-for="flow in $root.flows">
            <input v-model="flow.name" @change="update(flow)">
        </router-link>
        <a @click="add">+</a>
    </div>
</template>

<style lang="scss">
.font-bold > input{
    font-weight:bold;
}
</style>

<script>
import axios from "axios";

export default {
    created(){

    },
    methods: {
        add(){
            axios.post('/flows/create')
                .then(response => {
                    this.$emit('new-flow', response.data);
                }).catch(error => {
                    console.log(error);
                });
        },
        update(flow){
            axios.post('/flows/'+flow.id+'/update', {flow: flow})
                .then(response => {
                    if(flow.name.length === 0){
                        window.location.href = '/maker';
                    }
                }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>
