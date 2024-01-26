<template>
    <Modal class="modal-lg" targetModal="menu-details-modal" modaltitle="Menu Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="mt-1" id="menu-details-form">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Name <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text" v-model="menu.name" :disabled="!edit" :class="{ inputInvalidClass: checkInputValidity('menu', 'name', ['required']) }" required autocomplete="menu_name"  />
                        <div v-if="v$.menu.name.$dirty" :class="{ 'text-danger': checkInputValidity('menu', 'name', ['required']) }">
                            <p v-if="v$.menu.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Url <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text" v-model="menu.url" :disabled="!edit" :class="{ inputInvalidClass: checkInputValidity('menu', 'url', ['required']) }" required autocomplete="menu_url"  />
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
                        <Input type="text" v-model="menu.icon" autocomplete="icon" :disabled="!edit" />
                    </div>
                    <div class="col-6">
                        <label>Order <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="number" v-model="menu.order" :disabled="!edit"  onkeydown="return event.keyCode !== 69" onpaste="return false;" :class="{ inputInvalidClass: checkInputValidity('menu', 'order', ['required']) }" required autocomplete="menu_order"  />
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
                <div class="row">
                    <div class="col-md-6">
                        <label>Status</label>
                        <div class="form-check form-switch d-flex align-items-center" style="margin:0">
                            <input class="form-check-input" type="checkbox" :disabled="!edit" value="" id="example-switch-default1" name="example-switch-default1" :checked="menu.active" @change="handleStatusToggle"/>
                            <label class="form-check-label" for="example-switch-default1">
                                <span :class="`btn btn-sm ms-2 px-4 btn-${ menu.active ? 'success': 'danger'} btn-xs rounded-pill`">{{ menu.active ? "Active" : "Inactive" }}</span>
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
        name:'Menu Details',
        props:{
            menu_details: [Object, Array],
            menu_id: [Number],
            index: [Number]
        },
        emits: ['updateMenu'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                menu: {
                    name: null,
                    url: null,
                    icon: null,
                    order: null,
                    active: null,
                },
                isSaving: false,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                prevDetails:null,
                errors: [{
                    name:false,
                    url:false,
                    order:false,
                }],
            }
        },
       
        validations () {
            return {
                menu: {
                    name: { required },
                    url: { required },
                    order: { required },
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
            
                const form = document.querySelector('#menu-details-form');
                if(form){
                    form.reset();
                }
                
                this.menu = deepClone(this.prevDetails);
                this.errors = [];
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
                const id = document.getElementById('menu-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            handleStatusToggle() {
                this.menu.active = !this.menu.active;
            },

            async update(){
                this.isUpdating = true;

                axios.put(`/api/admin/menus/${this.menu_id}`, this.menu,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;
                    console.log(response);
                    this.isUpdating = false;

                    if(response.data.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                    }else{
                        this.edit = false;
    
                        this.prevDetails = deepClone(data);
                        
                        this.$emit('updateMenu', this.index, data);
    
                        SwalDefault.fire({
                            icon: "success",
                            text: "Succesfully updated!",
                            showConfirmButton: false,
                            timer: 1500
                        });
    
                        this.resetForm();    
                        this.errors = [];
                    }


                })
                .catch((error) => {
                    this.isUpdating = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
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
        },

        mounted(){
        },

        watch:{
            menu_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.menu_details);
                // this.resetForm();
                this.menu = this.menu_details;
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