<template>
    <Create @loadUpdatedEvents="getEvents"/>
    <Show @updateEvent="updateEventByIndex" :event_id="event_id" :event_details="event_details" :index="index" :date="date"/>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-end mb-2">
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
                    <button type="button" class="btn btn-primary text-end ms-3" data-bs-toggle="modal" data-bs-target="#create-event-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.name }}</td>
                        <td>{{ data.description }}</td>
                        <td>{{ data.start_date }}</td>
                        <td>{{ data.end_date }}</td>
                        <td> 
                            <div class="dropdown">
                                <button :class="`btn btn-sm rounded-pill  btn-secondary dropdown-toggle px-4 btn-${data.active ? 'success' : 'danger'}`"  type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ data.status}}
                                </button>
                                <ul class="dropdown-menu fs-sm">
                                    <li>
                                        <a class="dropdown-item" aria-current="true" :active="data.active === status.value" v-for="status in data_status" :key="status.value" type="button" @click="updateStatusConfirmation(data.id, index, status.value)">{{ status.label }}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2" @click="viewEventDetails(data.id, index)"><i class="fa-solid fa-eye"></i></button>
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
        name:'Event Index',
        emits:['loadUpdatedEvents','updateEvent'],
        data(){
            return{
                isExportAll:false,
                data:[],
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
                        name:'Start Date',
                        field:'start_date',
                        sort:''
                    },
                    {
                        name:'End Date',
                        field:'end_date',
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
                event_details:null,
                event_id:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
                index:null,
                date:null,
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
            await this.getEvents();  
        },
        methods:{

            formatData(event){
                const start_date = formatDate(undefined, event.start_date, 'date');
                const end_date = formatDate(undefined, event.end_date, 'date');
                const details = {                            
                        ...event,
                        status:event.active ? 'Active' : 'Inactive',
                        start_date:start_date,
                        end_date:end_date,
                        created_at: formatDateSlash(event.created_at),
                        updated_at: formatDateSlash(event.updated_at),
                        date_range: `${start_date} - ${end_date}`,
                        date: [new Date(event.start_date), new Date(event.end_date)]
                }
                return details;
            },

            async getEvents(){
                await axios.get('/api/raffle/events', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { events } = response.data;

                    const event_details = events.map(event => {
                       return this.formatData(event);
                    })

                    this.data = event_details;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async viewEventDetails(event_id, index){
                const id = document.getElementById('event-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);

                this.isLoading = true;
                this.index = index;
                this.event_id = event_id;

                await axios.get(`api/raffle/events/${event_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { event } = response.data;

                    const constructedDetails = this.formatData(event);
                    console.log(constructedDetails,'constructedDetails');
                    this.date = constructedDetails.date;

                    delete constructedDetails.created_at;
                    delete constructedDetails.updated_at;
                    this.event_details = constructedDetails;

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },

            updateStatusConfirmation(id, index, status) {
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.updateStatus(id, index, status)
                    }
                });
            },

            updateStatus(id, index, status){
                axios.post('api/raffle/event-change-status',{id:id, status:status}, { 
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
                            text: `Event status has been changed to ${status_label}`,
                            showConfirmButton: false,
                        })
                })
            },

            updateEventByIndex(index, data){
                this.data[index] = this.formatData(data);
            },

            handleExport(type){
                get(this.data, 'event', type, this.columns);
            }
        },
    }
</script>

<style scoped>
</style>