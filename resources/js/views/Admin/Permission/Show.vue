<template>
    <Modal class="modal-md" targetModal="permission-details-modal" modaltitle="Permission Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="mt-1" id="permission-details-form">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="permission.name"
                            :class="{ inputInvalidClass: checkInputValidity('permission', 'name', ['required']) }" required
                            autocomplete="permission_name" :disabled="!edit"/>
                        <div v-if="v$.permission.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('permission', 'name', ['required']) }">
                            <p v-if="v$.permission.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Status</label>
                        <div class="form-check form-switch d-flex align-items-center" style="margin:0">
                            <input class="form-check-input" type="checkbox" :disabled="!edit" value="" id="example-switch-default1" name="example-switch-default1" :checked="permission.active" @change="handleStatusToggle"/>
                            <label class="form-check-label" for="example-switch-default1">
                                <span :class="`btn btn-sm ms-2 px-4 btn-${ permission.active ? 'success': 'danger'} btn-xs rounded-pill`">{{ permission.active ? "Active" : "Inactive" }}</span>
                            </label>
                        </div>
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
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Permission Details',
        props:{
            permission_details: [Object, Array],
            permission_id: [Number],
            index: [Number]
        },
        emits: ['updatePermission'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                permission: {
                    name: null,
                },
                isSaving: false,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                prevDetails:null,
            }
        },
       
        validations () {
            return {
                permission: {
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
            resetForm() {
            
                const form = document.querySelector('#permission-details-form');
                if(form){
                    form.reset();
                }
                
                this.permission = deepClone(this.prevDetails);
                this.v$.$reset();
            },

            editDetails(){
                this.edit = true;
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
            },


            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },


            closeModal(){
                const id = document.getElementById('permission-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            async update(){
                this.isUpdating = true;
                axios.put(`/api/admin/permissions/${this.permission_id}`, this.permission,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;

                    this.isUpdating = false;

                    this.edit = false;

                    this.prevDetails = deepClone(data);
                    
                    this.$emit('updatePermission', this.index, data);

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


            async updateConfirmation(){
                if(!await this.v$.$validate()){
                    return;
                }
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.update()
                    }
                });
            },

            handleStatusToggle() {
                this.permission.active = !this.permission.active;
            },

        },

        mounted(){
        },

        watch:{
            permission_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.permission_details);
                // this.resetForm();
                this.permission = this.permission_details;
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