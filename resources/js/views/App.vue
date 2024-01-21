<template>
    <template v-if="isUserAuthenticated">
        <Navbar/>
    </template>
    
    <router-view></router-view>
</template>

<script>
import defaultProduct from '@/../../public/storage/default_images/product.png';
import {isAuthenticated as checkIsAuthenticated} from '@/helpers/Auth/isAuthenticated.js'; 
import axios from 'axios';
import Navbar from '@/components/Navbar/Header.vue';
    export default {
        name:'App',
        data(){
            return{
                token: localStorage.getItem('auth-token'),
                isUserAuthenticated: false,
                logo:defaultProduct,
                search:null,
            }
        },
        components:{
            Navbar
        },
        watch: {
            async $route(to, from) {
                await this.checkAuthentication();
            }
        },
        async created() {
            await this.checkAuthentication();
        },
        methods:{
            async checkAuthentication() {
                try {
                    const data = await checkIsAuthenticated();
                    this.isUserAuthenticated = data;
                } catch (error) {
                    console.error('Error checking authentication:', error);
                }
            },
        }
    }
</script>

<style lang="scss">
@import '@/../css/app.css';
@import '@/../scss/app.scss';
</style>