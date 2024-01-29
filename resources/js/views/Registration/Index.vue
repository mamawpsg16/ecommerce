<template>
    <Rouelette :items="items"/>
    <Create @loadUpdatedParticipants="getparticipants"/>
    <!-- <Show @loadUpdatedparticipants="participants" :participant_details="participant_details"/> -->
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" :opacity="0.4" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="d-flex justify-content-end mb-2">
                <div id="import">
                    <button type="button" class="btn btn-primary text-end me-2" data-bs-toggle="modal" data-bs-target="#participant-registration-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div>
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.event }}</td>
                        <td>{{ data.name }}</td>
                        <td>{{ data.email }}</td>
                        <td>{{ data.birth_date }}</td>
                        <td>{{ data.personal_phone_number }}</td>
                        <td>{{ data.industry }}</td>
                        <td>{{ data.company }}</td>
                        <td>{{ data.position }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success me-2" @click="playRoulette(data.id)"><i class="fa-solid fa-gamepad"></i></button>
                            <button class="btn btn-sm btn-primary me-2" @click="viewparticipantDetails(data.id)"><i class="fa-solid fa-eye"></i></button>
                        </td>
                    </tr>
                </template>
            </Dataset>
        </div>
    </div>
</template>

<script>
import Create from './Create.vue';
import Rouelette from './Components/WheelOfFortune.vue';
import Show from './Show.vue';
import Modal from '@/components/Modal/modal.vue';
import { formatDate, titleCase } from '@/helpers/Formatter/Date.js';
import {SwalDefault, swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
    export default {
        name:'Registration Index',
        data(){
            return{
                isExportAll:false,
                data:[],
                playGame:false,
                columns:[
                    {
                        name:'Event',
                        field:'event',
                        sort:''
                    },
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
                        name:'Birthday',
                        field:'birth_date',
                        sort:''
                    },
                    {
                        name:'Phone #',
                        field:'personal_phone_number',
                        sort:''
                    },
                    {
                        name:'Industry',
                        field:'industry',
                        sort:''
                    },
                    {
                        name:'Company',
                        field:'company',
                        sort:''
                    },
                    {
                        name:'Position',
                        field:'position',
                        sort:''
                    },
                    {
                        name:'Action',
                        field:'action',
                        sort:''
                    }
                ],
                participant_details:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
                items:[]
            }
        },
        components: {
            Dataset,
            Modal,
            Create,
            Show,
            Loading,
            Rouelette
        },
        async created(){
            await this.getparticipants();
        },
        methods:{
            async getparticipants(){
                await axios.get('/api/raffle/participants', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { participants } = response.data;

                    const data = participants?.map(participant =>{
                        return {
                            ...participant,
                            name:participant.name,
                            birth_date: formatDate(undefined, participant.birth_date, 'date'),
                            event: participant.events.map(event => event.name).join(', ')
                        }
                    });

                    this.data = data;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async getItems(){
                console.log('GET ITEMS');
                await axios.get('/api/raffle/get-items', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { items } = response.data;
                    const colors = ['#45ace9','#dd3832','#fef151'];
                    const formattedData = items.map(item =>{
                        const index = Math.floor(Math.random() * colors.length);
                        console.log(index,'index');
                        return{
                            ...item,
                            value:item.name,
                            weight: 1,
                            color: '#ffffff',
                            bgColor: colors[index],
                        }
                    })
                    this.items = formattedData;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            async playRoulette(participant_id){
                await this.getItems();
                this.isLoading = true;
                const id = document.getElementById('participant-roulette-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                console.log(participant_id);
                this.isLoading = false;
                modal.show();
            }, 

            async viewparticipantDetails(participant_id){
                const id = document.getElementById('participant-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.isLoading = true;
                await axios.get(`/api/participant/${participant_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { participant } = response.data;
                    
                    // const birth_date = formatDate(undefined, participant.date_of_birth, 'date');
                    
                    this.participant_details = {
                            ...participant,
                            name: participant.name,
                          
                    }; 

                }).catch((error) => {
                    console.log(error)
                });
                this.isLoading = false;
                modal.show();
               
            },
        },
    }
</script>

<style scoped>
</style>