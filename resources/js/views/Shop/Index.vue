<template>
    <Create @loadUpdatedShops="getShops"/>
    <Show @updateShop="updateShopByIndex" :shop_id="shop_id" :shop_details="shop_details" :index="index"/>
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
                    <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-shop-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.description }}</td>
                        <td>{{ data.status }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td class="text-center">
                            <router-link :to="`/shop/${data.slug}`" target="_blank" type="button" class="btn btn-sm btn-success me-2"><i class="fa-solid fa-store"></i></router-link> 
                            <button class="btn btn-sm btn-primary me-2" @click="viewShopDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
                            <button class="btn btn-sm btn-danger" @click="deleteConfirmation(data.id, index)"><i class="fa-solid fa-trash"></i></button>
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
import { formatDate } from '@/helpers/Formatter/Date.js';
import {SwalDefault, swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
import { get } from '@/helpers/Export/index.js';

    export default {
        name:'Shop Index',
        emits:['loadUpdatedShops','updateShop'],
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
                        name:'Description',
                        field:'description',
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
                        name:'Action',
                        field:'action',
                        sort:''
                    }
                ],
                shop_details:null,
                shop_id:null,
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
            await this.getShops();  
        },
        methods:{
            async getShops(){
                await axios.get('/api/shops', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { shops } = response.data;

                    const shop_details = shops.map(shop => {
                        return {
                            ...shop,
                            status:shop.active ? 'Active' : 'Inactive',
                            created_at: formatDate(undefined, shop.created_at),
                            updated_at: formatDate(undefined, shop.updated_at),
                        }
                    })

                    this.data = shop_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewShopDetails(shop_id, index){
                const id = document.getElementById('shop-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);

                this.isLoading = true;
                this.index = index;

                await axios.get(`/api/shops/${shop_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data } = response.data;
                    this.shop_id = data.id;
                    
                    data.status = data.active ? 'Active' : 'Inactive';

                    delete data.created_at;
                    delete data.updated_at;

                    this.shop_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateShopByIndex(index, data){
                this.data[index] = {
                            ...data,
                            status: data.active ? 'Active' : 'Inactive',
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                        }
            },

            delete(shop_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting shop, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/shops/${shop_id}`,{
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

            deleteConfirmation(shop_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(shop_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'shop', type, this.columns);
            }
        },
    }
</script>

<style scoped>
</style>