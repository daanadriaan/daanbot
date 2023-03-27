<template>
    <div class="daanbot__chat" :class="{'daanbot__chat--user': chat.user_input}">
        <div class="daanbot__avatar">
            <avatar></avatar>
        </div>
        <div class="daanbot__clouds">
            <div class="daanbot__cloud"
                 :class="{
                    'daanbot__cloud--loading': ! ready[key],
                    'daanbot__cloud--image': content.includes('<img'),
                    'daanbot__cloud--video': content.includes('<iframe'),
                    }"
                 :key="'cloud'+chat.id+'-'+key"
                 v-if="content.length > 0"
                 v-for="(content, key) in contents">
                <div class="daanbot__cloud__ball"></div>
                <div class="daanbot__cloud__ball"></div>
                <div class="daanbot__cloud__ball"></div>
                <div class="daanbot__cloud__container" v-html="content"></div>
            </div>
        </div>

    </div>
</template>

<script>
import Avatar from './Avatar';

export default {
    props:['chat'],
    components:{
        Avatar
    },
    data() {
        return {
            ready: {
                0: false,
                1: false,
                2: false,
                3: false,
                4: false,
                5: false,
                6: false,
                7: false,
            }
        }
    },
    computed:{
        contents(){
            var html = new DOMParser().parseFromString(this.chat.content, "text/html");
            return Array.prototype.slice.call(html.getElementsByTagName('p')).map(p => {
                p = p.innerHTML.replace('\xa0', ' '); return p;
            });
        }
    },
    created(){
        this.load(0);
    },
    methods:{
        load(key){
           //var self = this;
            this.$emit('scroll', this.chat);
            setTimeout(function(){
                this.ready[key] = true;
                if(this.contents.length > (key + 1)){
                    this.load(key + 1);

                }else{
                    this.$emit('ready', this.chat);
                }
            }.bind(this), this.chat?.delay ? this.chat.delay : 2000)
        }
    },
}
</script>
