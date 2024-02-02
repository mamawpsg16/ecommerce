<template>
    <Create @loadUpdatedRoles="getRoles"/>
    <Show @updateRole="updateRoleByIndex" :role_id="role_id" :role_details="role_details" :index="index"/>
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
                        <!-- <template v-if="data.length">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Export
                                </button>
                                <ul class="dropdown-menu">
                                    <button class="dropdown-item" type="button" @click="handleExport('csv')">CSV</button>
                                    <button class="dropdown-item" type="button" @click="handleExport('xlsx')">EXCEL</button>
                                </ul>
                            </div>
                        </template> -->
                        <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-role-modal">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.created_by }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td>{{ data.updated_by }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewRoleDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
        name:'Role Index',
        emits:['loadUpdatedRoles','updateRole'],
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
                        name:'Created At',
                        field:'created_at',
                        sort:''
                    },
                    {
                        name:'Created By',
                        field:'created_by',
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
                role_details:null,
                role_id:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
                index:null,
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
            await this.getRoles();  
        },
        methods:{
            async getRoles(){
                await axios.get('/api/admin/roles', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { roles } = response.data;

                    const role_details = roles.map(role => {
                        return {
                            ...role,
                            created_at: formatDate(undefined, role.created_at),
                            updated_at: formatDate(undefined, role.updated_at),
                        }
                    })

                    this.data = role_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewRoleDetails(role_id, index){
                const id = document.getElementById('role-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/admin/roles/${role_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data, menu_permissions, menu_ids, menu_permission_ids} = response.data;
                    console.log(menu_permissions, 'menu_permissions');
                    this.role_id = data.id;
                    this.index = index;
                    
                    delete data.created_at;
                    delete data.updated_at;

                    data.menu_permissions = menu_permissions;
                    data.menu_ids = menu_ids;
                    data.menu_permission_ids = menu_permission_ids;
                    this.role_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateRoleByIndex(index, data){
                this.data[index] = {
                            ...data,
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                        }
            },

            delete(role_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting role, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/admin/roles/${role_id}`,{
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

            deleteConfirmation(role_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(role_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'role', type, this.columns);
            }
        },
    }
</script>

<style scoped>
</style>