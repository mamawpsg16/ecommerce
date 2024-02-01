<template>
    <Modal class="modal-lg" targetModal="create-item-modal" modaltitle="Add Item" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="m-3" id="create-item-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultItemImage" class="img-fluid mb-4 rounded"
                            style="height: 280px; width: 280px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 280px;"
                                @change="uploadImage" accept="image/*">
                            <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i
                                    class="fa-solid fa-trash"></i></button>
                        </div>
                        <div v-if="errors?.image" class="text-danger">
                            {{ errors?.image[0] }}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="item.name" :class="{ inputInvalidClass: checkInputValidity('item', 'name', ['required']) }" required autocomplete="item_name" />
                        <div v-if="v$.item.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('item', 'name', ['required']) }">
                            <p v-if="v$.item.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <Input type="number"  v-model="item.quantity" :class="{ inputInvalidClass: checkInputValidity('item', 'quantity', ['required']) }" required
                            autocomplete="quantity" />
                        <div v-if="v$.item.quantity.$dirty" :class="{ 'text-danger': checkInputValidity('item', 'quantity', ['required']) }">
                            <p v-if="v$.item.quantity.required.$invalid">
                                Quantity is required.
                            </p>
                        </div>
                        <div v-if="errors?.quantity" class="text-danger">
                            {{ errors?.quantity[0] }}
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Chance Rate<span class="text-danger">*</span></label>
                        <Input type="number"  v-model="item.chance_rate" :class="{ inputInvalidClass: checkInputValidity('item', 'chance_rate', ['required']) }" required
                            autocomplete="chance_rate" />
                        <div v-if="v$.item.chance_rate.$dirty" :class="{ 'text-danger': checkInputValidity('item', 'chance_rate', ['required']) }">
                            <p v-if="v$.item.chance_rate.required.$invalid">
                                Chance Rate is required.
                            </p>
                        </div>
                        <div v-if="errors?.chance_rate" class="text-danger">
                            {{ errors?.chance_rate[0] }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label>Description</label>
                        <textarea class="form-control" rows="5" v-model="item.description" />
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
import defaultItem from '@/../../public/storage/default_images/shop.png';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import VueMultiselect from 'vue-multiselect'
import axios from 'axios';
export default {
    name: 'Item Create',
    setup() {
        return { v$: useVuelidate({ $autoDirty: true, student: {} }) }
    },
    emits: ['loadUpdatedItems'],
    data() {
        return {
            defaultItemImage: defaultItem,
            image: null,
            file: null,
            item: {
                name: null,
                description: null,
                quantity: 1,
                chance_rate: 1,
            },
            errors:[{
                    name:false,
                    quantity:false,
                    chance_rate:false,
                }],
            isSaving: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
        }
    },
    validations() {
        return {
            item: {
                name: { required },
                quantity: { required },
                chance_rate: { required },
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
      
    },
    methods: {
        removeImage() {
            this.file = null;
            this.image = this.defaultItemImage;
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
            this.item = {
                name: null,
                description: null,
                quantity: 1,
                chance_rate: 1,
            };
            document.querySelector('#create-item-form').reset();
            this.v$.$reset();
        },

        async store() {        
            this.isSaving = true;

            const formData = new FormData();

            if (this.file) {
                formData.append('item_image', this.file);
            }

            formData.append('name', this.item.name);
            formData.append('description', this.item.description ?? '');
            formData.append('quantity', this.item.quantity);
            formData.append('chance_rate', this.item.chance_rate);

            axios.post('/api/raffle/items', formData, {
                headers: {
                    Authorization: this.auth_token,
                }
            })
                .then((response) => {
                    this.isSaving = false;
                  
                    this.resetForm();

                    this.$emit('loadUpdatedItems');

                    SwalDefault.fire({
                        icon: "success",
                        text: "Item Successfully Saved.",
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