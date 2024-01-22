<template>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-center">
                <template v-if="cart.count">
                    <div class="col-8 me-5">
                        <div class="card mb-2 p-3" v-for="(item, key) in items" :key="item.id">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <router-link class="btn" :to="`/shop/${item.product.shop.slug}`" target="_blank" alt="Shop Name">
                                        {{ item.product.shop.name }} <i class="fa-solid fa-caret-right"></i>
                                    </router-link>
                                    <button type="button" @click="deleteItem(item.id, key)" class="btn">
                                        <i class="fa-solid fa-trash" style="color: red;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 d-flex">
                                <div class="col-3">
                                    <div class="card-body">
                                        <img :src="defaultImage" class="img-fluid mb-3" style="height: 100px; width: 100px;"
                                            alt="Product Image">
                                    </div>
                                </div>
                                <div class="col-9">
                                    <div class="card-body pt-3 pb-2" style="height: 100%; margin:0; padding:0;">
                                        <div class="d-flex justify-content-around align-items-center">
                                            <div class="col-5">
                                                <h1 class="card-title">
                                                    <p style="margin:0">{{ item.product.name }}</p>
                                                    <p class="card-text fs-5">{{ item.product.description }}</p>
                                                </h1>
                                            </div>
    
                                            <div class="col-3">
                                                <p class="fs-4 text-start" style="color:#F57224">₱{{ item.product.price }} </p>
                                            </div>
    
                                            <div class="col-4">
                                                <div id="quantity" class="d-flex align-items-center">
                                                    <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                                        @click="reduceQuantity(key)">-</button>
                                                    <input type="text" v-model="item.quantity"
                                                        class="col-4 col-md-2 text-center me-2" @input="changeQuantity(key)">
                                                    <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                                        @click="addQuantity(key)">+</button>
                                                </div>
                                            </div>
    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4" style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">  
                        <div class="card mb-2 p-3" style="height:400px !important;">
                            <p style="margin-bottom:5px !important; padding:0">Location</p>
                            <div class="flex align-items-center">
                                <span class="fw-bold"><i class="fa-solid fa-location-dot me-2"></i> Barangay 659, Ermita, Metro
                                    Manila</span>
                            </div>
                            <hr style="margin-bottom:10px; padding:0;">
    
                            <p class="fs-5">Order Summary</p>
                            <div class="row mb-2" id="summary">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-bold">Subtotal (4 items)</p>
                                    <span>₱{{ parseFloat(500).toFixed(2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="fw-bold">Subtotal (4 items)</p>
                                    <span>₱{{ parseFloat(500).toFixed(2) }}</span>
                                </div>
                            </div>
    
                            <div class="d-flex mx-auto mb-4">
                                <input type="text" placeholder="Enter Voucher Code" class="form-control">
                                <button type="button" class="btn btn-primary ms-2 border-0">Apply</button>
                            </div>
    
                            <div class="row mb-3" id="summary">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-bold">Total</p>
                                    <span>₱{{ parseFloat(2500).toFixed(2) }}</span>
                                </div>
                                <button type="button" class="btn btn-primary col-10 mx-auto">Proceed To Checkout</button>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                   <p class="fs-4 mt-5"> No Items Found :(</p>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { formatDate } from '@/helpers/Formatter/Date.js';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { Items } from './types/Cart.ts';
import { useCartStore } from '@/stores/cart.js';
import axios from 'axios';
export default {
    name: 'Cart',
    data() {
        return {
            defaultImage: defaultProduct,
            auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
            items:[] as Items<number | string>,
            cart: useCartStore(),
        }
    },

    created() {
        this.getCartItems();
    },

    methods: {
        getCartItems(){
            axios.get('/api/cart-items', {
                headers:{
                    Authorization: this.auth_token,
                }
            }).then((response) =>{
                this.items = response.data.items;
                console.log(response,'response');
            }).catch((error) =>{
                console.log(error);
            })
        },

        addQuantity(index: number) {
            console.log(index,' add quantity');
            if (this.items[index].quantity == this.items[index].product.quantity) {
                return;
            } else {
                this.items[index].quantity++;
            }
        },

        changeQuantity(index: number) {
            if (this.items[index].quantity > this.items[index].product.quantity) {
                return this.items[index].quantity = this.items[index].product.quantity;
            } else if (this.items[index].quantity <= 0) {
                return this.items[index].quantity = 1;
            }
        },

        reduceQuantity(index: number) {
            if (this.items[index].quantity <= 1) {
                return this.items[index].quantity = 1;
            }
            this.items[index].quantity--;
        },

        deleteItem(item_id: number, index: number) {
            axios.delete(`/api/cart-item/${item_id}`,{
                headers:{
                    Authorization: this.auth_token,
                }
            }).then((response) =>{
                const { message, cart_items_count } = response.data;
                this.cart.setCount(cart_items_count);
                this.items.splice(index, 1);

                swalSuccess({ 
                            icon: 'success',
                            title: message,
                            showConfirmButton: false,
                        });


            }).catch((error) =>{

            });
        }
    }

}
</script>

<style lang="scss" scoped></style>