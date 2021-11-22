<template>
    <div class="daanbot__chat">
        <div class="daanbot__avatar">
            <avatar></avatar>
        </div>
        <div class="daanbot__cloud" :class="{'daanbot__cloud--loading': loading}">
            <div class="daanbot__cloud__ball"></div>
            <div class="daanbot__cloud__ball"></div>
            <div class="daanbot__cloud__ball"></div>
            <div class="daanbot__cloud__container" v-html="chat.content"></div>
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
    created(){
        setTimeout(timer => {
            this.loading = false;
        }, 600)
    }
}
</script>
