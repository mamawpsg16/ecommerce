<template>
    <div class="row bg-secondary-subtle z-2 mb-3 py-2" style="position: sticky; top: 0;">
        <div class="col-12 my-2">
            <div class="d-flex justify-content-between align-items-center" style="position: relative;">
                <router-link class="ms-5 text-dark" :to="{ name: 'home' }">
                    <i class="fa-solid fa-house fs-5  icon-option" alt="home"></i>
                </router-link>
                <router-link class="text-dark" :to="{ name: 'dashboard' }" alt="dashboard">
                    <i class="fa-solid fa-chart-simple fs-3"></i>
                </router-link>
                <VueMultiselect v-model="search" @select="onSelectProduct" @remove="onRemoveProduct" @input="debouncedCheckProductExistence($event)" :loading="isLoading" :preserveSearch="true" :closeOnSelect="false" placeholder="Search..." :options="options" style="width:80%;" class="ms-3"  label="label" track-by="label"></VueMultiselect>
                <div class="d-flex align-items-center z-3 position-relative">
                    <router-link type="button" to="/cart" class="text-dark position-relative">
                        <i class="fa-solid fa-cart-shopping me-2 icon-option"></i>
                        <template v-if="cart.count">
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                                {{ cart.count }}
                            </span>
                        </template>
                    </router-link>


                    <div class="dropdown z-3" id="menu-wrapper">
                        <router-link data-bs-toggle="dropdown" aria-expanded="false" type="button" class="ms-4 me-5 text-dark" to="#">
                            <i class="fa-solid fa-gear icon-option"></i>
                        </router-link>
                        <ul class="dropdown-menu" id="menu-picker">
                            <router-link class="dropdown-item" :to="{ name: 'users' }">Users</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'shop-index' }">Shops</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'product-index' }">Products</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'menus' }">Menus</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'permissions' }">Permissions</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'roles' }">Roles</router-link>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <router-link class="dropdown-item" :to="{ name: 'event-index' }">Events</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'item-index' }">Items</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'registration-index' }">Registration</router-link>
                            <router-link class="dropdown-item" :to="{ name: 'participant-index' }">Participants</router-link>
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
import { useCartStore } from '@/stores/cart.js';
import debounce from 'lodash/debounce';
export default {
    data() {
        return {
            search: null,
            product: useProductStore(),
            cart: useCartStore(),
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

        onRemoveProduct(){
            this.checkProducts([], true);
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

<style lang="scss" scoped>
#menu-wrapper {
  position: relative;
  width: 100%;
}

#menu-picker {
  position: absolute;
  z-index: 1000;
  top: 100%;
  left: 0;
}
</style>