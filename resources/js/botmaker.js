import axios from "axios";

require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router';

import Botmaker from './components/Botmaker'
import Navigation from './components/Navigation'

Vue.use(VueRouter);

let routes = [
    {
        path: '/maker/:id',
        name: 'maker',
        component: Botmaker,
    }
];
const vueRouter = new VueRouter({
    mode: 'history',
    linkActiveClass: 'font-bold',
    routes:routes,
});

new Vue({
    el: '#app',
    data() {
        return {
            flows: []
        };
    },
    created() {
        axios.get('/flows')
            .then(response => {
                this.flows = response.data;
            }).catch(error => {
                console.log(error);
            });
    },
    components: {
        Botmaker, Navigation
    },
    methods:{
        newFlow(flow){
            this.flows.push(flow);
            this.$router.push({name: 'maker', params:{id: flow.id}});
        }
    },
    router: vueRouter
});
