<template>
    <Modal class="modal-lg" targetModal="create-user-modal" modaltitle="Create Roles" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="mt-1" id="user-roles-form">
                <div class="row mb-3">
                    <div class="col-11">
                        <label>Users <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingUsers" :disabled="loadingUsers" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('data', 'users', ['required']) }" v-model="data.users" placeholder="Select datas" :options="users"> </VueMultiselect>
                        <div v-if="v$.data.users.$dirty"
                            :class="{ 'text-danger': checkInputValidity('data', 'users', ['required']) }">
                            <p v-if="v$.data.users.required.$invalid">
                                User is required.
                            </p>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center mt-3" style="font-size: 25px;">
                        <input type="checkbox" class="form-check-input" :checked="selectAllUsers" @change="toggleAllUsers()"> 
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-11">
                        <label>Roles <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingRoles" @select="onSelectedRole"  @remove="onRemoveRole" :disabled="loadingRoles" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('data', 'roles', ['required']) }" v-model="data.roles" placeholder="Select roles" :options="roles"> </VueMultiselect>
                        <div v-if="v$.data.roles.$dirty"
                            :class="{ 'text-danger': checkInputValidity('data', 'roles', ['required']) }">
                            <p v-if="v$.data.roles.required.$invalid">
                                Role is required.
                            </p>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center mt-3" style="font-size: 25px;">
                        <input type="checkbox" class="form-check-input" :checked="selectAllRoles" @change="toggleAllRoles()">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-11">
                        <label>Permissions <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingMenuPermissions" :disabled="loadingMenuPermissions" :multiple="true" track-by="label" label="label" :class="{ inputInvalidClass: checkInputValidity('data', 'menu_permissions', ['required']) }" v-model="data.menu_permissions" placeholder="Select roles" :options="menu_permissions">
                        </VueMultiselect>
                        <div v-if="v$.data.menu_permissions.$dirty"
                            :class="{ 'text-danger': checkInputValidity('data', 'menu_permissions', ['required']) }">
                            <p v-if="v$.data.menu_permissions.required.$invalid">
                                Menu Permission is required.
                            </p>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center mt-3" style="font-size: 25px;">
                        <input type="checkbox" class="form-check-input" :checked="selectAllMenuPermissions" @change="toggleAllMenuPermissions()">
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
        emits: ['loadUpdatedUsers'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                data: {
                    users: [],
                    roles: [],
                    menu_permissions: [],
                },
                selectedRoles:[],
                isSaving: false,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isSaving:false,
                prevDetails:null,
                roles: [],
                users: [],
                menu_permissions: [],
                loadingRoles: false,
                loadingUsers: false,
                loadingMenuPermissions: false,
                selectAllUsers:false,
                selectAllRoles:false,
                selectAllMenuPermissions:false,
            }
        },
       
        validations () {
            return {
                data: {
                    users: { required },
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
            await this.getUsers();
            await this.getRoles();
        },

        mounted(){
        },

        methods:{
            async getUsers() {
                this.loadingUsers = true;
                await axios.get('/api/admin/get-users', {
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                    .then((response) => {
                        const { users } = response.data;
                        this.users = users;

                        this.loadingUsers = false;
                    })
                    .catch((error) => {
                        console.log(error, 'error');
                    });
            },

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
            
                // const form = document.querySelector('#user-roles-form');
                // if(form){
                //     form.reset();
                // }
                this.data = {
                    users: [],
                    roles: [],
                    menu_permissions: [],
                },

                this.selectAllUsers = [];
                this.selectAllRoles =  [];
                this.selectAllMenuPermissions = [];

                this.v$.$reset();
            },

            toggleAllUsers(){
                this.selectAllUsers =  !this.selectAllUsers;
                const duplicateUsers = [...this.users];
                
                this.data.users = this.selectAllUsers ?  duplicateUsers : [];
            },

            toggleAllRoles(){
                this.selectAllRoles =  !this.selectAllRoles;
                const duplicateRoles = [...this.roles];
                this.data.roles = this.selectAllRoles ?  duplicateRoles : [];
                if(!this.selectAllRoles){
                    this.data.menu_permissions = [];
                    this.selectAllMenuPermissions  = false;
                }
                this.getRoleMenuPermissions();
            },

            toggleAllMenuPermissions(){
                this.selectAllMenuPermissions =  !this.selectAllMenuPermissions;
                const duplicateMenuPermissions = [...this.menu_permissions];
                
                this.data.menu_permissions = this.selectAllMenuPermissions ?  duplicateMenuPermissions : [];
            },


            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            async getRoleMenuPermissions() {
                this.loadingMenuPermissions = true;
                await axios.get('/api/admin/get-menu-permissions', {
                    headers: {
                        Authorization: this.auth_token
                    },
                    params:{
                        role_ids : this.data.roles.map(role => role.value),
                    }
                })
                    .then((response) => {
                        console.log(response,'response asdasd');
                        const { menu_permissions } = response.data;

                        const duplicateMenuPermissions = [...menu_permissions];

                        this.menu_permissions = duplicateMenuPermissions

                        this.loadingMenuPermissions = false;
                    })
                    .catch((error) => {
                        console.log(error, 'error');
                    });
            },

            onSelectedRole(selectedOption, id){
                this.getRoleMenuPermissions();
            },

            onRemoveRole(removedOption, id){
                this.getRoleMenuPermissions();

                const duplicateMenuPermissions = [...this.data.menu_permissions]
                const filterSelectedRoleMenu = duplicateMenuPermissions.filter(menu_permission => menu_permission.role_id != removedOption.value);
                this.data.menu_permissions = filterSelectedRoleMenu;
            },

            async create(){
                this.isSaving = true;
                console.log(this.data.menu_permissions,'this.data.menu_permissions');
                let menu_permission_ids = [];
                const data = {
                    ...this.user,
                    users: this.data.users.map(user => user.value),
                    roles: this.data.roles.map(role => role.value),
                    menu_permissions: this.data.menu_permissions.map(menu_permission => {
                        menu_permission_ids.push(menu_permission.value);
                        return {
                            user_id:this.user_id,
                            menu_permission_id: menu_permission.value,
                            created_at: new Date().toISOString().slice(0, 19).replace("T", " ")
                        }
                    }),
                    menu_permission_ids:menu_permission_ids
                }
                
                axios.post(`/api/admin/users`, data,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { message } = response.data;

                    this.isSaving = false;

                    this.$emit('loadUpdatedUsers');

                    SwalDefault.fire({
                        icon: "success",
                        text: "Succesfully created!",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    this.resetForm();
                })
                .catch((error) => {
                      this.isSaving = false;
                    console.log(error);
                });
            },


            async storeConfirmation(){
                if(!await this.v$.$validate()){
                    return;
                }
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.create()
                    }
                });
            },

            handleStatusToggle() {
                this.data.active = !this.data.active;
            },

        },

        watch:{
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