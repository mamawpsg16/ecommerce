<template>
    <div class="row mt-3">
        <div class="col-12 my-2">
            <div class="d-flex justify-content-between align-items-center">
                <router-link class="ms-5 me-5 text-dark" :to="{ name: 'home' }">
                    <i class="fa-solid fa-house fs-5  icon-option" alt="home"></i>
                </router-link>
                <VueMultiselect v-model="search" @select="onSelectProduct" @input="debouncedCheckProductExistence($event)" :loading="isLoading" :preserveSearch="true" :closeOnSelect="false" placeholder="Search..." :options="options" style="width:80%;" class="me-4"  label="label" track-by="label"></VueMultiselect>
                <div class="d-flex align-items-center">
                    <router-link type="button" to="/cart" class="ms-4 text-dark position-relative">
                        <i class="fa-solid fa-cart-shopping me-2 icon-option"></i>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                            5 <!-- Put your badge count here -->
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </router-link>

                    <div class="dropdown  me-5">
                        <router-link data-bs-toggle="dropdown" aria-expanded="false" type="button" class="ms-4 me-5 text-dark" to="#">
                            <i class="fa-solid fa-gear icon-option"></i>
                        </router-link>
                        <ul class="dropdown-menu">
                            <router-link class="dropdown-item" :to="{ name: 'registration-index' }">Registration</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'shop-index' }">Shop</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'product-index' }">Product</router-link>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <button class="dropdown-item" type="button" @click="logoutConfirmation">Logout</button>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import VueMultiselect from 'vue-multiselect'
import { useProductStore } from '@/stores/search.js';
import debounce from 'lodash/debounce';
export default {
    data() {
        return {
            search: null,
            product: useProductStore(),
            token: localStorage.getItem('auth-token'),
            options: [],
            items: [],
            isLoading:false,
        }
    },
    components: {
        VueMultiselect,
    },
    methods: {
        debouncedCheckProductExistence: debounce(function (event) {
            this.checkProducts(event.target.value)
        }, 500),

        incrementCount() {
            this.product.increment();
            this.product.doubleCount;
        },

        // Function to check email existence (replace with your actual implementation)
        async checkProducts(value, isSelect = false) {
            this.isLoading = true;
            const search = value;
            try {
                axios.get('/api/search-product-existence', {
                    headers: {
                        Authorization: `Bearer ${this.token}`
                    },
                    params: {
                        search: search
                    }
                }).then((response) => {
                    const { products } = response.data;


                    this.options = products.map(product =>{
                        return {
                            label:product.name,
                            value:product.id,
                        }
                    });
                    this.isLoading = false;

                    console.log(isSelect);
                    if(isSelect){
                        this.product.setResults(products)

                        if(this.$route.name != 'home'){
                            this.$router.replace({ name: 'home' });
                        }
                    }
                }).catch((error) => {
                    this.isLoading = false;
                    console.log(error);
                })
                // const response = await axios.post('/api/search-product-existence', {search: this.search},{   headers: {
                //     Authorization: this.auth_token
                // }}
                // );

                // const { options } = response.data;

                // Update the component state with the result
                // this.options = exists;
            } catch (error) {
                // Handle errors
                console.error('Error checking email existence:', error);
            }
        },

        async onSelectProduct(){
            await this.checkProducts(this.search.label, true);
        },

        logoutConfirmation() {
            swalConfirmation({
                title: "Are you sure?",
                text: "Want to logout?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.logout()
                }
            });
        },

        logout() {
            axios.post('/api/logout', null, {
                headers: {
                    Authorization: `Bearer ${this.token}`
                }
            }).then((response) => {
                localStorage.removeItem('auth-token');
                this.$router.push({ name: 'login' });
            }).catch((error) => {
                console.log(error);
            })
        }
    }
}
</script>

<style lang="scss" scoped></style>