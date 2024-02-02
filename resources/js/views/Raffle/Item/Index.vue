<template>
    <Create @loadUpdatedItems="getItems"/>
    <Show @updateItem="updateItemByIndex" :item_id="item_id" :item_details="item_details" :index="index"/>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-end mb-2">
                <div id="import"  class="d-flex">
                    <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-item-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.description }}</td>
                        <td>{{ data.quantity }}</td>
                        <td :style="{ backgroundColor: data.color }"></td>
                        <td>{{ data.chance_rate }}</td>
                        <td>{{ data.order }}</td>
                        <td> 
                            <div class="dropdown" >
                                <button :class="`btn btn-sm rounded-pill  btn-secondary dropdown-toggle px-4 btn-${data.active ? 'success' : 'danger'}`"  type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ data.status}}
                                </button>
                                <ul class="dropdown-menu fs-sm" id="menu-picker">
                                    <li>
                                        <a class="dropdown-item" aria-current="true" :active="data.active === status.value" v-for="status in data_status" :key="status.value" type="button" @click="updateStatusConfirmation(data.id, index, status.value)">{{ status.label }}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewItemDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
import { formatDate, formatDateSlash } from '@/helpers/Formatter/Date.js';
import {SwalDefault, swalConfirmation, swalSuccess } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
import { get } from '@/helpers/Export/index.js';

    export default {
        name:'Item Index',
        emits:['loadUpdatedItems','updateItem'],
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
                        name:'Quantity',
                        field:'quantity',
                        sort:''
                    },
                    {
                        name:'Color',
                        field:'color',
                        sort:''
                    },
                    {
                        name:'Chance Rate',
                        field:'chance_rate',
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
                data_status:[
                    {
                    'label':'Active',
                    'value':1,
                    },
                    {
                    'label':'Inactive',
                    'value':0,
                    },
                ],
                item_details:null,
                item_id:null,
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
            await this.getItems();  
        },
        methods:{
            formatData(data){
                const details = {                            
                        ...data,
                        status:data.active ? 'Active' : 'Inactive',
                        created_at: formatDateSlash(data.created_at),
                        updated_at: formatDateSlash(data.updated_at),
                }
                return details;
            },

            async getItems(){
                await axios.get('/api/raffle/items', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { items } = response.data;

                    const item_details = items.map(item => {
                        return this.formatData(item);
                    })

                    this.data = item_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewItemDetails(item_id, index){
                const id = document.getElementById('item-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/raffle/items/${item_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data } = response.data;
                    this.item_id = data.id;
                    this.index = index;
                    
                    const details = this.formatData(data);
                    delete details.created_at;
                    delete details.updated_at;

                    this.item_details = details;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateItemByIndex(index, data){
                this.data[index] = this.formatData(data);
            },

            updateStatusConfirmation(id, index, status) {
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.updateStatus(id, index, status)
                    }
                });
            },

            updateStatus(id, index, status){
                axios.post('api/raffle/item-change-status',{id:id, status:status}, { 
                    headers: {
                        Authorization: this.auth_token
                    }
                }).then(response =>{
                    this.data[index].active = status;
                    const status_label =  status ? 'Active' : 'Inactive';
                    this.data[index].status = status_label;
                    swalSuccess({ 
                            icon: 'success',
                            title: `Update Success`,
                            text: `Item status has been changed to ${status_label}`,
                            showConfirmButton: false,
                        })
                })
            },

            delete(item_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting item, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/items/${item_id}`,{
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

            deleteConfirmation(item_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(item_id, index)
                    }
                });
            },

            handleExport(type){
                get(this.data, 'item', type, this.columns);
            }
        },
    }
</script>

<style scoped>
#menu-wrapper {
  position: relative;
  width: 100%;
}

#menu-picker {
  position: absolute;
  z-index: 1000;
  top: 100%;
  left: 0;
}
</style>