<template>
    <Modal class="modal-lg" targetModal="shop-details-modal" modaltitle="Shop Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="m-3" id="shop-details-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultShopImage" class="img-fluid mb-4 rounded" style="height: 250px; width: 250px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center" v-if="image && edit">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 250px;" @change="uploadImage" accept="image/*">
                            <button  type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Shop Name <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text"  class="form-control" v-model="shop.name" :disabled="!edit" :class="{ inputInvalidClass : checkInputValidity('shop','name',['required']) }" required   autocomplete="shop_name" />
                        <div  v-if="v$.shop.name.$dirty" :class="{ 'text-danger': checkInputValidity('shop','name',['required']) }">
                            <p v-if="v$.shop.name.required.$invalid">
                                Shop Name is required.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label>Shop Description</label>
                        <textarea class="form-control" rows="5" v-model="shop.description" :disabled="!edit"/>
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
import defaultShop from '@/../../public/storage/default_images/shop.png';

import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Shop Details',
        props:{
            shop_details: [Object, Array],
            shop_id: [Number],
            index: [Number]
        },
        emits: ['updateShop'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                defaultShopImage: defaultShop,
                image:null,
                file:null,
                shop:{
                    name:null,
                    description:null,
                },
                categories:[],
                edit_categories:null,
                loadingCategories:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                emailExists:false,
                firstStepDisable:false,
                prevDetails:null
            }
        },
       
        validations () {
            return {
                shop: {
                    name: { required },
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
        },

        methods:{
            resetForm() {
                this.image   = null;
                this.file    = null;
                const form = document.querySelector('#shop-details-form');
                if(form){
                    form.reset();
                }
                this.shop = deepClone(this.prevDetails);
                this.image   = deepClone(this.prevDetails.shop_image);
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
                this.image = this.defaultShopImage;
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            formatData(shop){
                    
                shop.status = shop.active ? 'Active' : 'Inactive';
                this.shop = shop;
                this.image   = shop.shop_image; 

                return shop;
            },

            closeModal(){
                const id = document.getElementById('shop-details-modal');
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

                const shop = {
                    ...this.shop,
                };

                delete shop.shop_image;
                delete shop.status;
           

                formData.append('id', this.shop_id);
                formData.append('name', shop.name);
                formData.append('description', shop.description);
                formData.append('shop', JSON.stringify(shop));


                axios.post(`/api/update-shop`,formData,{
                    headers:{
                        'Content-Type': 'multipart/form-data',
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;

                    this.isUpdating = false;

                    this.edit = false;

                    const formattedData = this.formatData(data);

                    this.prevDetails = deepClone(formattedData);

                    this.$emit('updateShop', this.index, formattedData);

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
            shop_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.shop_details);
                // this.resetForm();
                this.shop = this.shop_details;
                this.image  = this.shop_details.shop_image; 
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