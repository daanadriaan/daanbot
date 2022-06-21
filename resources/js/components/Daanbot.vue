<template>
    <div class="daanbot" :class="{'daanbot--loading': loading, 'daanbot--welcome': welcome}">
        <div class="daanbot__container">
            <div class="daanbot__chats">
                <div class="daanbot__welcome">
                    <div class="daanbot__welcome__avatar">
                        <img src="/img/daan.jpg">
                    </div>
                    <div class="daanbot__welcome__right">
                        <h1>Daan Muilkens</h1>
                        <h2>Freelance webdeveloper</h2>
                        <button @click="start" class="daanbot__option" v-if="welcome">Contact</button>
                    </div>
                </div>
                <chat :ref="'chat'"
                      v-if="!welcome"
                      @scroll="scrollToEnd"
                      @ready="showOptions(chat)"
                      :key="'chat'+chat.id+'-'+index"
                      v-for="(chat, index) in chats"
                      :chat="chat"/>
            </div>
            <div class="daanbot__options" >
                <div class="daanbot__option daanbot__box"
                     v-for="option in options"
                     @click="chooseOption(option)">
                    {{ option.label }}
                </div>
            </div>
            <div class="daanbot__bottom" v-if="!welcome">
                <div class="daanbot__bottom__textarea">
                    <textarea v-model="message"
                              :disabled="!typing"
                              @keypress="enter"
                              :placeholder="placeholder"></textarea>
                    <button @click="askQuestion">
                        <svg width="43px" height="39px" viewBox="0 0 43 39" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="Artboard-2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path d="M5.84253488,2.191555 L38.893123,17.6901217 C39.8931944,18.1590899 40.3237386,19.3499816 39.8547705,20.350053 C39.6418497,20.8041051 39.2661191,21.1615969 38.8020423,21.3516793 L5.84879589,34.8490893 C4.8266448,35.2677549 3.65863205,34.7785333 3.23996641,33.7563822 C3.05750331,33.3109078 3.04183861,32.814568 3.19584781,32.3584743 L6.55199013,22.4193586 C6.79807151,21.690596 7.44028425,21.1675335 8.20376919,21.0740357 L15.6898279,20.1572781 C16.2005213,20.0947376 16.563821,19.6300397 16.5012804,19.1193463 C16.4479363,18.6837488 16.0978189,18.3444484 15.6607623,18.3047963 L8.30152752,17.6371271 C7.4878691,17.5633076 6.8009141,17.0020118 6.56640033,16.2193927 L3.07755727,4.57642929 C2.7604989,3.51834245 3.36122117,2.40356699 4.41930802,2.08650863 C4.89049321,1.94531681 5.39718504,1.98271502 5.84253488,2.191555 Z" id="Path-4" fill="#FFFFFF"></path>
                            </g>
                        </svg>
                    </button>
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
            welcome: true,

            chats:[],
            options:[],
            typing: true,
            message: '',
            placeholder: 'Typ hier je vraag..'
        }
    },
    created(){
        this.get(true);
    },
    methods: {
        get(initial){
            axios.get('/conversation')
                .then(response => {
                    this.loading = false;
                    if(initial) this.checkInitial(response.data);
                    this.appendChats(response.data.chats, 0);

                }).catch(error => {
                    console.log(error);
                });
        },
        appendChats(chats, delay = 1000){
            setTimeout(timer => {
                this.chats = this.chats.concat(chats);
                if(delay === 0){
                    this.chats = this.chats.map(item => {item.delay = 1; return item;});
                }
                this.scrollToEnd();
            }, delay);
        },
        chooseOption(option){
            option.user_input = true;
            this.options = [];
            this.chats.push(option);

            this.scrollToEnd();

            axios.post('/conversation/choose', {'option': option.id})
                .then(response => {
                    this.appendChats(response.data.chats);
                }).catch(error => {
                    console.log(error);
                });
        },
        askQuestion(){
            if(this.message.length < 1) return;

            this.appendChats([{
                content: '<p>'+this.message+'</p>',
                user_input: true
            }], 0);

            axios.post('/conversation/interpret', {'message': this.message})
                .then(response => {
                    //this.appendChats([response.data.chat], 0);
                    this.appendChats(response.data.responses, 3000);
                    this.message = '';
                }).catch(error => {
                    console.log(error);
                });
        },
        enter(event){
            // If key is arrow.. navigate;
            if (event.key && event.key.includes('Arrow')) {
                if (['ArrowUp', 'ArrowDown'].includes(event.key)) {
                    //this.navigate(event);
                    event.preventDefault();
                }
                return;
            } else if (event.key && event.key.includes('Enter')) {
                this.askQuestion();
                event.preventDefault();
                return;
            }
        },
        scrollToEnd(){
            setTimeout(timer => {
                window.scroll({ top: document.body.scrollHeight , behavior: 'smooth' });
            }, 100);
        },
        showOptions(chat){
            if(chat.content !== this.chats[this.chats.length -1].content) return; // dirty hack to tackle faulty calls to this function.
            let options = [];
            for(let option of chat.options){ // dirty hack to tackle doubles
                if(!options.map(item => item.id).includes(option.id)) options.push(option);
            }
            this.options = options;
            this.scrollToEnd();
        },
        checkInitial(data){
            if(data.chats.length > 1){
                // Remove welcome
                this.welcome = false;
            }
        },
        start(){
            this.welcome = false;
        }
    }
}
</script>
