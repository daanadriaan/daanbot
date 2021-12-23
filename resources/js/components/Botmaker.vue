<template>
    <div class="botmaker">
        <div class="botmaker-menu">
            <div class="botmaker__button" @click="addStep('Response')">+ Chat</div>
            <div class="botmaker__button" @click="addStep('Option')">+ Option</div>
            <div class="botmaker__button" @click="dropdown = !dropdown">
                + Flow
                <div class="botmaker__button__dropdown" v-if="dropdown">
                    <div v-for="flow in flows"
                         @click="addStep('Flow', flow)">
                        {{ flow.label }}
                    </div>
                </div>
            </div>
            <div class="botmaker__button" @click="undo">< Undo</div>
            <div class="botmaker__button" @click="store">↓ Save</div>
        </div>
        <flow :scene.sync="data"
              @nodeClick="nodeClick"
              @nodeDelete="nodeDelete"
              @linkBreak="linkBreak"
              @linkAdded="linkAdded"
              @canvasClick="canvasClick"/>
        <div v-if="editable" class="botmaker-window">
            <div v-if="editable.type !== 'Flow'">
                <input class="form-control" v-model="editable.label">
            </div>
            <div v-if="editable.type !== 'Flow'">
                <editor v-model='editable.content'
                        :init='editorOptions'></editor>
            </div>
            <div v-if="editable.type === 'Flow'">
                <v-select :options="flows"
                          :label="'name'"
                          placeholder="Filter: Selecteer een flow"
                          class="bg-white w-full"
                          v-model="editable.redirect"/>
            </div>
            <div class="botmaker__button mt-5" @click="saveEditable">Opslaan</div>
        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select';
import axios from "axios";
import Flow from 'vue-simple-flowchart';
require('../tinymce_init.js');
require('vue-select/dist/vue-select.css')
require('vue-simple-flowchart/dist/vue-flowchart.css');
import Editor from '@tinymce/tinymce-vue';

export default {
    props:[],
    components:{
        Flow, Editor, vSelect
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
            editable: null,
            editorOptions:   {
                path_absolute:               '/',
                language:                    'nl',
                theme:                       'modern',
                resize:                      false,
                //content_css:                 '/css/tinymce_content.css',
                menu:                        {
                    edit:   {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
                    format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript'},
                    table:  {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
                },
                autoresize_min_height:       350,
                autoresize_max_height:       1000,
                autoresize_overflow_padding: 10,
                autoresize_on_init:          true,
                fontsize_formats:            '15px 18px 20px',
                placeholder:                 'Typ of plak jouw tekst in dit venster..',
                plugins:                     [
                    'placeholder',
                    'autoresize advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste'
                ],
                toolbar:                     'undo | bold italic | bullist numlist | link hr | code',
            },
            dropdown: false,
        }
    },
    computed: {
        flows(){
            return this.toOptions(this.$root.flows);
        },
        lastId(){
            return this.data.nodes.length ? this.max(this.data.nodes.map(item => item.id)) : 1;
        },
        selected(){
            return this.data.nodes.filter(node => { return node.options?.selected; });
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
        addStep(type, options = null){
            var data = {
                id: this.lastId + 1,
                x: this.center.x + 20,
                y: this.center.y + 20,
                type: type,
                redirect: options,
                label: type == 'Flow' ? 'Ga naar '+options.label : 'Nieuwe '+type
            };
            axios.post('/flows/'+this.$route.params.id+'/steps/new', data)
                .then(response => {
                    data.real_id = response.data.id;
                    this.data.nodes.push(data);
                    this.editable = data;
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
        nodeClick(id){
            this.editable = id ? this.data.nodes.filter(node => {return node.id === id})[0] : null;
        },
        nodeDelete(){
            this.editable = null;
        },
        linkBreak(link){
            this.data.links = this.data.links.filter(l => {return l.id !== link.id});
            this.editable = null;
        },
        linkAdded(){
            this.editable = null;
        },
        canvasClick(){
            console.log('canvasClick');
        },
        saveEditable(){
            this.store();
            this.editable = null;
        },
        // REUSABLE
        toOptions(data, key = 'name') {
            var options = [];
            data.forEach(v => {
                v['value'] = v.id;
                v['label'] = v[key];
                options.push(v);
            });
            return options
        },
    }
}
</script>

<style lang="scss">
.flowchart-container, .flowchart-container svg{
    height: 100vh;
}
.botmaker {
    position: relative;

    &-window {
        z-index: 1;
        position: absolute;
        top: 0;
        right: 0;
        width: 500px;
        height: 100%;
        background: white;
        padding: 10px;

        .form-control {
            width: 100%;
            border: 1px solid lightgrey;
            border-radius: 3px;
            padding: 5px 10px;
            margin-bottom: 10px;
        }
    }

    &-menu {
        position: absolute;
        left: 0;
        top: 0;
        padding: 10px;
        z-index: 1;
    }

    &__button{
        position: relative;
        cursor: pointer;
        background: #5E6BF6;
        color: white;
        border-radius: 5px;
        padding: 5px 15px;
        margin-bottom: 10px;

        &__dropdown{
            background: white;
            padding: 5px 10px;
            min-width: 140px;
            color: black;
            border-radius: 4px;
            position: absolute;
            left: 120px;
            top: 0;
        }
    }

    .v-select{
        width: 200px;
    }
}
</style>
