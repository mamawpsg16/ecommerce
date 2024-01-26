<template>
    <Create @loadUpdatedMenus="getMenus"/>
    <Show @updateMenu="updateMenuByIndex" :menu_id="menu_id" :menu_details="menu_details" :index="index"/>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-menu-modal">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.url }}</td>
                        <td>{{ data.icon }}</td>
                        <td>{{ data.order }}</td>
                        <td>{{ data.status }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.created_by }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td>{{ data.updated_by }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewMenuDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
        name:'Menu Index',
        emits:['loadUpdatedMenus','updateMenu'],
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
                        name:'Url',
                        field:'url',
                        sort:''
                    },
                    {
                        name:'Icon',
                        field:'icon',
                        sort:''
                    },
                    {
                        name:'Order',
                        field:'order',
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
                menu_details:null,
                menu_id:null,
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
            await this.getMenus();  
        },
        methods:{
            async getMenus(){
                await axios.get('/api/admin/menus', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { menus } = response.data;

                    const menu_details = menus.map(menu => {
                        return {
                            ...menu,
                            created_at: formatDate(undefined, menu.created_at),
                            updated_at: formatDate(undefined, menu.updated_at),
                            status: menu.active ? 'Active' : 'Inactive'
                        }
                    })

                    this.data = menu_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewMenuDetails(menu_id, index){
                const id = document.getElementById('menu-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/admin/menus/${menu_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data} = response.data;
                    this.menu_id = data.id;
                    this.index = index;
                    
                    delete data.created_at;
                    delete data.updated_at;
                    delete data.created_by;
                    delete data.updated_by;
                    this.menu_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateMenuByIndex(index, data){
                this.data[index] = {
                            ...data,
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                            status: data.active ? 'Active' : 'Inactive'
                        }
            },

            delete(menu_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting menu, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/admin/menus/${menu_id}`,{
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

            deleteConfirmation(menu_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(menu_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'menu', type, this.columns);
            },

            handleStatusToggle() {
                this.product.active = !this.product.active;
            },
        },
    }
</script>

<style scoped>
</style>