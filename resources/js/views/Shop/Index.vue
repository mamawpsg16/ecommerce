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
                <div id="import">
                    <template v-if="data.length">
                        <button type="button" class="btn btn-secondary text-end me-2" @click="exportCsvStudents">CSV
                            <i class="fa-solid fa-file-export"></i>
                        </button>
                        <button type="button" class="btn btn-secondary text-end me-2" @click="exportExcelStudents">EXCEL
                            <i class="fa-solid fa-file-export"></i>
                        </button>
                    </template>
                    <button type="button" class="btn btn-primary text-end me-2" data-bs-toggle="modal" data-bs-target="#create-shop-modal">
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
import * as XLSX from 'xlsx';

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
                        field:'',
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

            exportExcelStudents(){
                const columns = [...this.columns
                .map(column => {
                    if (column.name !== 'Action') {
                        return {
                            name:column.name,
                            field:column.field,
                        };
                    }
                    return null; // Return null for fields you want to exclude
                })
                .filter(field => field !== null)]; // Filter out null values

                const data = this.data.map(item => {
                    return columns.reduce((obj, column) => {
                            obj[column.field] = item[column.field];
                            return obj;  // Return the accumulated object in each iteration
                    }, {});
                });

                  /* generate worksheet and workbook */
                const worksheet = XLSX.utils.json_to_sheet(data);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Students");

                /* fix headers */
                XLSX.utils.sheet_add_aoa(worksheet, [[...columns.map(column => column.name)]], { origin: "A1" });

                /* calculate column width */
                const column_widths = columns.map(column => {
                    return {
                        wch:20
                    }
                })

                worksheet["!cols"] = column_widths;

                // var wscols = [
                //     {wch: 6}, // "characters"
                //     {wpx: 50}, // "pixels"
                //     ,
                //     {hidden: true} // hide column
                // ];

                // /* At 96 PPI, 1 pt = 1 px */
                // var wsrows = [
                //     {hpt: 12}, // "points"
                //     {hpx: 16}, // "pixels"
                //     ,
                //     {hpx: 24, level:3},
                //     {hidden: true}, // hide row
                //     {hidden: false}
                // ];
                // worksheet["!rows"] = rows;

                /* create an XLSX file and try to save to Presidents.xlsx */
                XLSX.writeFile(workbook, "Student.xlsx", { compression: true });
            },

            exportCsvStudents(){
                const columns = [...this.columns
                .map(column => {
                    if (column.name !== 'Action') {
                        return {
                            name:column.name,
                            field:column.field,
                        };
                    }
                    return null; // Return null for fields you want to exclude
                })
                .filter(field => field !== null)]; // Filter out null values

                const data = this.data.map(item => {
                    return columns.reduce((obj, column) => {
                            obj[column.field] = item[column.field];
                            return obj;  // Return the accumulated object in each iteration
                    }, {});
                });

                  /* generate worksheet and workbook */
                const worksheet = XLSX.utils.json_to_sheet(data);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Students");

                /* fix headers */
                XLSX.utils.sheet_add_aoa(worksheet, [[...columns.map(column => column.name)]], { origin: "A1" });

                /* calculate column width */
                const column_widths = columns.map(column => {
                    return {
                        wch:20
                    }
                })

                worksheet["!cols"] = column_widths;

                /* create an XLSX file and try to save to Presidents.xlsx */
                XLSX.writeFile(workbook, "Student.csv", { compression: true });
            }
        },
    }
</script>

<style scoped>
</style>