<template>
    <div class="botmaker">
        <div class="botmaker-menu">
            <div class="botmaker-menu__button" @click="addChat">+ Chat</div>
            <div class="botmaker-menu__button" @click="addOption">+ Option</div>
            <div class="botmaker-menu__button" @click="undo">< Undo</div>
            <div class="botmaker-menu__button" @click="store">↓ Save</div>
        </div>
        <flow :scene.sync="data"
              @nodeClick="nodeClick"
              @nodeDelete="nodeDelete"
              @linkBreak="linkBreak"
              @linkAdded="linkAdded"
              @canvasClick="canvasClick"/>
    </div>
</template>

<script>
import axios from "axios";
import Flow from 'vue-simple-flowchart';
require('vue-simple-flowchart/dist/vue-flowchart.css');

export default {
    props:[],
    components:{
        Flow
    },
    data() {
        return {
            data: {
                centerX: 1024,
                centerY: 140,
                scale: 1,
                nodes: [],
                links: []
            },
        }
    },
    computed: {
        lastId(){
            return this.data.nodes.length ? this.max(this.data.nodes.map(item => item.id)) : 1;
        },
        center(){
            return this.data.nodes.length ? {
                x: this.mean(this.data.nodes.map(item => item.x)),
                y: this.mean(this.data.nodes.map(item => item.y)),
            } : {x: 300, y: 300};
        }
    },
    created(){
        axios.get('/flows/'+this.$route.params.id)
            .then(response => {
                this.data = response.data;
            }).catch(error => {
                console.log(error);
            });
    },
    methods: {
        max(array){
            var max = 0;
            for (var i = 0; i < array.length; i++) {
                max = array[i] > max ? array[i] : max;
            }
            return max;
        },
        mean(array){
            var total = 0;
            for (var i = 0; i < array.length; i++) {
                total += parseInt(array[i]);
            }
            return total / array.length;
        },
        addChat(){
            var data = {
                id: this.lastId + 1,
                x: this.center.x + 20,
                y: this.center.y + 20,
                type: 'Chat',
                label: 'Nieuwe chat'
            };
            axios.post('/flows/'+this.$route.params.id+'/chats/new', data)
                .then(response => {
                    data.real_id = response.data.id;
                    this.data.nodes.push(data);
                }).catch(error => {
                    console.log(error);
                });

        },
        addOption(){
            var data = {
                id: this.lastId + 1,
                x: this.center.x + 20,
                y: this.center.y + 20,
                type: 'Option',
                label: 'Nieuwe optie'
            };
            axios.post('/flows/'+this.$route.params.id+'/options/new', data)
                .then(response => {
                    data.real_id = response.data.id;
                    this.data.nodes.push(data);
                }).catch(error => {
                    console.log(error);
                });
        },
        undo(){

        },
        store(){
            axios.post('/flows/'+this.$route.params.id, this.data)
                .then(response => {
                    // console.log(response.data);
                    // this.data = response.data;
                }).catch(error => {
                console.log(error);
            });
        },
        nodeClick(){
            console.log('nodeClick');
        },
        nodeDelete(){
            console.log('nodeDelete');
        },
        linkBreak(){
            console.log('linkBreak');
        },
        linkAdded(){
            console.log('linkAdded');
        },
        canvasClick(){
            console.log('canvasClick');
        }
    }
}
</script>

<style lang="scss">
.flowchart-container, .flowchart-container svg{
    height: 100vh;
}
.botmaker{
    position: relative;
}
.botmaker-menu{
    position: absolute;
    left: 0;
    top: 0;
    padding:10px;
    z-index: 1;
    &__button{
        cursor: pointer;
        background: #5E6BF6;
        color: white;
        border-radius: 5px;
        padding: 5px 15px;
        margin-bottom: 10px;
    }
}
</style>
