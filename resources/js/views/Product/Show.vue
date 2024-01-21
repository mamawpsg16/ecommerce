<template>
    <Modal class="modal-lg" targetModal="product-details-modal" modaltitle="Product Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="m-3" id="product-details-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultProductImage" class="img-fluid mb-4 rounded" style="height: 280px; width: 280px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center" v-if="image && edit">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 280px;" @change="uploadImage" accept="image/*">
                            <button  type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 ms-auto">
                        <label>Shop <span class="text-danger" v-if="edit">*</span></label>
                        <Input  class="form-control " v-if="!edit" type="text" v-model="product.shop_name" :disabled="!edit"/>
                        <VueMultiselect v-else :loading="loadingShops" :disabled="loadingShops"  track-by="label" label="label"  :class="{ inputInvalidClass : checkInputValidity('product','shop',['required']) }" v-model="product.shop" placeholder="Select a shop"  :options="shops"></VueMultiselect>
                        <div  v-if="v$.product.shop.$dirty" :class="{ 'text-danger': checkInputValidity('product','shop',['required']) }">
                            <p v-if="v$.product.shop.required.$invalid">
                                Shop is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 ms-auto">
                        <label>Product Category <span class="text-danger" v-if="edit">*</span></label>
                        <Input  class="form-control " v-if="!edit" type="text" v-model="edit_categories" :disabled="!edit"/>
                        <VueMultiselect v-else :loading="loadingCategories" :disabled="loadingCategories" :multiple="true"   track-by="label" label="label"  :class="{ inputInvalidClass : checkInputValidity('product','categories',['required']) }" v-model="product.categories" placeholder="Select a category"  :options="categories"></VueMultiselect>
                        <div  v-if="v$.product.categories.$dirty" :class="{ 'text-danger': checkInputValidity('product','categories',['required']) }">
                            <p v-if="v$.product.categories.required.$invalid">
                                Category is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label>Product Name <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text"  class="form-control" v-model="product.name" :disabled="!edit" :class="{ inputInvalidClass : checkInputValidity('product','name',['required']) }" required   autocomplete="product_name" />
                        <div  v-if="v$.product.name.$dirty" :class="{ 'text-danger': checkInputValidity('product','name',['required']) }">
                            <p v-if="v$.product.name.required.$invalid">
                                Product Name is required.
                            </p>
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Product Price <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="number"  class="form-control" step="0.01"  v-model="product.price" :disabled="!edit" :class="{ inputInvalidClass : checkInputValidity('product','price',['required']) }"  required autocomplete="price" />
                        <div  v-if="v$.product.price.$dirty" :class="{ 'text-danger': checkInputValidity('product','price',['required']) }">
                            <p v-if="v$.product.price.required.$invalid">
                                Product Price is required.
                            </p>
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Product Quantity<span class="text-danger" v-if="edit">*</span></label>
                        <Input type="number" step="0.01"   class="form-control" v-model="product.quantity" :disabled="!edit" :class="{ inputInvalidClass : checkInputValidity('product','quantity',['required']) }"  required autocomplete="quantity" />
                        <div  v-if="v$.product.quantity.$dirty" :class="{ 'text-danger':  checkInputValidity('product','quantity',['required']) }">
                            <p v-if="v$.product.quantity.required.$invalid">
                                Product Quantity is required.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label>Product Description</label>
                        <textarea class="form-control" rows="5" v-model="product.description" :disabled="!edit"/>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" v-if="!edit" class="btn btn-md btn-secondary me-1 px-3" @click="closeModal">Close</button>
                    <button type="button"  v-if="!edit" class="btn btn-md btn-primary me-1 px-3" @click="editDetails"><i class="fa-solid fa-pencil"></i></button>
                    <div v-else>
                        <button type="button" class="btn btn-md btn-danger me-1 px-3" @click="cancelEdit">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary me-1 px-3">Update</button>
                    </div>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import VueMultiselect from 'vue-multiselect'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Product Details',
        props:{
            product_details: [Object, Array],
            product_id: [Number],
            index: [Number]
        },
        emits: ['updateProduct'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true ,product: {} }) }
        },
        data(){
            return{
                defaultProductImage: defaultProduct,
                image:null,
                file:null,
                product:{
                    name:null,
                    description:null,
                    quantity:null,
                    price:null,
                    categories:null,
                },
                categories:[],
                edit_categories:null,
                loadingCategories:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                emailExists:false,
                firstStepDisable:false,
                prevDetails:null,
                shops:[],
                loadingShops:false,
            }
        },
       
        validations () {
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
        components:{
            Modal,
            Input,
            VueDatePicker,
            VueMultiselect
        },

        async created(){
            await this.getCategories()
            await this.getShops()
        },

        methods:{
            async getCategories(){
                this.loadingCategories = true;
                await axios.get('/api/get-categories', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { categories } = response.data;
                    this.categories = categories;
                    
                    this.loadingCategories = false;
                })
                .catch((error) =>{
                    console.log(error,'error');
                });
            },

            async getShops(){
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
                .catch((error) =>{
                    console.log(error,'error');
                });
            },

            resetForm() {
                this.image   = null;
                this.file    = null;
                const form = document.querySelector('#shop-details-form');
                if(form){
                    form.reset();
                }
                
                this.product = deepClone(this.prevDetails);
                this.image   = deepClone(this.prevDetails.product_image);
                this.edit_categories = deepClone(this.prevDetails?.categories?.map(category => category.label).join(', '));
                this.v$.$reset();
            },

            editDetails(){
                this.edit = true;
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
            },

            uploadImage(e){
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

            removeImage(){
                this.file = null;
                this.image = this.defaultProductImage;
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            formatData(product){
                    
                product.status = product.active ? 'Active' : 'Inactive';
                product.price = parseFloat(product.price);
                product.quantity = parseFloat(product.quantity);
                this.product = product;
                this.image   = product.product_image; 

                return product;
            },

            closeModal(){
                const id = document.getElementById('product-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            async update(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isUpdating = true;

                const formData = new FormData();

                if(this.file){
                    formData.append('image',this.file);
                }

                const categories = this.product.categories.map(category => category.value);

                const product = {
                    ...this.product,
                    shop_id:this.product.shop.value,
                };
                
                delete product.categories;
                delete product.product_image;
                delete product.status;
                delete product.shop;
           

                formData.append('id', this.product_id);
                formData.append('name', product.name);
                formData.append('description', product.description);
                formData.append('price', product.price);
                formData.append('quantity', product.quantity);
                formData.append('shop_id', product.shop_id);
                formData.append('product', JSON.stringify(product));
                formData.append('categories', JSON.stringify(categories));

                axios.post(`/api/update-product`,formData,{
                    headers:{
                        'Content-Type': 'multipart/form-data',
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data, categories } = response.data;

                    this.isUpdating = false;

                    this.edit = false;

                    const formattedData = this.formatData(data);

                    formattedData.categories = categories;
                    formattedData.shop_name  = data.shop.name;
                    formattedData.shop       =  {label:data.shop.name,  value: data.shop.id};

                    this.prevDetails = deepClone(formattedData);
                    
                    this.$emit('updateProduct', this.index, formattedData);

                    SwalDefault.fire({
                        icon: "success",
                        text: "Succesfully updated!",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    this.resetForm();
                })
                .catch((error) => {
                      this.isUpdating = false;
                    console.log(error);
                });
            },


            updateConfirmation(){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.update()
                    }
                });
            },
        },

        mounted(){
        },

        watch:{
            product_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.product_details);
                // this.resetForm();
                this.product = this.product_details;
                this.image  = this.product_details.product_image; 
                this.edit_categories = this.product_details.categories.map(category => category.label).join(', ');
                immediate:true
            },

            isUpdating(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Updating...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        }
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>