<template>
    <Create @loadUpdatedUsers="getUsers"/>
    <Show @updateUser="updateUserByIndex" :user_id="user_id" :user_details="user_details" :index="index" :menuPermissions="menuPermissions"/>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-between mb-2">
                <div id="export">
                     <!-- <div class="d-flex justify-content-center align-items-center">
                        <select class="form-select" v-model="selectedExportOption" @change="handleExport">
                            <option  value="" disabled selected>Export Options</option>
                            <option  v-for="(option,index) in exportOptions" :value="index" :key="index">{{ option.name }}</option>
                        </select>
                    </div> -->
                </div>
                <div id="import" class="d-flex">
                        <template v-if="data.length">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Export
                                </button>
                                <ul class="dropdown-menu">
                                    <button class="dropdown-item" type="button" @click="handleExport('csv')">CSV</button>
                                    <button class="dropdown-item" type="button" @click="handleExport('xlsx')">EXCEL</button>
                                </ul>
                            </div>
                        </template>
                        <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-user-modal">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td  data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" :data-bs-title="data.roles">{{ data.name }}</td>
                        <td>{{ data.email }}</td>
                        <td>{{ data.status }}</td>
                        <!-- <td>{{ data.roles }}</td> -->
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td>{{ data.updated_by }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewUserDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
                            <!-- <button class="btn btn-sm btn-danger" @click="deleteConfirmation(data.id, index)"><i class="fa-solid fa-trash"></i></button> -->
                        </td>
                    </tr>
                </template>
            </Dataset>
        </div>
    </div>
</template>

<script>
import Create from './Create.vue';
import Show from './Show.vue';
import Modal from '@/components/Modal/modal.vue';
import { formatDate, titleCase } from '@/helpers/Formatter/Date.js';
import {SwalDefault, swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
import { get } from '@/helpers/Export/index.js';
    export default {
        name:'User Index',
        emits:['loadUpdatedUsers','updateUser'],
        data(){
            return{
                isExportAll:false,
                data:[],
                columns:[
                    {
                        name:'Name',
                        field:'name',
                        sort:''
                    },
                    {
                        name:'Email',
                        field:'email',
                        sort:''
                    },
                    {
                        name:'Status',
                        field:'status',
                        sort:''
                    },
                    {
                        name:'Created At',
                        field:'created_at',
                        sort:''
                    },
                    {
                        name:'Updated At',
                        field:'updated_at',
                        sort:''
                    },
                    {
                        name:'Updated By',
                        field:'updated_by',
                        sort:''
                    },
                    {
                        name:'Action',
                        field:'action',
                        sort:''
                    }
                ],
                user_details:null,
                user_id:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
                menuPermissions:[],
                index:null
            }
        },
        components: {
            Dataset,
            Modal,
            Create,
            Show,
            Loading
        },
        async created(){
            await this.getUsers();  
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        },
        methods:{
            async getUsers(){
                await axios.get('/api/admin/users', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { users } = response.data;
                    const user_details = users.map(user => {
                        const roles = user.roles.length ? user.roles.map(role => role.name).join(', ') : 'N/A';
                        
                        return {
                            ...user,
                            roles:roles,
                            created_at: formatDate(undefined, user.created_at),
                            updated_at: formatDate(undefined, user.updated_at),
                            status: user.active ? 'Active' : 'Inactive'
                        }
                    })

                    this.data = user_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewUserDetails(user_id, index){
                const id = document.getElementById('user-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/admin/users/${user_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data, menuPermissions } = response.data;
                    console.log(data, 'data');
                    this.user_id = data.id;
                    this.menuPermissions = menuPermissions;
                    this.index = index;
                    
                    delete data.created_at;
                    delete data.updated_at;

                    this.user_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateUserByIndex(index, data){
                console.log(data,'dataupdatebyindex');
                this.data[index] = {
                            ...data,
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                            status: data.active ? 'Active' : 'Inactive'
                        }
            },

            delete(user_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting user, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/admin/users/${user_id}`,{
                    headers: {
                        Authorization: this.auth_token
                    }
                }).then((response)=>{
                    const { message } = response.data;

                    SwalDefault.close();

                    this.data.splice(index, 1);
                    
                    SwalDefault.fire({
                        icon: "success",
                        text: message,
                        showConfirmButton: false,
                        timer:1500
                    });
                }).catch(error =>{

                });
            },

            deleteConfirmation(user_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(user_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'user', type, this.columns);
            }
        },
    }
</script>

<style scoped>
.custom-tooltip {
  --bs-tooltip-bg: var(--bd-violet-bg);
  --bs-tooltip-color: var(--bs-white);
}
</style>