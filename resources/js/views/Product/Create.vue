<template>
    <Modal class="modal-lg" targetModal="create-product-modal" modaltitle="Add Product" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="m-3" id="create-product-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultProductImage" class="img-fluid mb-4 rounded"
                            style="height: 280px; width: 280px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 280px;"
                                @change="uploadImage" accept="image/*">
                            <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i
                                    class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row mt-5 mb-3">
                    <div class="col-6 ms-auto">
                        <label>Shop <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingShops" :disabled="loadingShops" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('product', 'shop', ['required']) }" v-model="product.shop" placeholder="Select a shop" :options="shops"></VueMultiselect>
                        <div v-if="v$.product.shop.$dirty"
                            :class="{ 'text-danger': checkInputValidity('product', 'shop', ['required']) }">
                            <p v-if="v$.product.shop.required.$invalid">
                                Shop is required.
                            </p>
                        </div>   
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 ms-auto">
                        <label>Product Category <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingCategories" :disabled="loadingCategories" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('product', 'categories', ['required']) }" v-model="product.categories" placeholder="Select a category" :options="categories">
                        </VueMultiselect>
                        <div v-if="v$.product.categories.$dirty"
                            :class="{ 'text-danger': checkInputValidity('product', 'categories', ['required']) }">
                            <p v-if="v$.product.categories.required.$invalid">
                                Category is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label>Product Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="product.name" :class="{ inputInvalidClass: checkInputValidity('product', 'name', ['required']) }" required autocomplete="product_name" />
                        <div v-if="v$.product.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('product', 'name', ['required']) }">
                            <p v-if="v$.product.name.required.$invalid">
                                Product Name is required.
                            </p>
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Product Price <span class="text-danger">*</span></label>
                        <Input type="number" step="0.01" v-model="product.price" :class="{ inputInvalidClass: checkInputValidity('product', 'price', ['required']) }" required
                            autocomplete="price" />
                        <div v-if="v$.product.price.$dirty" :class="{ 'text-danger': checkInputValidity('product', 'price', ['required']) }">
                            <p v-if="v$.product.price.required.$invalid">
                                Product Price is required.
                            </p>
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Product Quantity<span class="text-danger">*</span></label>
                        <Input type="number" step="0.01" v-model="product.quantity" :class="{ inputInvalidClass: checkInputValidity('product', 'quantity', ['required']) }" required
                            autocomplete="quantity" />
                        <div v-if="v$.product.quantity.$dirty" :class="{ 'text-danger': checkInputValidity('product', 'quantity', ['required']) }">
                            <p v-if="v$.product.quantity.required.$invalid">
                                Product Quantity is required.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label>Product Description</label>
                        <textarea class="form-control" rows="5" v-model="product.description" />
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-md btn-secondary me-1 px-3" @click="resetForm" alt="reset"><i class="fa-solid fa-rotate-left"></i></button>
                    <button type="submit" class="btn btn-primary btn btn-md btn-primary me-1 px-5">Save</button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
// import FloatingInput from '@/components/Form/FloatingInput.vue'
import Input from '@/components/Form/Input.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { generateUniqueSlug } from '@/helpers/PartialHelpers/index.js';
import VueMultiselect from 'vue-multiselect'
import axios from 'axios';
export default {
    name: 'Product Create',
    setup() {
        return { v$: useVuelidate({ $autoDirty: true, student: {} }) }
    },
    emits: ['loadUpdatedProducts'],
    data() {
        return {
            defaultProductImage: defaultProduct,
            image: null,
            file: null,
            product: {
                name: null,
                description: null,
                quantity: null,
                price: null,
                categories: null,
                shop: null,
            },
            isSaving: false,
            categories: [],
            shops: [],
            loadingCategories: false,
            loadingShops: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
        }
    },
    validations() {
        return {
            product: {
                name: { required },
                quantity: { required },
                price: { required },
                categories: { required },
                shop: { required },
            },
        }
    },
    components: {
        Modal,
        Input,
        VueDatePicker,
        VueMultiselect
    },
    computed: {

    },
    async created() {
        await this.getCategories()
        await this.getShops()
    },
    methods: {
        async getCategories() {
            this.loadingCategories = true;
            await axios.get('/api/admin/get-categories', {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { categories } = response.data;
                    this.categories = categories;

                    this.loadingCategories = false;
                })
                .catch((error) => {
                    console.log(error, 'error');
                });
        },

        async getShops() {
            this.loadingShops = true;
            await axios.get('/api/get-shops', {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { shops } = response.data;
                    this.shops = shops;

                    this.loadingShops = false;
                })
                .catch((error) => {
                    console.log(error, 'error');
                });
        },

        removeImage() {
            this.file = null;
            this.image = this.defaultProductImage;
        },

        checkInputValidity(parentProperty = null, dataProperty, validations = []) {
            return checkValidity(this.v$, parentProperty, dataProperty, validations);
        },

        uploadImage(e) {
            e.preventDefault()
            const file = e.target.files[0];
            this.file = file;
            if (file) {
                // Use FileReader to read the file as a data URL
                const reader = new FileReader();
                reader.onload = () => {
                    this.image = reader.result; // Set the imageUrl to the data URL
                };
                reader.readAsDataURL(file);
            }
        },

        resetForm() {
            this.image = null;
            this.file = null;
            this.product = {
                name: null,
                description: null,
                quantity: null,
                price: null,
                categories: null,
                shop: null,
            };
            document.querySelector('#create-product-form').reset();
            this.v$.$reset();

        },

        async store() {        
            this.isSaving = true;

            const formData = new FormData();

            if (this.file) {
                formData.append('image', this.file);
            }

            const categories = this.product.categories.map(category => category.value);

            const product = {
                ...this.product,
                shop_id: this.product.shop.value,
                slug: generateUniqueSlug(this.product.name)
            };

            delete product.categories;
            delete product.shop;


            formData.append('name', product.name);
            formData.append('description', product.description);
            formData.append('price', product.price);
            formData.append('quantity', product.quantity);
            formData.append('shop_id', product.shop_id);
            formData.append('product', JSON.stringify(product));
            formData.append('categories', JSON.stringify(categories));

            axios.post('/api/products', formData, {
                headers: {
                    Authorization: this.auth_token,
                }
            })
                .then((response) => {
                    this.isSaving = false;
                  
                    this.resetForm();

                    this.$emit('loadUpdatedProducts');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Product Successfully Saved.",
                        showConfirmButton: false,
                    });
                })
                .catch((error) => {
                    this.isSaving = false;
                    if (error.response.status == 422) {
                        this.errors =  error.response.data.errors;
                        SwalDefault.close();
                    }
                });
        },

        async storeConfirmation() {
            if (!await this.v$.$validate()) {
                return;
            }
            swalConfirmation().then((result) => {
                if (result.isConfirmed) {
                    this.store()
                }
            });
        },
    },

    watch: {
        isSaving(newValue, oldValue) {
            if (newValue) {
                SwalDefault.fire({
                    title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Saving...',
                    text: "Saving changes, kindly wait.",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }
        }
    },
}
</script>

<style scoped>.inputInvalidClass {
    border: 1px solid red;
    /* Adjust the style as needed */
    border-radius: 5px;
}</style>