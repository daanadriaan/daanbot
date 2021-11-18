<template>
    <div class="daanbot" :class="{'daanbot--loading': loading}">
        <div class="daanbot__container">
            <div class="daanbot__chats">
                <chat :key="chat.id"
                      v-for="chat in chats"
                      :chat="chat"/>
            </div>
            <div class="daanbot__bottom">
                <div class="daanbot__option daanbot__box"
                     v-for="option in options"
                     @click="chooseOption(option)">
                    {{ option.pivot.label }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Chat from './Chat';
import Avatar from './Avatar';

require('./../../css/daanbot.scss');

export default {
    props:['fields', 'post', 'force'],
    components:{
        Avatar,
        Chat
    },
    data() {
        return {
            loading: true,
            chats:[],
            options:[]
        }
    },
    created(){
        this.get();
    },
    methods: {
        get(){
            axios.get('/conversation')
                .then(response => {
                    console.log(response.data);
                    this.loading = false;
                    this.chats = this.chats.concat(response.data.chats);
                    this.options = this.chats[this.chats.length - 1].options;

                }).catch(error => {
                    console.log(error);
                });
        },
        chooseOption(option){
            axios.post('/conversation/choose', {'option': option.id})
                .then(response => {
                    this.chats = this.chats.concat(response.data.chats);
                    this.options = this.chats[this.chats.length - 1].options;

                }).catch(error => {
                    console.log(error);
                });
        }
    }
}
</script>
