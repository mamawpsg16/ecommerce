<template>
    <!-- <ConfettiExplosion v-if="showConfetti" :particleCount="200" :stageWidth="5000" /> -->
    <!-- <Rouelette v-if="items.length" :items="items" @toggleConfetti="toggleConfettiComponent"/> -->
    <!-- <Create @loadUpdatedParticipants="getparticipants"  @toggleConfetti="toggleConfettiComponent"/> -->
    <Show @loadUpdatedparticipants="getparticipants" :participant_details="participant_details"/>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" :opacity="0.4" loader="dots"/>
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <!-- <div class="d-flex justify-content-end mb-2">
                <div id="import">
                    <button type="button" class="btn btn-primary text-end me-2" data-bs-toggle="modal" data-bs-target="#participant-registration-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
           </div> -->
            <Dataset :data="data" :columns="columns">
                <template #body="{ data, index }">
                    <tr>
                        <td>{{ data.event }}</td>
                        <td>{{ data.name }}</td>
                        <td>{{ data.email }}</td>
                        <td>{{ data.age }}</td>
                        <td>{{ data.birth_date }}</td>
                        <td>{{ data.personal_phone_number }}</td>
                        <td>{{ data.industry }}</td>
                        <td>{{ data.company }}</td>
                        <td>{{ data.company_phone_number }}</td>
                        <td>{{ data.position }}</td>
                        <td>{{ data.item }}</td>
                        <td>{{ data.quantity }}</td>
                        <td>{{ data.created_at }}</td>
                        <td>{{ data.updated_at }}</td>
                        <td class="text-center">
                            <!-- <button class="btn btn-sm btn-success me-2" @click="playRoulette(data.id)"><i class="fa-solid fa-gamepad"></i></button> -->
                            <button class="btn btn-sm btn-primary me-2" @click="viewparticipantDetails(data.id)"><i class="fa-solid fa-eye"></i></button>
                        </td>
                    </tr>
                </template>
            </Dataset>
        </div>
    </div>
</template>

<script>
// import ConfettiExplosion from "vue-confetti-explosion";
// import Create from './Create.vue';
// import Rouelette from './Components/WheelOfFortune.vue';
import Show from './Show.vue';
import Modal from '@/components/Modal/modal.vue';
import { formatDate, formatDateSlash } from '@/helpers/Formatter/Date.js';
import {SwalDefault, swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import axios from 'axios';
import Dataset from '@/components/Dataset/Index.vue';
import Loading from 'vue-loading-overlay';
    export default {
        name:'Registration Index',
        emits:['toggleConfetti'],
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
                        name:'Age',
                        field:'age',
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
                        name:'Phone #',
                        field:'company_phone_number',
                        sort:''
                    },
                    {
                        name:'Position',
                        field:'position',
                        sort:''
                    },
                    {
                        name:'Item',
                        field:'item',
                        sort:''
                    },
                    {
                        name:'Quantity',
                        field:'quantity',
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
                participant_details:null,
                auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
                isLoading: false,
                fullPage: false,
                items:[],
                showConfetti:false
            }
        },
        components: {
            Dataset,
            Modal,
            Show,
            Loading,
            // Create,
            // Rouelette,
            // ConfettiExplosion

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
                            created_at: formatDateSlash(participant.created_at),
                            updated_at: formatDateSlash(participant.updated_at),
                            age:this.getAge(participant.birth_date)
                        }
                    });

                    this.data = data;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            getAge(birth_date){
                const birthDate = new Date(birth_date);
                const today = new Date();

                // Calculate the difference in years
                const age = today.getFullYear() - birthDate.getFullYear();

                // Check if the birthday has occurred this year
                const hasBirthdayOccurred = (today.getMonth() > birthDate.getMonth()) || 
                                            (today.getMonth() === birthDate.getMonth() && today.getDate() >= birthDate.getDate());

                // If birthday has not occurred yet this year, subtract 1 from age
                const finalAge = hasBirthdayOccurred ? age : age - 1;

                return finalAge == -1 ? 0 : finalAge;
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
                    const colors = ['#E7292D','#ED6722','#14284D',"#E6C805","#ED6722"];
                    const formattedData = items.map(item =>{
                        const index = Math.floor(Math.random() * colors.length);
                        
                        return{
                            ...item,
                            name:`(${item.quantity}) ${item.name}`,
                            value:item.name,
                            quantity:item.quantity,
                            weight: item.chance_rate,
                            color: '#ffffff',
                            bgColor: colors[index],
                        }
                    })
                    this.items = formattedData;
                }).catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            toggleConfettiComponent(show){
                this.showConfetti = show;
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
                await axios.get(`/api/raffle/participants/${participant_id}`, {
                    headers:{
                        Authorization: this.auth_token
                    }
                }).then((response) => {
                    const { participant } = response.data;
                    const constructedDetails =  {
                        ...participant,
                        birthday: formatDate(undefined, participant.birth_date, 'date'),
                        event:{label:participant.events[0].name, value:participant.events[0].id},
                        event_name:participant.events[0].name,
                        company_details:{
                            industry: participant.industry,
                            company: participant.company,
                            position: participant.position,
                            company_phone_number: participant.company_phone_number,
                            company_address: participant.company_address,
                        },
                    }
                    this.participant_details =  constructedDetails;

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