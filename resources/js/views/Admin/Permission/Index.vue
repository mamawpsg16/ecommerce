<template>
    <Create @loadUpdatedPermissions="getPermissions"/>
    <Show @updatePermission="updatePermissionByIndex" :permission_id="permission_id" :permission_details="permission_details" :index="index"/>
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
                        <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-permission-modal">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.status }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.created_by }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td>{{ data.updated_by }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewPermissionDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
        name:'Permission Index',
        emits:['loadUpdatedPermissions','updatePermission'],
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
                permission_details:null,
                permission_id:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
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
            await this.getPermissions();  
        },
        methods:{
            async getPermissions(){
                await axios.get('/api/admin/permissions', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { permissions } = response.data;

                    const permission_details = permissions.map(permission => {
                        return {
                            ...permission,
                            created_at: formatDate(undefined, permission.created_at),
                            updated_at: formatDate(undefined, permission.updated_at),
                            status: permission.active ? 'Active' : 'Inactive'
                        }
                    })

                    this.data = permission_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewPermissionDetails(permission_id, index){
                const id = document.getElementById('permission-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/admin/permissions/${permission_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data} = response.data;
                    this.permission_id = data.id;
                    this.index = index;
                    
                    delete data.created_at;
                    delete data.updated_at;

                    this.permission_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updatePermissionByIndex(index, data){
                this.data[index] = {
                            ...data,
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                            status: data.active ? 'Active' : 'Inactive'
                        }
            },

            delete(permission_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting permission, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/admin/permissions/${permission_id}`,{
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

            deleteConfirmation(permission_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(permission_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'permission', type, this.columns);
            }
        },
    }
</script>

<style scoped>
</style>