<template>
    <div class="daanbot__chat" :class="{'daanbot__chat--user': chat.user_input}">
        <div class="daanbot__avatar">
            <avatar></avatar>
        </div>
        <div class="daanbot__clouds">
            <div class="daanbot__cloud"
                 :class="{'daanbot__cloud--loading': loading}"
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
            loading: true,

        }
    },
    computed:{
        contents(){
            var html = new DOMParser().parseFromString(this.chat.content, "text/html");
            return Array.prototype.slice.call(html.getElementsByTagName('p')).map(p => {
                p = p.innerHTML; return p;
            });
        }
    },
    created(){
        setTimeout(timer => {
            this.$emit('ready');
            this.loading = false;
        }, this.chat.delay ? this.chat.delay : 2000)
    }
}
</script>
