<template>
    <template v-if="isUserAuthenticated">
        <Navbar/>
    </template>
    <router-view></router-view>
</template>

<script>

import defaultProduct from '@/../../public/storage/default_images/product.png';
import {isAuthenticated as checkIsAuthenticated} from '@/helpers/Auth/isAuthenticated.js'; 
import { useCartStore } from '@/stores/cart.js';
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
                cart: useCartStore(),

            }
        },
        components:{
            Navbar,
        },
        watch: {
            async $route(to, from) {
                await this.checkAuthentication();
            }
        },
        async created() {
            await this.checkAuthentication();
            this.getPartials();
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

            getPartials(){
                axios.get('/api/get-partials', {
                    headers: {
                        Authorization: `Bearer ${this.token}`
                    },
                }).then((response) => {
                    const { cart_items_count } = response.data;

                    this.cart.setCount(cart_items_count);
                    
                }).catch((error) => {
                    this.isLoading = false;
                    console.log(error);
                })
            }
        }
    }
</script>

<style lang="scss">
@import '@/../css/app.css';
@import '@/../scss/app.scss';
</style>