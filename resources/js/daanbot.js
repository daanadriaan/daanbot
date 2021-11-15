require('./bootstrap');
import axios from 'axios';
import Vue from 'vue';
require('./pusher.min.js');

import Echo from "laravel-echo"
window.Pusher = require('pusher-js');

import Daanbot from './components/Daanbot';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '1e8fa86951ec9ac668e2',
    cluster: 'eu',
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    encrypted: true,
    auth: {
        headers: {
            Authorization: "Bearer " + localStorage.getItem("token"),
        },
    },
});

new Vue({
    el: '#daanbot',
    data() {
        return {

        };
    },
    created() {

        // Private
       /*
        window.Echo.private('App.Models.User.'+document.head.querySelector('meta[name="auth_id"]').content)
            .notification((notification) => {
                // Notifications
                if(notification.type === 'Overcast\\Notifications\\Info'){
                    this.$emit('notification', notification);
                    this.alerts.push(notification);
                }

                // Chat message
            });
            */
    },
    components: {
        daanbot: Daanbot
    },
    methods:{

    },
});
