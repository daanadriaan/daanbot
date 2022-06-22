<template>
    <div class="navigation">
        <router-link class="cursor-pointer" replace :to="{name: 'maker', params:{id: flow.id}}" v-for="flow in $root.flows" :key="flow.id">
            <input v-model="flow.name" @change="update(flow)">
        </router-link>
        <a @click="add">+</a>
    </div>
</template>

<style lang="scss">
.navigation{
    position: relative;
    z-index: 100;
}
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
