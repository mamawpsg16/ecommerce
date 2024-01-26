<template>
    <Modal class="modal-lg" targetModal="create-menu-modal" modaltitle="Add menu" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="mt-1" id="create-menu-form">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="menu.name"
                            :class="{ inputInvalidClass: checkInputValidity('menu', 'name', ['required']) }" required
                            autocomplete="menu_name"  />
                        <div v-if="v$.menu.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('menu', 'name', ['required']) }">
                            <p v-if="v$.menu.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Url <span class="text-danger">*</span></label>
                        <Input type="text" v-model="menu.url"
                            :class="{ inputInvalidClass: checkInputValidity('menu', 'url', ['required']) }" required
                            autocomplete="menu_url"  />
                        <div v-if="v$.menu.url.$dirty"
                            :class="{ 'text-danger': checkInputValidity('menu', 'url', ['required']) }">
                            <p v-if="v$.menu.url.required.$invalid">
                                Url is required.
                            </p>
                        </div>
                        <div v-if="errors?.url" class="text-danger">
                            {{ errors?.url[0] }}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Font Awesome Icon</label>
                        <Input type="text" v-model="menu.icon" autocomplete="icon"  />
                    </div>
                    <div class="col-6">
                        <label>Order <span class="text-danger">*</span></label>
                        <Input type="number" v-model="menu.order"  onkeydown="return event.keyCode !== 69" onpaste="return false;" :class="{ inputInvalidClass: checkInputValidity('menu', 'order', ['required']) }" required autocomplete="menu_order"  />
                        <div v-if="v$.menu.order.$dirty"
                            :class="{ 'text-danger': checkInputValidity('menu', 'order', ['required']) }">
                            <p v-if="v$.menu.order.required.$invalid">
                                Order is required.
                            </p>
                        </div>
                        <div v-if="errors?.order" class="text-danger">
                            {{ errors?.order[0] }}
                        </div>
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
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { generateUniqueSlug } from '@/helpers/PartialHelpers/index.js';
import axios from 'axios';
export default {
    name: 'Menu Create',
    setup() {
        return { v$: useVuelidate({ $autoDirty: true }) }
    },
    emits: ['loadUpdatedMenus'],
    data() {
        return {
            menu: {
                name: null,
                url: null,
                icon: null,
                order: null,
            },
            errors: [{
                name:false,
                url:false,
                order:false,
            }],
            isSaving: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
        }
    },
    validations() {
        return {
            menu: {
                name: { required },
                url: { required },
                order: { required },
            },
         
        }
    },
    components: {
        Modal,
        Input,
    },
    computed: {

    },
    async created() {
    },
    methods: {
        checkInputValidity(parentProperty = null, dataProperty, validations = []) {
            return checkValidity(this.v$, parentProperty, dataProperty, validations);
        },

        resetForm() {
          
            this.menu = {
                name: null,
            };
            this.errors = [];
            document.querySelector('#create-menu-form').reset();
            this.v$.$reset();

        },

        async store() {
            this.isSaving = true;

            const menu = {
                ...this.menu,
                slug: generateUniqueSlug(this.menu.name)
            };

            axios.post('/api/admin/menus', menu, {
                headers: {
                    Authorization: this.auth_token,
                }
            })
                .then((response) => {
                    this.isSaving = false;
                    if(response.data.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                    }else{
                        this.resetForm();
    
                        this.$emit('loadUpdatedMenus');
                        SwalDefault.fire({
                            icon: "success",
                            text: "Menu Successfully Saved.",
                            showConfirmButton: false,
                        });
                        this.errors = [];
                    }
                })
                .catch((error) => {
                    this.isSaving = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
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