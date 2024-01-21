<template>
    <div class="row">
        <div class="col-8 mx-auto my-2">
            <div class="card mb-2 p-3" style="height:100% !important;">
                <div class="col-12">
                    <span class="fw-bold">Category :</span> {{ details.categories }}
                </div>
                <div class="col-12 d-flex">
                    <div class="col-4">
                        <div class="card-body pt-3 pb-2 px-3 d-flex" style="height: 100%">
                            <img :src="details.product_image" class="img-fluid mb-3" style="height: 400px; width: 400px;"
                                alt="Product Image">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="card-body pt-3 pb-2 px-3 d-flex" style="height: 100%">
                            <div class="w-100 p-4">
                                <div class="text-end ">
                                    <router-link :to="`/shop/${details?.shop?.slug}`" target="_blank" alt="Shop Image">
                                        <img :src="details?.shop?.shop_image" class="img-fluid mb-3 text-end"
                                            style="height:75px; width: 75px;" alt="Shop Image">
                                    </router-link>
                                </div>
                                <h1 class="card-title text-truncate mb-4">
                                    <p>{{ details.name }}</p>
                                    <p class="card-text fs-5">{{ details.description }}</p>
                                </h1>
                                <p class="card-text text-start fs-4" style="color:#F57224">₱{{ details.price }} </p>

                                <div id="quantity" class="d-flex align-items-center mb-5">
                                    <span class="me-2">Quantity:</span>
                                    <template v-if="details.quantity">
                                        <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                            @click="reduceQuantity">-</button>
                                        <input type="text" v-model="order.quantity" class="col-2 text-center"
                                            @input="changeQuantity">
                                        <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                            @click="addQuantity">+</button>
                                    </template>
                                    <template v-else>
                                        <span class="text-end fw-bold">Out of stock</span>
                                    </template>
                                </div>
                                <div class="flex align-items-end">
                                    <template v-if="details.quantity">
                                        <button class="btn col-5  me-2  text-white"  style="background-color:#00b300">Buy</button>
                                        <button class="btn col-5 text-white" type="button" style="background-color:#FFAC1C" @click="addToCart">Add To Cart</button>
                                    </template>
                                    <template v-else>
                                        <button class="btn col-5 me-2 "  style="background-color:#face0b" @click="addToWishlist">Add to wishlist</button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card-body bg-body-tertiary pt-3 pb-2 px-3 d-flex flex-column" style="height: 100%;">
                            <div class="mb-2">
                                <p class="fs-5 fw-bold">Delivery Options</p>
                                <p><i class="fa-solid fa-location-dot me-2"></i> Metro Manila~Quezon City, Quezon City,
                                    Project </p>
                                <p><i class="fa-solid fa-truck-fast me-2"></i> Priority/48H</p>
                                <p class="fs-6"><i class="fa-solid fa-money-bill-transfer me-2"></i> ₱40.00 Cash on Delivery
                                    Available for orders below ₱25,000</p>
                            </div>
                            <div id="return_and_warranty">
                                <p class="fs-5 fw-bold">Return & Warranty</p>
                                <p><i class="fa-regular fa-gem me-2"></i> 100% Authentic</p>
                                <p><i class="fa-solid fa-person-walking-arrow-loop-left me-2"></i> Change of mind returns
                                </p>
                                <p><i class="fa-solid fa-rotate-left me-2"></i> 30 Days Returns</p>
                                <p><i class="fa-solid fa-shield me-2"></i> Warranty not available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { formatDate } from '@/helpers/Formatter/index.js';
export default {
    data() {
        return {
            details: {},
            order: {
                quantity: 1,
                size: 0,
            },
            isLoading: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
        }
        
    },

    created() {
        this.getProductDetails();
    },

    methods: {
        addQuantity() {
            if (this.order.quantity == this.details.quantity) {
                return;
            } else {
                this.order.quantity++;
            }
        },

        changeQuantity() {
            if (this.order.quantity > this.details.quantity) {
                return this.order.quantity = this.details.quantity;
            } else if (this.order.quantity <= 0) {
                return this.order.quantity = 1;
            }
        },

        reduceQuantity() {
            if (this.order.quantity <= 1) {
                return this.order.quantity = 1;
            }
            this.order.quantity--;
        },

        addToCart(){
            axios.post(`/api/product-add-to-cart`,{ product_id: this.details.id,  quantity: this.order.quantity },{
                headers: {
                    Authorization: this.auth_token
                }
            }).then((response) => {
                const { message, data } = response.data;

                this.details = data;
                this.order = {
                    quantity: 1,
                    size: 0,
                },
                SwalDefault.fire({
                    icon: "success",
                    text: message,
                    showConfirmButton: false,
                    timer:1500,
                    toast:true,
                    position: "top-end",
                    animation:false
                });

            }).catch((error) => {
                console.log(error, 'ERROR');
            });
        },

        addToWishlist(){
            SwalDefault.fire({
                    icon: "success",
                    text: 'Succesfully added to wishlist, you\'ll be notified once its available!',
                    showConfirmButton: false,
                    timer:3000,
                    toast:true,
                    position: "top-end",
                    animation:false,
                    width:'auto',
                });
        },
        
        async getProductDetails() {
            this.isLoading = true;
            await axios.get(`/api/product-details/${this.$route.params.slug}`, {
                headers: {
                    Authorization: this.auth_token
                }
            }).then((response) => {
                const { details } = response.data;

                const formatData =   {
                            ...details,
                            status: details.active ? 'Active' : 'Inactive',
                            created_at: formatDate(undefined, details.created_at),
                            updated_at: formatDate(undefined, details.updated_at),
                            categories: details.categories.map(category => category.name).join(', ')
                        }
                this.details = formatData;
                this.isLoading = false;
            }).catch((error) => {
                this.isLoading = false;
                console.log(error, 'ERROR');
            });
        },
    }
}
</script>

<style lang="scss" scoped></style>