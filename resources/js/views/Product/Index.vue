<template>
    <Create @loadUpdatedProducts="getProducts"/>
    <Show @updateProduct="updateProductByIndex" :product_id="product_id" :product_details="product_details" :index="index"/>
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
                    <button type="button" class="btn btn-primary text-end me-2" data-bs-toggle="modal" data-bs-target="#create-product-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.shop_name }}</td>
                        <td>{{ data.description }}</td>
                        <td class="text-end">{{ data.price }}</td>
                        <td class="text-end">{{ data.quantity }}</td>
                        <td>{{ data.categories }}</td>
                        <td>{{ data.status }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewProductDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
import { formatDate, titleCase } from '@/helpers/Formatter/index.js';
import {SwalDefault, swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
import * as XLSX from 'xlsx';

    export default {
        name:'Product Index',
        emits:['loadUpdatedProducts','updateProduct'],
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
                        name:'Shop',
                        field:'shop_name',
                        sort:''
                    },
                    {
                        name:'Description',
                        field:'description',
                        sort:''
                    },
                    {
                        name:'Price',
                        field:'price',
                        sort:''
                    },
                    {
                        name:'Quantity',
                        field:'quantity',
                        sort:''
                    },
                    {
                        name:'Categories',
                        field:'categories',
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
                product_details:null,
                product_id:null,
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
            await this.getProducts();  
        },
        methods:{
            async getProducts(){
                await axios.get('/api/products', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { products } = response.data;

                    const product_details = products.map(product => {
                        return {
                            ...product,
                            status:product.active ? 'Active' : 'Inactive',
                            created_at: formatDate(undefined, product.created_at),
                            updated_at: formatDate(undefined, product.updated_at),
                        }
                    })

                    this.data = product_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewProductDetails(product_id, index){
                const id = document.getElementById('product-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/products/${product_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { data, categories } = response.data;
                    this.product_id = data.id;
                    this.index = index;
                    
                    data.status = data.active ? 'Active' : 'Inactive';
                    data.price = parseFloat(data.price);
                    data.quantity = parseFloat(data.quantity);
                    data.categories = categories;
                    data.shop_name = data.shop.name;
                    data.shop = {label:data.shop.name,  value: data.shop.id};

                    delete data.created_at;
                    delete data.updated_at;

                    this.product_details = data;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateProductByIndex(index, data){
                this.data[index] = {
                            ...data,
                            status: data.active ? 'Active' : 'Inactive',
                            created_at: formatDate(undefined, data.created_at),
                            updated_at: formatDate(undefined, data.updated_at),
                            categories: data.categories.map(category => category.label).join(', ')
                        }
            },

            delete(product_id, index){
                SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Deleting...',
                        text: "Deleting product, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                });
                axios.delete(`/api/products/${product_id}`,{
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

            deleteConfirmation(product_id, index){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.delete(product_id, index)
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