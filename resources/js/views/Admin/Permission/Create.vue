<template>
    <Modal class="modal-md" targetModal="create-permission-modal" modaltitle="Add permission" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="mt-1" id="create-permission-form">
                <div class="row mb-3">
                    <div class="col-12">
                        <label>Permission <span class="text-danger">*</span></label>
                        <Input type="text" v-model="permission.name" :class="{ inputInvalidClass: checkInputValidity('permission', 'name', ['required']) }" required autocomplete="permission_name"  />
                        <div v-if="v$.permission.name.$dirty" :class="{ 'text-danger': checkInputValidity('permission', 'name', ['required']) }">
                            <p v-if="v$.permission.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
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
    name: 'Permission Create',
    setup() {
        return { v$: useVuelidate({ $autoDirty: true }) }
    },
    emits: ['loadUpdatedPermissions'],
    data() {
        return {
            permission: {
                name: null,
            },
            roles:[],
            menus:[],
            isSaving: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
            errors: [{
                name:false,
            }],
        }
    },
    validations() {
        return {
            permission: {
                name: { required },
            },
        }
    },
    components: {
        Modal,
        Input,
    },
    computed: {

    },
   
    methods: {
        checkInputValidity(parentProperty = null, dataProperty, validations = []) {
            return checkValidity(this.v$, parentProperty, dataProperty, validations);
        },

        resetForm() {
          
            this.permission = {
                name: null,
            };
            this.errors = [];
            document.querySelector('#create-permission-form').reset();
            this.v$.$reset();

        },

        async store() {
            this.isSaving = true;

            const permission = {
                ...this.permission,
                slug: generateUniqueSlug(this.permission.name)
            };

            axios.post('/api/admin/permissions', permission, {
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
                        
                        this.$emit('loadUpdatedPermissions');
                        SwalDefault.fire({
                            icon: "success",
                            text: "Permission Successfully Saved.",
                            showConfirmButton: false,
                        });
                        
                        this.errors = [];
                    }
                   
                })
                .catch((error) => {
                    this.isSaving = false;
                    if (error.response.status == 422) {
                        this.errors = response.data.errors;
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