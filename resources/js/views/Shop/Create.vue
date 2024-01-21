<template>
    <Modal class="modal-lg" targetModal="create-shop-modal" modaltitle="Add Shop" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="m-3" id="create-shop-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultShopImage" class="img-fluid mb-4 rounded" style="height: 280px; width: 280px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 280px;" @change="uploadImage" accept="image/*">
                            <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Shop Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="shop.name" :class="{ inputInvalidClass : checkInputValidity('shop','name',['required']) }" required   autocomplete="shop_name" />
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
                        <textarea class="form-control" rows="5" v-model="shop.description"/>
                    </div>
                </div>
                <div class="text-end">
                    <!-- <button type="button" class="btn btn-md btn-secondary me-1 px-3" @click="closeModal">Close</button> -->
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
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import defaultShopImage from '@/../../public/storage/default_images/shop.png';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { generateUniqueSlug } from '@/helpers/PartialHelpers/index.js';
    export default {
        name:'Shop Create',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true ,student: {} }) }
        },
        emits: ['loadUpdatedShops'],
        data(){
            return{
                defaultShopImage: defaultShopImage,
                image:null,
                file:null,
                shop:{
                    name:null,
                    description:null,
                },
                isSaving:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
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
        },

        async created(){
        },
        methods:{
            removeImage(){
                this.file = null;
                this.image = this.defaultShopImage;
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
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

            resetForm(){
                this.image = null;
                this.file = null;
                this.shop = {
                    name:null,
                    description:null,
                };
                document.querySelector('#create-shop-form').reset();
                this.v$.$reset();

            },

            async store(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isSaving = true;

                const formData = new FormData();

                if(this.file){
                    formData.append('image',this.file);
                }

                const shop = {
                    ...this.shop,
                    slug:generateUniqueSlug(this.shop.name)
                };

                formData.append('name', shop.name);
                formData.append('description', shop.description);
                formData.append('shop', JSON.stringify(shop));

                axios.post('/api/shops',formData,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    this.isSaving = false;
                    this.resetForm();

                    this.$emit('loadUpdatedShops');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Shop Successfully Saved.",
                        showConfirmButton: false,
                    });
                })
                .catch((error) => {
                    this.isSaving = false;
                    if(error.response.status == 422){
                        SwalDefault.close();
                        console.log(error.response.data,'error.response.data');
                    }
                });
            },

            storeConfirmation(){
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

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>