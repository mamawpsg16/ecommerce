<template>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-center">
                <div class="col-8 me-5">
                    <div class="card mb-2 p-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <router-link class="btn" to="#" target="_blank" alt="Shop Image">
                                    Gatorade <i class="fa-solid fa-caret-right"></i>
                                </router-link>
                                <i class="fa-solid fa-trash"></i>
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
                                                <p style="margin:0">Gatorade</p>
                                                <p class="card-text fs-5"> Energy Drink </p>
                                            </h1>
                                        </div>

                                        <div class="col-3">
                                            <p class="fs-4 text-start" style="color:#F57224">₱ 500 </p>
                                        </div>

                                        <div class="col-4">
                                            <div id="quantity" class="d-flex align-items-center">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                                    @click="reduceQuantity">-</button>
                                                <input type="text" v-model="order.quantity"
                                                    class="col-4 col-md-2 text-center me-2" @input="changeQuantity">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                                    @click="addQuantity">+</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2 p-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <router-link class="btn" to="#" target="_blank" alt="Shop Image">
                                    Gatorade <i class="fa-solid fa-caret-right"></i>
                                </router-link>
                                <i class="fa-solid fa-trash"></i>
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
                                                <p style="margin:0">Gatorade</p>
                                                <p class="card-text fs-5"> Energy Drink </p>
                                            </h1>
                                        </div>

                                        <div class="col-3">
                                            <p class="fs-4 text-start" style="color:#F57224">₱ 500 </p>
                                        </div>

                                        <div class="col-4">
                                            <div id="quantity" class="d-flex align-items-center">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                                    @click="reduceQuantity">-</button>
                                                <input type="text" v-model="order.quantity"
                                                    class="col-4 col-md-2 text-center me-2" @input="changeQuantity">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                                    @click="addQuantity">+</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2 p-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <router-link class="btn" to="#" target="_blank" alt="Shop Image">
                                    Gatorade <i class="fa-solid fa-caret-right"></i>
                                </router-link>
                                <i class="fa-solid fa-trash"></i>
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
                                                <p style="margin:0">Gatorade</p>
                                                <p class="card-text fs-5"> Energy Drink </p>
                                            </h1>
                                        </div>

                                        <div class="col-3">
                                            <p class="fs-4 text-start" style="color:#F57224">₱ 500 </p>
                                        </div>

                                        <div class="col-4">
                                            <div id="quantity" class="d-flex align-items-center">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                                    @click="reduceQuantity">-</button>
                                                <input type="text" v-model="order.quantity"
                                                    class="col-4 col-md-2 text-center me-2" @input="changeQuantity">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                                    @click="addQuantity">+</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2 p-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <router-link class="btn" to="#" target="_blank" alt="Shop Image">
                                    Gatorade <i class="fa-solid fa-caret-right"></i>
                                </router-link>
                                <i class="fa-solid fa-trash"></i>
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
                                                <p style="margin:0">Gatorade</p>
                                                <p class="card-text fs-5"> Energy Drink </p>
                                            </h1>
                                        </div>

                                        <div class="col-3">
                                            <p class="fs-4 text-start" style="color:#F57224">₱ 500 </p>
                                        </div>

                                        <div class="col-4">
                                            <div id="quantity" class="d-flex align-items-center">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 me-2"
                                                    @click="reduceQuantity">-</button>
                                                <input type="text" v-model="order.quantity"
                                                    class="col-4 col-md-2 text-center me-2" @input="changeQuantity">
                                                <button type="button" class="btn btn-md bg-dark-subtle px-3 ms-2"
                                                    @click="addQuantity">+</button>
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
            </div>
        </div>
    </div>
</template>

<script>
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { formatDate } from '@/helpers/Formatter/index.js';
import defaultProduct from '@/../../public/storage/default_images/product.png';
export default {
    name: 'Cart',
    data() {
        return {
            items: [],
            defaultImage: defaultProduct,
            order: {
                quantity: 1,
                size: 0,
            },
        }
    },

    created() {

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
    }

}
</script>

<style lang="scss" scoped></style>