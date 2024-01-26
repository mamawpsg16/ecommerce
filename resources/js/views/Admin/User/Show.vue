<template>
    <Modal class="modal-lg" targetModal="user-details-modal" modaltitle="User Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="mt-1" id="user-details-form">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="user.name" :class="{ inputInvalidClass: checkInputValidity('user', 'name', ['required']) }" required  autocomplete="user_name" disabled/>
                        <div v-if="v$.user.name.$dirty" :class="{ 'text-danger': checkInputValidity('user', 'name', ['required']) }">
                            <p v-if="v$.user.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-11">
                        <label>Roles <span class="text-danger" v-if="edit">*</span></label>
                        <VueMultiselect :loading="loadingRoles" @select="onSelectedRole"  @remove="onRemoveRole" :disabled="loadingRoles || !edit" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('user', 'roles', ['required']) }" v-model="user.roles" placeholder="Select roles" :options="roles"> </VueMultiselect>
                        <div v-if="v$.user.roles.$dirty"
                            :class="{ 'text-danger': checkInputValidity('user', 'roles', ['required']) }">
                            <p v-if="v$.user.roles.required.$invalid">
                                Role is required.
                            </p>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center mt-3" style="font-size: 25px;" v-if="edit">
                        <input type="checkbox" class="form-check-input" :checked="selectAllRoles" @change="toggleAllRoles()">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-11">
                        <label>Permissions <span class="text-danger" v-if="edit">*</span></label>
                        <VueMultiselect :loading="loadingMenuPermissions" :disabled="loadingMenuPermissions || !edit" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('user', 'menu_permissions', ['required']) }" v-model="user.menu_permissions" placeholder="Select roles" :options="menu_permissions">
                        </VueMultiselect>
                        <div v-if="v$.user.menu_permissions.$dirty"
                            :class="{ 'text-danger': checkInputValidity('user', 'menu_permissions', ['required']) }">
                            <p v-if="v$.user.menu_permissions.required.$invalid">
                                Menu Permission is required.
                            </p>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center mt-3" style="font-size: 25px;" v-if="edit">
                        <input type="checkbox" class="form-check-input" :checked="selectAllMenuPermissions" @change="toggleAllMenuPermissions()">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Status</label>
                        <div class="form-check form-switch d-flex align-items-center" style="margin:0">
                            <input class="form-check-input" type="checkbox" :disabled="!edit" value="" id="example-switch-default1" name="example-switch-default1" :checked="user.active" @change="handleStatusToggle"/>
                            <label class="form-check-label" for="example-switch-default1">
                                <span :class="`btn btn-sm ms-2 px-4 btn-${ user.active ? 'success': 'danger'} btn-xs rounded-pill`">{{ user.active ? "Active" : "Inactive" }}</span>
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
import VueMultiselect from 'vue-multiselect'
    export default {
        name:'User Details',
        props:{
            user_details: [Object, Array],
            user_id: [Number],
            index: [Number],
            menuPermissions:[Object, Array]
        },
        emits: ['updateUser'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                user: {
                    name: null,
                    roles: [],
                    menu_permissions: [],
                },
                selectedRoles:[],
                isSaving: false,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                prevDetails:null,
                roles: [],
                menu_permissions: [],
                loadingRoles: false,
                loadingMenuPermissions: false,
                selectAllRoles:false,
                selectAllMenuPermissions:false,
            }
        },
       
        validations () {
            return {
                user: {
                    name: { required },
                    roles: { required },
                    menu_permissions: { required },
                },
            }
        },
        components:{
            Modal,
            Input,
            VueMultiselect
        },

        async created(){
            await this.getRoles();
        },

        mounted(){
        },

        methods:{
            async getRoles() {
                this.loadingRoles = true;
                await axios.get('/api/admin/get-roles', {
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                    .then((response) => {
                        const { roles } = response.data;
                        this.roles = roles;

                        this.loadingRoles = false;
                    })
                    .catch((error) => {
                        console.log(error, 'error');
                    });
            },

            resetForm() {
            
                // const form = document.querySelector('#user-details-form');
                // if(form){
                //     form.reset();
                // }
                
                this.user = deepClone(this.prevDetails);
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
                const id = document.getElementById('user-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            async getRoleMenuPermissions() {
                this.loadingMenuPermissions = true;
                await axios.get('/api/admin/get-menu-permissions', {
                    headers: {
                        Authorization: this.auth_token
                    },
                    params:{
                        role_ids : this.user.roles.map(role => role.value),
                    }
                })
                    .then((response) => {
                        console.log(response,'response asdasd');
                        const { menu_permissions, sheesh } = response.data;
                        const duplicateMenuPermissions = [...menu_permissions];
                        this.menu_permissions = duplicateMenuPermissions
                        // this.menu_permissions = duplicateMenuPermissions.filter(menu => {
                        //     console.log(menu,'menu');
                        //     console.log(this.user,'this.user');
                        //     const exists = this.user.menu_permissions.find(permission =>{
                        //         console.log(permission,'selected user permissions ');
                        //         console.log(menu,'menu permissions ');
                        //     });
                        //     console.log(exists,'exists');
                           
                        // });

                        this.loadingMenuPermissions = false;
                    })
                    .catch((error) => {
                        console.log(error, 'error');
                    });
            },

            onSelectedRole(selectedOption, id){
                // this.selectedRoles.push(selectedOption.value);

                this.getRoleMenuPermissions();
            },

            onRemoveRole(removedOption, id){
                // const index = this.selectedRoles.findIndex((value) => value == removedOption.value);
                // if (index !== -1) {
                //     this.selectedRoles.splice(index, 1);
                // }

                this.getRoleMenuPermissions();

                const duplicateMenuPermissions = [...this.user.menu_permissions]
                const filterSelectedRoleMenu = duplicateMenuPermissions.filter(menu_permission => menu_permission.role_id != removedOption.value);
                this.user.menu_permissions = filterSelectedRoleMenu;
            },

            toggleAllRoles(){
                this.selectAllRoles =  !this.selectAllRoles;
                const duplicateRoles = [...this.roles];
               this.user.roles = this.selectAllRoles ?  duplicateRoles : [];
                if(!this.selectAllRoles){
                   this.user.menu_permissions = [];
                   this.selectAllMenuPermissions  = false;
                }
                this.getRoleMenuPermissions();
            },

            toggleAllMenuPermissions(){
                this.selectAllMenuPermissions =  !this.selectAllMenuPermissions;
                const duplicateMenuPermissions = [...this.menu_permissions];
                
                this.user.menu_permissions = this.selectAllMenuPermissions ?  duplicateMenuPermissions : [];
            },

            async update(){
                this.isUpdating = true;
                console.log(this.user.menu_permissions,'this.user.menu_permissions');
                let menu_permission_ids = [];
                const data = {
                    ...this.user,
                    roles: this.user.roles.map(role => role.value),
                    menu_permissions: this.user.menu_permissions.map(menu_permission => {
                        menu_permission_ids.push(menu_permission.value);
                        return {
                            user_id:this.user_id,
                            menu_permission_id: menu_permission.value,
                            updated_at: new Date().toISOString().slice(0, 19).replace("T", " ")
                        }
                    }),
                    menu_permission_ids:menu_permission_ids
                }
                
                axios.put(`/api/admin/users/${this.user_id}`, data,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;

                    this.isUpdating = false;

                    this.edit = false;

                    this.prevDetails = deepClone(data);
                    
                    this.$emit('updateUser', this.index, data);

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
                this.user.active = !this.user.active;
            },

        },

        watch:{
            user_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.user_details);
                // this.resetForm();
                this.menu_permissions = this.menuPermissions;

                this.user = this.user_details;
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
            },
        }
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>